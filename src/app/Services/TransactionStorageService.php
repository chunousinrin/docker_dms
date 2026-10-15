<?php

namespace App\Services;

use App\Models\Accounting\Transaction;
use App\Models\Accounting\TransactionFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

class TransactionStorageService
{
    /**
     * 取引情報と関連ファイルを一括保存する
     */
    public function store(array $transactionData, array $fileItems)
    {
        return DB::transaction(function () use ($transactionData, $fileItems) {
            // 1. 取引（親）の保存
            $transaction = Transaction::create($transactionData);

            // 2. 保存ディレクトリの決定 (例: 2026/04/ID)
            $date = Carbon::parse($transaction->transaction_date);
            $directory = "{$date->format('Y')}/{$date->format('m')}/{$transaction->id}";

            foreach ($fileItems as $item) {
                // $item['file'] は Illuminate\Http\UploadedFile のインスタンスを想定
                $file = $item['file'];
                $type = $item['type']; // '請求書', '納品書' など

                // 3. ファイル名の生成（サニタイズ：OSで禁止されている文字を置換）
                $safeCustomerName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $transaction->customer_name);
                $extension = $file->getClientOriginalExtension();
                $filename = "{$transaction->transaction_date}_{$safeCustomerName}_{$transaction->amount}_{$type}.{$extension}";

                // 4. 指定したディスク（transaction）に保存
                $path = $file->storeAs($directory, $filename, 'transaction');

                // 5. ハッシュ値の計算 (SHA-256)
                // storage_path を使って物理パスを取得してハッシュ化
                $fullPath = storage_path("app/transaction/{$path}");
                $hash = hash_file('sha256', $fullPath);

                // 6. TransactionFile（子）の登録
                $transaction->files()->create([
                    'file_type' => $type,
                    'file_path' => $path,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_hash' => $hash,
                ]);
            }

            return $transaction;
        });
    }
}
