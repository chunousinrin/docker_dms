<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'order_number',

        'item_name',
        'quantity',
        'unit',
        'unit_price',
        'amount',
        'remarks',
    ];

    protected $casts = [
        'quantity'   => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount'     => 'decimal:2',
    ];

    /**
     * 発注書
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    /**
     * 指定した行番号の明細を取得
     */
    public function item(int $number): ?PurchaseOrderItem
    {
        return $this->items->firstWhere('order_number', $number);
    }
}
