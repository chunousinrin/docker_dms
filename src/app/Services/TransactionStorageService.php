<?php

namespace App\Services;

use App\Models\Accounting\Transaction;
use App\Models\Accounting\TransactionAuditLog;
use App\Models\Master\Customer;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TransactionStorageService
{
    private const DISK = 'transaction';

    /**
     * 取引情報と証憑ファイルを一括保存する。
     *
     * @param  array $data   customer_name, transaction_date, amount, note, user_id
     * @param  array $items  [['file_type' => '請求書', 'file' => UploadedFile], ...]
     */
    public function store(array $data, array $items): Transaction
    {
        $disk = Storage::disk(self::DISK);
        $written = []; // DBロールバック時に掃除するため、書き込んだパスを控える

        try {
            return DB::transaction(function () use ($data, $items, $disk, &$written) {
                // 取引先の作成もトランザクション内に含める（保存失敗時に取引先だけ残らない）
                $customer = Customer::firstOrCreate(['name' => $data['customer_name']]);

                $date = Carbon::parse($data['transaction_date']); // 区切り文字の揺れ(2026/04/16等)を吸収

                $transaction = Transaction::create([
                    'user_id'          => $data['user_id'],
                    'customer_id'      => $customer->id,
                    'customer_name'    => $customer->name, // 保存時点の名称を保持（マスタ変更対策）
                    'transaction_date' => $date->toDateString(),
                    'amount'           => $data['amount'],
                    'note'             => $data['note'] ?? null,
                ]);

                $directory = $date->format('Y/m') . '/' . $transaction->id;
                $fileLog = [];

                foreach ($items as $item) {
                    /** @var UploadedFile $file */
                    $file = $item['file'];

                    // 保存前のアップロードファイルからハッシュを計算（ディスクのパス構成に依存しない）
                    $hash = hash_file('sha256', $file->getRealPath());

                    // 保存名は ULID。衝突・禁則文字・長さの問題が起きない。
                    // 人が読む名前は original_filename と、ダウンロード時の Content-Disposition で出す。
                    $storedName = Str::ulid() . '.' . $file->extension(); // 拡張子は内容(MIME)から判定
                    $path = $file->storeAs($directory, $storedName, self::DISK);
                    $written[] = $path; // disk の throw=true なので失敗時は例外になる

                    $record = $transaction->files()->create([
                        'file_type'         => $item['file_type'],
                        'file_path'         => $path,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_hash'         => $hash,
                        'file_size'         => $file->getSize(),
                        'mime_type'         => $file->getMimeType(),
                    ]);

                    $fileLog[] = [
                        'id'        => $record->id,
                        'file_type' => $record->file_type,
                        'name'      => $record->original_filename,
                        'sha256'    => $hash,
                    ];
                }

                TransactionAuditLog::record('created', $transaction, null, null, [
                    'transaction_date' => $transaction->transaction_date->toDateString(),
                    'amount'           => $transaction->amount,
                    'customer_name'    => $transaction->customer_name,
                    'files'            => $fileLog,
                ]);

                return $transaction;
            });
        } catch (\Throwable $e) {
            // DBはロールバックされるが、書き込み済みファイルは残るので掃除する
            foreach ($written as $path) {
                $disk->delete($path);
            }
            throw $e;
        }
    }

    /**
     * 取引を「取消」する。物理削除はせず、取引は論理削除、証憑ファイルはそのまま保存する。
     */
    public function voidTransaction(Transaction $transaction, string $reason, int $userId): void
    {
        DB::transaction(function () use ($transaction, $reason, $userId) {
            $before = [
                'transaction_date' => $transaction->transaction_date->toDateString(),
                'amount'           => $transaction->amount,
                'customer_name'    => $transaction->customer_name,
            ];

            $transaction->forceFill(['void_reason' => $reason, 'deleted_by' => $userId])->save();
            $transaction->delete(); // SoftDeletes

            TransactionAuditLog::record('voided', $transaction, null, $before, ['void_reason' => $reason]);
        });
    }
}
