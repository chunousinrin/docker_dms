<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Master\User;
use LogicException;

class StampTransactionVerification extends Model
{
    public const STATUS_CONFIRMED   = 'confirmed';
    public const STATUS_DISCREPANCY = 'discrepancy';

    public const STATUSES = [
        self::STATUS_CONFIRMED   => '確認済',
        self::STATUS_DISCREPANCY => '差異あり',
    ];

    protected $fillable = [
        'stamp_transaction_id',
        'verifier_id',
        'status',
        'expected_qty',
        'actual_qty',
        'memo',
        'verified_at',
    ];

    protected $casts = [
        'expected_qty' => 'integer',
        'actual_qty'   => 'integer',
        'verified_at'  => 'datetime',
    ];

    /** 確認記録も証跡のため更新・削除不可 */
    protected static function booted(): void
    {
        static::updating(fn() => throw new LogicException('確認記録は編集できません。'));
        static::deleting(fn() => throw new LogicException('確認記録は削除できません。'));
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(StampTransaction::class, 'stamp_transaction_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }
}
