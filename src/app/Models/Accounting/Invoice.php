<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Master\Classification;
use App\Models\Accounting\TaxType;
use App\Models\Accounting\InvoiceItem;
use App\Models\Master\Branch;
use App\Models\Master\User;

/**
 * 請求書モデル
 */
class Invoice extends Model
{
    protected $fillable = [
        'invoice_category_id',
        'creator_id',
        'billing_date',
        'requester_id',
        'invoice_number',
        'project_id',
        'project_name',
        'customer_name',
        'honorific',
        'tax_amount',
        'total_billing',
        'project_location',
        'due_date',
        'payment_date',
        'tax_type_id',
        'remarks',
        'memo',
        'show_creator',
        'show_billing_date',
        'bank_id1',
        'bank_id2',
        'bank_id3',
        'completion_start_date',
        'completion_end_date'
    ];

    /**
     * 明細行とのリレーション
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('order_number');
    }

    /**
     * 税率設定とのリレーション
     */
    public function taxType(): BelongsTo
    {
        return $this->belongsTo(TaxType::class);
    }

    public function classification()
    {
        // 第2引数はDB上の実際のカラム名（invoice_category_id）を指定してください
        return $this->belongsTo(Classification::class, 'invoice_category_id');
    }

    public function branch(): BelongsTo
    {
        // requester_id を使って Branch モデルと紐付け
        return $this->belongsTo(Branch::class, 'requester_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }


    /**
     * 常に15行分のデータを返す（表示用）
     */
    public function getItemsForDisplay()
    {
        $items = $this->items;
        $count = $items->count();

        if ($count < 15) {
            // 足りない分を空のインスタンスで埋める
            for ($i = $count + 1; $i <= 15; $i++) {
                $items->push(new InvoiceItem(['order_number' => $i]));
            }
        }

        return $items;
    }

    /**
     * 税金および合計金額を計算してプロパティにセットする
     * (保存直前にコントローラーから呼び出す想定)
     */
    public function calculateAmounts()
    {
        // 1. 小計を算出 (入力がある明細のみ)
        $subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $taxRate = $this->taxType->rate / 100;
        $taxType = $this->taxType->type;

        if ($taxType === 'exclusive') {
            // 外税：小計 × 税率 (切捨て)
            $this->tax_amount = floor($subtotal * $taxRate);
            $this->total_billing = $subtotal + $this->tax_amount;
        } elseif ($taxType === 'inclusive') {
            // 内税：小計 - (小計 / (1 + 税率)) (切捨て)
            // ※税込合計から税抜額を引いて税額を出すパターン
            $netAmount = ceil($subtotal / (1 + $taxRate));
            $this->tax_amount = $subtotal - $netAmount;
            $this->total_billing = $subtotal;
        } else {
            // 税抜・非課税
            $this->tax_amount = 0;
            $this->total_billing = $subtotal;
        }
    }
}
