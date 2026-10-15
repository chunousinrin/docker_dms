<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\StampTransaction;
use App\Models\InventoryAuditLine;


class StampItem extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'revenue_stamp' => '収入印紙',
        'postage_stamp' => '郵便切手',
        'letter_pack'   => 'レターパック',
        'other'         => 'その他',
    ];

    protected $fillable = ['category', 'name', 'face_value', 'is_active', 'sort_order'];

    protected $casts = [
        'face_value' => 'integer',
        'is_active'  => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(StampTransaction::class);
    }

    public function auditLines(): HasMany
    {
        return $this->hasMany(InventoryAuditLine::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    /** $item->stock で現在庫が取れる（取引なしは null → accessor で 0 扱い） */
    public function scopeWithStock(Builder $q): Builder
    {
        return $q->withSum('transactions as stock', 'quantity');
    }

    public function getStockAttribute($value): int
    {
        return (int) $value;
    }

    public function getStockAmountAttribute(): int
    {
        return $this->stock * $this->face_value;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    /** 現在庫を DB から直接算出（ロック済みトランザクション内で使用） */
    public function currentStock(): int
    {
        return (int) $this->transactions()->sum('quantity');
    }
}
