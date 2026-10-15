<?php

namespace App\Services;

use App\Models\Master\Branch;
use App\Models\Accounting\TaxType;
use App\Models\Master\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EstimatePdfService
{
    public function previewFromRequest(Request $request)
    {
        $data = $this->buildFromRequest($request);

        return $this->render($data);
    }

    public function stream($estimate)
    {
        $data = $this->buildFromModel($estimate);

        return $this->render($data);
    }

    // ==================================================
    // Request → PDFデータ
    // ==================================================

    private function buildFromRequest(Request $request): array
    {
        $taxType = TaxType::findOrFail(
            $request->tax_type_id
        );

        $items = collect($request->items)
            ->sortBy('order_number');

        $items = $this->normalizeItems($items);

        [$subtotal, $taxAmount, $total] =
            $this->calculateTotals($items, $taxType);

        $requester = Branch::find(
            $request->requester_id
        );

        $creator = User::find(
            $request->creator_id
        );

        return [
            'estimate' => [
                'estimate_number' => $request->estimate_number,
                'customer_name' => $request->customer_name,
                'honorific' => $request->honorific,
                'project_name' => $request->project_name,
                'project_location' => $request->project_location,
                'scheduled_date' => $request->scheduled_date,
                'effective_date' => $request->effective_date,
                'remarks' => $request->remarks,

                'estimate_category_id' => $request->estimate_category_id,

                'show_estimate_date' => $request->show_estimate_date,
                'show_creator' => $request->show_creator,
                'unit_price_estimate' => $request->unit_price_estimate,

                'created_date' => $request->created_date,

                'tax_type_id' => $request->tax_type_id,
            ],

            'items' => $items,

            'subtotal' => $subtotal,
            'taxAmount' => $taxAmount,
            'total' => $total,

            'taxType' => $taxType,

            'requester' => $requester,
            'creator' => $creator,

            'stampBase64' => $this->getStampBase64($requester),
        ];
    }

    // ==================================================
    // Model → PDFデータ
    // ==================================================

    private function buildFromModel($estimate): array
    {
        $taxType = $estimate->taxType;

        $items = collect($estimate->items)
            ->sortBy('order_number');

        $items = $this->normalizeItems($items);

        [$subtotal, $taxAmount, $total] =
            $this->calculateTotals($items, $taxType);

        return [
            'estimate' => $estimate,

            'items' => $items,

            'subtotal' => $subtotal,
            'taxAmount' => $taxAmount,
            'total' => $total,

            'taxType' => $taxType,

            'requester' => $estimate->requester,
            'creator' => $estimate->creator,

            'stampBase64' => $this->getStampBase64(
                $estimate->requester
            ),
        ];
    }

    // ==================================================
    // PDF生成
    // ==================================================

    private function render(array $data)
    {
        $pdf = Pdf::loadView(
            'estimates.pdf',
            $data
        );

        return response(
            $pdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="estimate.pdf"',
            ]
        );
    }

    // ==================================================
    // 明細15行固定
    // ==================================================

    private function normalizeItems(Collection $items): Collection
    {
        return collect(range(1, 15))
            ->map(function ($rowNumber) use ($items) {

                $item = $items->first(function ($item) use ($rowNumber) {

                    return (int) data_get($item, 'order_number')
                        === $rowNumber;
                });

                return [
                    'order_number' => $rowNumber,

                    'description' =>
                    data_get($item, 'description', ''),

                    'quantity' =>
                    data_get($item, 'quantity', ''),

                    'unit' =>
                    data_get($item, 'unit', ''),

                    'unit_price' =>
                    data_get($item, 'unit_price', ''),
                ];
            });
    }
    // ==================================================
    // 金額計算
    // ==================================================

    private function calculateTotals(
        Collection $items,
        $taxType
    ): array {

        $subtotal = 0;

        foreach ($items as $item) {

            $subtotal +=
                ((float)($item['quantity'] ?? 0))
                * ((float)($item['unit_price'] ?? 0));
        }

        $taxAmount = 0;

        if ($taxType->type === 'exclusive') {

            $taxAmount =
                $subtotal * ($taxType->rate / 100);
        } elseif ($taxType->type === 'inclusive') {

            $taxAmount =
                $subtotal
                - ($subtotal / (1 + ($taxType->rate / 100)));
        }

        $total = floor(
            $subtotal + $taxAmount
        );

        return [
            $subtotal,
            $taxAmount,
            $total,
        ];
    }

    // ==================================================
    // 印影
    // ==================================================

    private function getStampBase64($requester)
    {
        if (!$requester || !$requester->seal_path) {
            return null;
        }

        return base64_encode(
            $requester->seal_path
        );
    }
}
