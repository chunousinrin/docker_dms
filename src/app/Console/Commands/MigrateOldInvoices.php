<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Accounting\Invoice;
use App\Models\Accounting\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigrateOldInvoices extends Command
{
    protected $signature = 'migrate:old-invoices {file}';
    protected $description = '対照表に基づき、金額計算を含めて旧システムからデータを移行します';

    public function handle()
    {
        $filePath = $this->argument('file');
        if (!file_exists($filePath)) {
            $this->error("ファイルが見つかりません。");
            return;
        }

        $file = fopen($filePath, 'r');
        fgetcsv($file); // ヘッダースキップ

        // 25,000件あるため、請求番号ごとにデータを一時保持して集計後に保存する
        $groupedData = [];
        while (($row = fgetcsv($file)) !== false) {
            $billNumber = $row[6];
            $groupedData[$billNumber][] = $row;
        }
        fclose($file);

        $this->info(count($groupedData) . " 件の請求データを処理開始します...");

        DB::beginTransaction();
        try {
            foreach ($groupedData as $billNumber => $rows) {
                $firstRow = $rows[0];

                // --- 1. 税区分(tax_type_id)の判定ロジック ---
                // 旧データのTax(0.10等)と、現状の「外税10%」を紐付け
                $rawTax = (float)$firstRow[15];
                $taxTypeId = 99; // デフォルト：税抜
                $rate = 0;

                if ($rawTax == 0.10) {
                    $taxTypeId = 1; // 外税10%
                    $rate = 0.10;
                } elseif ($rawTax == 0.08) {
                    $taxTypeId = 2; // 外税8%
                    $rate = 0.08;
                } elseif ($rawTax == 1.10) {
                    $taxTypeId = 3; // 内税10%
                    $rate = 0.10;
                } elseif ($rawTax == 1.08) {
                    $taxTypeId = 4; // 内税8%
                    $rate = 0.08;
                }

                // --- 2. 金額の事前計算 ---
                $subTotal = 0;
                $itemsToCreate = [];
                foreach ($rows as $index => $row) {
                    if (!empty($row[18])) { // Summaryがある場合のみ
                        $qty = (float)($row[19] ?: 0);
                        $price = (int)($row[21] ?: 0);
                        $lineTotal = (int)round($qty * $price);
                        $subTotal += $lineTotal;

                        $itemsToCreate[] = [
                            'description'  => $row[18],
                            'quantity'     => $qty,
                            'unit'         => $row[20] ?: '',
                            'unit_price'   => $price,
                        ];
                    }
                }

                $taxAmount = 0;
                $totalBilling = 0;

                if ($taxTypeId == 1 || $taxTypeId == 2) {
                    // 外税 10% / 8%
                    $taxAmount = (int)floor($subTotal * $rate); // 税抜合計から税額を計算
                    $totalBilling = $subTotal + $taxAmount;     // 税抜合計 ＋ 税額
                } elseif ($taxTypeId == 3 || $taxTypeId == 4) {
                    // 内税 10% / 8%（明細合計 $subTotal はすでに税込価格）
                    $taxAmount = (int)floor($subTotal - ($subTotal / (1 + $rate))); // 税込合計から税額を逆算
                    $totalBilling = $subTotal; // 税込合計がそのまま請求額
                } elseif ($taxTypeId == 99) {
                    // 税抜（税金なし）
                    $taxAmount = 0;
                    $totalBilling = $subTotal;
                }

                // --- 3. 親(Invoices)の作成 ---
                $invoice = Invoice::create([
                    'invoice_category_id'   => $firstRow[1],
                    'creator_id'            => $firstRow[2],
                    'billing_date'          => $this->parseDate($firstRow[4]),
                    'requester_id'          => $firstRow[5],
                    'invoice_number'        => $billNumber,
                    'project_name'          => $firstRow[7],
                    'customer_name'         => $firstRow[9],
                    'honorific'             => $firstRow[10],
                    'project_location'      => $firstRow[11],
                    'completion_start_date' => $this->parseDate($firstRow[12]),
                    'due_date'              => $this->parseDate($firstRow[13]),
                    'payment_date'          => $this->parseDate($firstRow[14]),
                    'tax_type_id'           => $taxTypeId,
                    'tax_amount'            => $taxAmount,    // 計算結果
                    'total_billing'         => $totalBilling, // 計算結果
                    'remarks'               => $firstRow[16],
                    'memo'                  => $firstRow[17],
                    'show_creator'          => $firstRow[22],
                    'show_billing_date'     => $firstRow[23],
                    'completion_end_date'   => $this->parseDate($firstRow[24]),
                    'bank_id1'              => $firstRow[25] ?: null,
                    'bank_id2'              => $firstRow[26] ?: null,
                    'bank_id3'              => $firstRow[27] ?: null,
                    'project_id'            => null,
                ]);

                // --- 4. 子(InvoiceItems)の一括作成 ---
                foreach ($itemsToCreate as $idx => $itemData) {
                    $invoice->items()->create(array_merge($itemData, [
                        'order_number' => $idx + 1
                    ]));
                }
            }

            DB::commit();
            $this->info("移行が完了しました。");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("エラー発生: " . $e->getMessage());
        }
    }

    private function parseDate($date)
    {
        if (empty($date) || in_array($date, ['0000-00-00', '0000/00/00'])) return null;
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
