<?php

namespace App\Models\Accounting;

use App\Models\Master\Branch;
use App\Models\Master\User;
use App\Models\Accounting\TaxType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'order_date',
        'order_number',

        'supplier_name',
        'supplier_title',
        'supplier_address',
        'supplier_department',
        'supplier_person',
        'supplier_person_title',

        'branch_id',
        'user_id',
        'tax_type_id',

        'subtotal',
        'tax_amount',
        'total_amount',

        'remarks',
    ];

    protected $casts = [
        'order_date'   => 'date',
        'subtotal'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * 発注明細
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class)
            ->orderBy('order_number');
    }

    /**
     * 発行者
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * 担当者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 税率
     */
    public function taxType(): BelongsTo
    {
        return $this->belongsTo(TaxType::class);
    }

    public function getSummaryItemAttribute()
    {
        $first = $this->items->sortBy('order_number')->first();

        if (!$first) {
            return '';
        }

        return $this->items->count() > 1
            ? "{$first->item_name} 他"
            : $first->item_name;
    }
}
