<?php

namespace App\Models\Accounting;

use App\Models\Master\User;
use Illuminate\Database\Eloquent\Model;

/**
 * 追記専用の操作履歴。更新・削除は不可。
 * DB側でもアプリ用ユーザーには INSERT/SELECT のみ付与することを推奨。
 */
class TransactionAuditLog extends Model
{
    public const UPDATED_AT = null;

    public const LABELS = [
        'created'         => '登録',
        'voided'          => '取消',
        'file_viewed'     => '証憑閲覧',
        'integrity_error' => '整合性エラー',
        'csv_exported'    => 'CSV出力',
    ];

    protected $fillable = [
        'user_id', 'transaction_id', 'transaction_file_id',
        'action', 'before', 'after', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return ['before' => 'array', 'after' => 'array'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new \LogicException('監査ログは変更できません。'));
        static::deleting(fn () => throw new \LogicException('監査ログは削除できません。'));
    }

    public static function record(
        string $action,
        ?Transaction $transaction = null,
        ?TransactionFile $file = null,
        ?array $before = null,
        ?array $after = null,
    ): self {
        return self::create([
            'user_id'             => auth()->id(),
            'transaction_id'      => $transaction?->id ?? $file?->transaction_id,
            'transaction_file_id' => $file?->id,
            'action'              => $action,
            'before'              => $before,
            'after'               => $after,
            'ip_address'          => request()?->ip(),
            'user_agent'          => mb_substr((string) request()?->userAgent(), 0, 255),
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
