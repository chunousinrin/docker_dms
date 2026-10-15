<?php

namespace App\Services;

use App\Models\Accounting\TaxType;

class InvoiceService
{
    /**
     * 請求書の小計・税額・合計を計算する
     *
     * @param array $items [['unit_price' => 1000, 'quantity' => 2], ...]
     * @param int   $taxTypeId tax_typesテーブルのID
     * @return array ['subtotal' => ..., 'tax' => ..., 'total' => ...]
     */
    public function calcInvoiceTotal(array $items, ?int $taxTypeId): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $unitPrice = (float)($item['unit_price'] ?? 0);
            $quantity  = (float)($item['quantity'] ?? ($item['qty'] ?? 0));
            $subtotal += $unitPrice * $quantity;
        }

        $tax = 0;
        $total = $subtotal;

        $taxable_10 = 0;
        $tax_10     = 0;
        $taxable_8  = 0;
        $tax_8      = 0;

        $taxType = TaxType::find($taxTypeId);

        if ($taxType) {
            $rate = $taxType->rate / 100; // 例: 10 → 0.10

            switch ($taxType->type) {
                case 'exclusive': // 外税
                    $tax   = $subtotal * $rate;
                    $total = $subtotal + $tax;
                    break;

                case 'inclusive': // 内税
                    $tax   = $subtotal - ($subtotal / (1 + $rate));
                    $total = $subtotal; // 内税は小計＝合計
                    break;

                case 'none': // 非課税
                    $tax   = 0;
                    $total = $subtotal;
                    break;
            }

            // 税率ごとの対象額をセット
            if ($taxType->rate == 10) {
                $taxable_10 = $subtotal;
                $tax_10     = $tax;
            } elseif ($taxType->rate == 8) {
                $taxable_8 = $subtotal;
                $tax_8     = $tax;
            }
        }

        return [
            'subtotal'   => floor($subtotal),
            'tax'        => floor($tax),
            'total'      => floor($total),
            'taxable_10' => floor($taxable_10),
            'tax_10'     => floor($tax_10),
            'taxable_8'  => floor($taxable_8),
            'tax_8'      => floor($tax_8),
            'tax_total'  => floor($tax_10 + $tax_8),
        ];
    }
}
