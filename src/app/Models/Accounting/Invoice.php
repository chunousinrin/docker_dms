<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    use SoftDeletes;

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
        'completion_end_date',
        'estimate_id'
    ];

    /**
     * 明細行とのリレーション
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('order_number');
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);   // 同じ namespace（App\Models\Accounting）
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
    public function calculateAmounts(): void
    {
        // 明細の合計（行ごとの丸めなし）。浮動小数点の微小誤差を除くため小数4桁で丸める
        $subtotal = round(
            (float) $this->items->sum(fn($i) => (float) $i->quantity * (float) $i->unit_price),
            4
        );
        $rate = (float) $this->taxType->rate;   // 小数表記（0.1 = 10%）

        switch ($this->taxType->type) {
            case 'exclusive': // 外税：最終合計で切捨て
                $this->total_billing = (int) floor(round($subtotal * (1 + $rate), 6));
                $this->tax_amount    = (int) floor(round($subtotal * $rate, 6));
                break;

            case 'inclusive': // 内税：入力は税込。最終合計を切捨て、税額は内数
                $total = (int) floor($subtotal);
                $this->total_billing = $total;
                $this->tax_amount    = (int) floor(round($total * $rate / (1 + $rate), 6));
                break;

            default:          // 税抜(none)など
                $this->total_billing = (int) floor($subtotal);
                $this->tax_amount    = 0;
        }
    }
}
