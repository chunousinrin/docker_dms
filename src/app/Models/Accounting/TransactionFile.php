<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class TransactionFile extends Model
{
    /** 書類種別の唯一の定義元（フォーム・バリデーションで共用） */
    public const TYPES = ['請求書', '領収書', '注文書', 'その他'];

    protected $table = 'transaction_files';

    protected $fillable = [
        'transaction_id',
        'file_type',
        'file_path',
        'original_filename',
        'file_hash',
        'file_size',
        'mime_type',
    ];

    protected static function booted(): void
    {
        // 証憑の実体に関わる項目は登録後に変更させない（将来 backup_status 等の追加列は更新可）
        static::updating(function (TransactionFile $f) {
            if ($f->isDirty(['transaction_id', 'file_type', 'file_path', 'original_filename', 'file_hash'])) {
                throw new \LogicException('証憑ファイル情報は変更できません。');
            }
        });

        static::deleting(function () {
            throw new \LogicException('証憑ファイルは削除できません。');
        });
    }

    public function transaction()
    {
        // 取消済み(論理削除)の取引の証憑も参照できるように
        return $this->belongsTo(Transaction::class, 'transaction_id')->withTrashed();
    }
}
