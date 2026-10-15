<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Master\User;
use App\Models\StampItem;
use App\Models\InventoryAudit;

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

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
