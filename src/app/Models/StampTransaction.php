<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Master\User;
use LogicException;

class StampTransaction extends Model
{
    use HasFactory;

    public const TYPE_IN     = 'in';
    public const TYPE_OUT    = 'out';
    public const TYPE_ADJUST = 'adjust';

    public const TYPES = [
        self::TYPE_IN     => '入庫',
        self::TYPE_OUT    => '出庫',
        self::TYPE_ADJUST => '棚卸調整',
    ];

    protected $fillable = [
        'stamp_item_id',
        'type',
        'quantity',
        'transacted_on',
        'counterparty',
        'memo',
        'user_id',
        'reporter_name',
        'ip_address',
        'audit_id',
    ];

    protected $casts = [
        'quantity'      => 'integer',
        'transacted_on' => 'date',
    ];

    /**
     * 金券の履歴は証跡のため更新・削除を禁止する。
     * 訂正は逆方向の取引（adjust 等）を新規追加して行う。
     */
    protected static function booted(): void
    {
        static::updating(function () {
            throw new LogicException('入出庫履歴は編集できません。訂正は新しい履歴を追加してください。');
        });
        static::deleting(function () {
            throw new LogicException('入出庫履歴は削除できません。訂正は新しい履歴を追加してください。');
        });
    }

    public function verification(): HasOne
    {
        return $this->hasOne(StampTransactionVerification::class);
    }

    /** 確認対象は入庫・出庫のみ（棚卸調整は棚卸監査自体が確認記録） */
    public function isVerifiable(): bool
    {
        return in_array($this->type, [self::TYPE_IN, self::TYPE_OUT], true);
    }

    /** 未確認の入庫・出庫 */
    public function scopeUnverified(Builder $q): Builder
    {
        return $q->whereIn('type', [self::TYPE_IN, self::TYPE_OUT])
            ->whereDoesntHave('verification');
    }

    /**
     * 確認できるのは「確認対象で、未確認で、記録者本人ではない」場合のみ。
     * 未ログイン出庫（user_id が NULL）は、誰が確認しても本人確認にならないので可。
     */
    public function canBeVerifiedBy(User $user): bool
    {
        return $this->isVerifiable()
            && ! $this->verification
            && ($this->user_id === null || (int) $this->user_id !== (int) $user->id);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(StampItem::class, 'stamp_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function audit(): BelongsTo
    {
        return $this->belongsTo(InventoryAudit::class, 'audit_id');
    }

    /** 記録者名：ログインユーザー名、未ログイン出庫は自己申告名（ゲスト） */
    public function getRecorderNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name;
        }

        return $this->reporter_name ? "{$this->reporter_name}（ゲスト）" : '-';
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
