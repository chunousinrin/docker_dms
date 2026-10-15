<?php

namespace App\Console\Commands;

use App\Models\Accounting\Estimate;
use App\Models\Accounting\EstimateItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportEstimates extends Command
{
    protected $signature =
    'estimates:import {file} {--force}';

    protected $description =
    '旧見積CSVをインポート';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {

            $this->error(
                "ファイルが存在しません: {$file}"
            );

            return self::FAILURE;
        }

        $handle = fopen($file, 'r');

        if ($handle === false) {

            $this->error(
                'CSVを開けません'
            );

            return self::FAILURE;
        }

        // ==========================================
        // ヘッダ取得
        // ==========================================

        $header = fgetcsv($handle);

        if (!$header) {

            $this->error(
                'ヘッダ行が取得できません'
            );

            fclose($handle);

            return self::FAILURE;
        }

        // UTF-8 BOM除去
        $header = array_map(
            fn($v) => trim(
                preg_replace(
                    '/^\xEF\xBB\xBF/',
                    '',
                    $v
                )
            ),
            $header
        );

        $records = collect();

        $lineNo = 1;

        // ==========================================
        // データ読込
        // ==========================================

        while (($row = fgetcsv($handle)) !== false) {

            $lineNo++;

            // 完全空行はスキップ
            if (
                count(
                    array_filter(
                        $row,
                        fn($v) => trim((string)$v) !== ''
                    )
                ) === 0
            ) {
                continue;
            }

            // 列不足はnull埋め
            if (count($row) < count($header)) {

                $this->warn(
                    "行 {$lineNo}: 列不足 "
                        . count($row)
                        . " → "
                        . count($header)
                );

                $row = array_pad(
                    $row,
                    count($header),
                    null
                );
            }

            // 列超過は切り捨て
            if (count($row) > count($header)) {

                $this->warn(
                    "行 {$lineNo}: 列超過 "
                        . count($row)
                        . " → "
                        . count($header)
                );

                $row = array_slice(
                    $row,
                    0,
                    count($header)
                );
            }

            $records->push(
                array_combine(
                    $header,
                    $row
                )
            );
        }

        fclose($handle);

        if ($records->isEmpty()) {

            $this->error(
                'CSVにデータがありません'
            );

            return self::FAILURE;
        }

        // ==========================================
        // 見積番号単位でグループ化
        // ==========================================

        $groups = $records->groupBy(
            'EstimateNumber'
        );

        $this->info(
            '明細件数: ' .
                number_format(
                    $records->count()
                )
        );

        $this->info(
            '見積件数: ' .
                number_format(
                    $groups->count()
                )
        );

        // ==========================================
        // インポート
        // ==========================================

        DB::transaction(function () use ($groups) {

            $bar =
                $this->output
                ->createProgressBar(
                    $groups->count()
                );

            $bar->start();

            foreach (
                $groups as
                $estimateNumber => $rows
            ) {

                $this->importEstimate(
                    $estimateNumber,
                    $rows
                );

                $bar->advance();
            }

            $bar->finish();
        });

        $this->newLine(2);

        $this->info(
            'インポート完了'
        );

        return self::SUCCESS;
    }

    private function importEstimate(
        string $estimateNumber,
        $rows
    ): void {

        $first =
            $rows->first();

        $taxTypeId =
            $this->resolveTaxType(
                $first['Tax']
            );

        $estimate =
            Estimate::firstOrCreate(

                [
                    'estimate_number'
                    => $estimateNumber,
                ],

                [
                    'estimate_category_id'
                    => $first['classicationId'] ?: null,

                    'creator_id'
                    => $first['UserID'],

                    'requester_id'
                    => $first['Branch'] ?: null,

                    'tax_type_id'
                    => $taxTypeId,

                    'created_date'
                    => $this->toDate(
                        $first['CreatedDate']
                    ),

                    'project_name'
                    => $first['EstimateName'],

                    'customer_name'
                    => $first['Customer'],

                    'honorific'
                    => $first['CustomerAdd'],

                    'project_location'
                    => $first['Location'],

                    'scheduled_date'
                    => $this->toDate(
                        $first['ScheduledDate']
                    ),

                    'effective_date'
                    => $this->toDate(
                        $first['EffectiveDate']
                    ),

                    'remarks'
                    => $first['Remark'],

                    'memo'
                    => $first['Memo'],

                    'show_creator'
                    => (bool)$first['StaffDisplay'],

                    'show_estimate_date'
                    => (bool)$first['CDDisplay'],

                    'unit_price_estimate'
                    => (bool)$first['UnitPriceEstimate'],
                ]
            );

        if (
            $estimate->items()->exists()
        ) {
            return;
        }

        $subtotal = 0;

        $order = 1;

        foreach ($rows as $row) {

            $quantity =
                $row['Quantity'] !== ''
                ? (float)$row['Quantity']
                : null;

            $unitPrice =
                $row['UnitPrice'] !== ''
                ? (float)$row['UnitPrice']
                : null;

            EstimateItem::create([

                'estimate_id'
                => $estimate->id,

                'description'
                => $row['Summary'],

                'quantity'
                => $quantity,

                'unit'
                => $row['Unit'],

                'unit_price'
                => $unitPrice,

                'order_number'
                => $order++,
            ]);

            $subtotal +=
                ($quantity ?? 0)
                *
                ($unitPrice ?? 0);
        }

        [
            $taxAmount,
            $total
        ] = $this->calculateTax(
            $subtotal,
            $first['Tax']
        );

        $estimate->update([

            'tax_amount'
            => $taxAmount,

            'total_estimate'
            => $total,
        ]);
    }

    private function resolveTaxType(
        ?string $tax
    ): int {

        return match ((string)$tax) {

            '0.1',
            '0.10'
            => 1,

            '0.08'
            => 2,

            '1.1',
            '1.10'
            => 3,

            '1.08'
            => 4,

            default
            => 1,
        };
    }

    private function calculateTax(
        float $subtotal,
        string $tax
    ): array {

        $taxAmount = 0;

        switch ((string)$tax) {

            case '0.1':
            case '0.10':

                $taxAmount =
                    $subtotal * 0.10;

                $total =
                    floor(
                        $subtotal +
                            $taxAmount
                    );

                break;

            case '0.08':

                $taxAmount =
                    $subtotal * 0.08;

                $total =
                    floor(
                        $subtotal +
                            $taxAmount
                    );

                break;

            case '1.1':
            case '1.10':

                $taxAmount =
                    $subtotal -
                    (
                        $subtotal / 1.10
                    );

                $total =
                    floor($subtotal);

                break;

            case '1.08':

                $taxAmount =
                    $subtotal -
                    (
                        $subtotal / 1.08
                    );

                $total =
                    floor($subtotal);

                break;

            default:

                $total =
                    floor($subtotal);
        }

        return [
            $taxAmount,
            $total,
        ];
    }

    private function toDate(
        ?string $date
    ): ?string {

        return !empty($date)
            ? $date
            : null;
    }
}
