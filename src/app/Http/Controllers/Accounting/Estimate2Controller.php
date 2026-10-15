<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Accounting\EstimateV2;
use App\Models\Master\User;

class Estimate2Controller extends Controller
{
    public function create()
    {
        $classifications = DB::table('classifications')->orderBy('id', 'ASC')->get();
        $branches = DB::table('branches')->orderBy('id', 'ASC')->get();
        $estimatenum = "13" . time();
        $user = auth()->user();

        return view('estimate2.create', compact('classifications', 'branches', 'estimatenum', 'user'));
    }

    /**
     * PDFプレビュー処理
     */
    public function preview(Request $request)
    {
        // 1. 全データのクレンジング
        $data = $this->prepareData($request);

        // 支店情報の取得
        $branch = DB::table('branches')->where('id', $request->input('branch_id'))->first();
        if ($branch) {
            $data['branch_name'] = $branch->name;
            $data['branch_representative'] = $branch->representative;
            $data['branch_postal_code'] = $branch->postal_code;
            $data['branch_address'] = $branch->address;
            $data['branch_phone'] = $branch->phone;
            $data['branch_fax'] = $branch->fax;
            $data['branch_registration_number'] = 'T4200005007974';

            if (!empty($branch->seal_path)) {
                $base64 = base64_encode($branch->seal_path);
                $data['branch_seal'] = 'data:image/png;base64,' . $base64;
            } else {
                $data['branch_seal'] = null;
            }
        }

        // 文字列項目の ###ZS### を 全角スペースに戻す
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = str_replace('###ZS###', '　', $value);
            }
        }

        // 2. 明細12行を配列として整理（PDFのループ用）
        $items = [];
        for ($i = 1; $i <= 12; $i++) {
            $items[] = [
                'summary'    => str_replace('###ZS###', '　', $request->input("smr$i") ?? ''),
                'quantity'   => $data["qat$i"],
                'unit'       => $request->input("unt$i") ?? '',
                'unit_price' => $data["up$i"],
                'amount'     => $data["amt$i"],
                'remark'     => $request->input("rm$i") ?? '',
            ];
        }

        // 3. PDF生成
        $html = view('estimate2.pdf', compact('data', 'items'))->render();
        $html = str_replace('　', '&emsp;', $html);

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4');

        return $pdf->stream("preview_{$data['estimate_number']}.pdf");
    }

    /**
     * 保存処理 (親子テーブル分割対応)
     */
    public function store(Request $request)
    {
        // 1. データのクレンジング
        $data = $this->prepareData($request);

        // バリデーション
        $request->validate([
            'classification_id' => 'required',
            'branch_id'         => 'required',
            'customer_name'     => 'required',
            'biz_name'          => 'required',
            'estimate_number'   => 'required|unique:estimate_v2s,estimate_number',
        ], [
            'branch_id.required' => '発行者（支店）を選択してください。',
        ]);

        try {
            DB::transaction(function () use ($data) {
                // 2. 親テーブル (estimate_v2s) の保存
                $estimate = EstimateV2::create([
                    'classification_id'   => $data['classification_id'],
                    'user_id'             => $data['user_id'],
                    'user_name'           => $data['user_name'],
                    'staff_display'       => $data['staff_display'],
                    'estimate_number'     => $data['estimate_number'],
                    'created_date'        => $data['created_date'],
                    'cd_display'          => $data['cd_display'],
                    'branch_id'           => $data['branch_id'],
                    'customer_name'       => str_replace('###ZS###', '　', $data['customer_name']),
                    'honorific'           => str_replace('###ZS###', '　', $data['honorific'] ?? ''),
                    'biz_name'            => str_replace('###ZS###', '　', $data['biz_name']),
                    'location'            => str_replace('###ZS###', '　', $data['location'] ?? ''),
                    'works_type'          => str_replace('###ZS###', '　', $data['works_type'] ?? ''),
                    'works_period_start'  => $data['works_period_start'],
                    'works_period_end'    => $data['works_period_end'],
                    'effective_date'      => $data['effective_date'],
                    'tax_rate'            => (float)($data['tax_rate'] ?? 1.1),
                    'remark'              => str_replace('###ZS###', '　', $data['Remark'] ?? ''), // 大文字のRemark

                    // ★ 追加：直接作業代、諸経費1-2、工事原価
                    'total_unit_price'    => $data['subtotal'] ?? 0, // 直接作業代(小計)を代入
                    'idsmr1'              => str_replace('###ZS###', '　', $data['idsmr1'] ?? ''),
                    'idqat1'              => $data['idqat1'] ?? 0,
                    'idunt1'              => str_replace('###ZS###', '　', $data['idunt1'] ?? ''),
                    'idup1'               => $data['idup1'] ?? 0,
                    'idamt1'              => $data['idamt1'] ?? 0,
                    'idsmr2'              => str_replace('###ZS###', '　', $data['idsmr2'] ?? ''),
                    'idqat2'              => $data['idqat2'] ?? 0,
                    'idunt2'              => str_replace('###ZS###', '　', $data['idunt2'] ?? ''),
                    'idup2'               => $data['idup2'] ?? 0,
                    'idamt2'              => $data['idamt2'] ?? 0,
                    'idtotal'             => $data['idtotal'] ?? 0,

                    // ★ 追加：諸経費3、その他経費
                    'otsmr1'              => str_replace('###ZS###', '　', $data['otsmr1'] ?? ''),
                    'otqat1'              => $data['otqat1'] ?? 0,
                    'otunt1'              => str_replace('###ZS###', '　', $data['otunt1'] ?? ''),
                    'otup1'               => $data['otup1'] ?? 0,
                    'otamt1'              => $data['otamt1'] ?? 0,
                    'otsmr2'              => str_replace('###ZS###', '　', $data['otsmr2'] ?? ''),
                    'otqat2'              => $data['otqat2'] ?? 0,
                    'otunt2'              => str_replace('###ZS###', '　', $data['otunt2'] ?? ''),
                    'otup2'               => $data['otup2'] ?? 0,
                    'otamt2'              => $data['otamt2'] ?? 0,
                    'otsmr3'              => str_replace('###ZS###', '　', $data['otsmr3'] ?? ''),
                    'otqat3'              => $data['otqat3'] ?? 0,
                    'otunt3'              => str_replace('###ZS###', '　', $data['otunt3'] ?? ''),
                    'otup3'               => $data['otup3'] ?? 0,
                    'otamt3'              => $data['otamt3'] ?? 0,
                    'otsmr4'              => str_replace('###ZS###', '　', $data['otsmr4'] ?? ''),
                    'otqat4'              => $data['otqat4'] ?? 0,
                    'otunt4'              => str_replace('###ZS###', '　', $data['otunt4'] ?? ''),
                    'otup4'               => $data['otup4'] ?? 0,
                    'otamt4'              => $data['otamt4'] ?? 0,

                    // ★ 追加：最終合計額
                    'ottotal'             => $data['ottotal'] ?? 0,
                ]);

                // 3. 子テーブル (estimate_v2_items) の保存
                for ($i = 1; $i <= 12; $i++) {
                    $summary = str_replace('###ZS###', '　', $data["smr$i"] ?? '');
                    $amount = $data["amt$i"] ?? 0;

                    if (!empty($summary) || $amount != 0) {
                        $estimate->items()->create([
                            'sort_order' => $i,
                            'summary'    => $summary,
                            'quantity'   => $data["qat$i"] ?? 0,
                            'unit'       => str_replace('###ZS###', '　', $data["unt$i"] ?? ''),
                            'unit_price' => $data["up$i"] ?? 0,
                            'amount'     => $amount,
                            'remark'     => str_replace('###ZS###', '　', $data["rm$i"] ?? ''),
                        ]);
                    }
                }
            });

            return redirect()->route('estimates.index')->with('success', '見積書を保存しました。');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', '保存失敗: ' . $e->getMessage());
        }
    }
    /**
     * データのクレンジングと数値化
     */
    private function prepareData(Request $request)
    {
        $all = $request->all();

        // 数値項目のリスト (カンマ除去対象)
        $numericFields = [
            'subtotal',
            'idtotal',
            'ottotal',
            'idamt1',
            'idamt2',
            'otamt1',
            'otamt2',
            'otamt3',
            'otamt4',
            'idup1',
            'idup2',
            'otup1',
            'otup2',
            'otup3',
            'otup4',
            'idqat1',
            'idqat2',
            'otqat1',
            'otqat2',
            'otqat3',
            'otqat4',
            'total_unit_price'
        ];

        for ($i = 1; $i <= 12; $i++) {
            $numericFields[] = "qat$i";
            $numericFields[] = "up$i";
            $numericFields[] = "amt$i";
        }

        foreach ($numericFields as $field) {
            $val = $request->input($field);
            $all[$field] = (is_null($val) || $val === '') ? null : (float)str_replace(',', '', $val);
        }

        // チェックボックスの数値化
        $all['cd_display']    = $request->input('cd_display') == '1' ? 1 : 0;
        $all['staff_display'] = $request->input('staff_display') == '1' ? 1 : 0;
        $all['show_creator']  = $request->input('show_creator') == '1' ? 1 : 0; // PDF表示用
        $all['show_billing_date'] = $request->input('show_billing_date') == '1' ? 1 : 0; // PDF表示用
        $all['tax_rate'] = (float)($request->input('tax_rate') ?? 1.1);

        return $all;
    }
    public function showPdf($id)
    {
        // 1. 親データと明細データを取得
        $estimate = EstimateV2::with('items')->findOrFail($id);
        $data = $estimate->toArray();

        // --- ① 基本項目のマッピング ---
        $data['customer_name'] = $estimate->customer_name;
        $data['biz_name']      = $estimate->biz_name;
        $data['honorific']     = $estimate->honorific ?? '様';
        $data['location']      = $estimate->location;
        $data['works_type']    = $estimate->works_type;
        $data['works_period_start'] = $estimate->works_period_start;
        $data['works_period_end']   = $estimate->works_period_end;
        $data['effective_date']     = $estimate->effective_date;
        $data['estimate_number']    = $estimate->estimate_number;
        $data['created_date']       = $estimate->created_date;
        $data['cd_display']         = $estimate->cd_display;
        $data['staff_display']      = $estimate->staff_display;
        $data['user_name']          = $estimate->user_name; // 担当者名
        $data['Remark']             = $estimate->remark;    // 備考（大文字R）

        // --- ② 金額・税率計算 ---
        $net = (float)$estimate->total_unit_price;
        $data['total_unit_price'] = $net;
        $data['tax_rate']         = (float)($estimate->tax_rate * 100); // 1.1 なら 110 (blade内の判定用)
        $data['subtotal']         = $net;

        // 税込合計（ottotal）
        $data['ottotal'] = floor($net * (float)$estimate->tax_rate);

        // --- ③ テンプレート(pdf.blade)が期待する全キーの初期化 ---
        // これを数値の0で埋めないと number_format や計算でエラーになります

        // 諸経費(idsmr) 2行分
        for ($i = 1; $i <= 2; $i++) {
            $data["idsmr$i"] = "";
            $data["idqat$i"] = null;
            $data["idunt$i"] = "";
            $data["idamt$i"] = 0;
        }
        $data['idtotal'] = $net; // 設計用では小計＝工事原価とする

        // その他経費(otsmr) 4行分 (otamt1〜4は計算に使われるため必須)
        for ($i = 1; $i <= 4; $i++) {
            $data["otsmr$i"] = "";
            $data["otqat$i"] = null;
            $data["otunt$i"] = "";
            $data["otup$i"]  = null;
            $data["otamt$i"] = 0;
        }

        // --- ④ 支店情報の取得 ---
        $branch = \DB::table('branches')->where('id', $estimate->branch_id)->first();
        if ($branch) {
            $data['branch_name'] = $branch->name;
            $data['branch_representative'] = $branch->representative;
            $data['branch_postal_code'] = $branch->postal_code;
            $data['branch_address'] = $branch->address;
            $data['branch_phone'] = $branch->phone;
            $data['branch_fax'] = $branch->fax;
            $data['branch_registration_number'] = 'T4200005007974';
            if (!empty($branch->seal_path)) {
                $data['branch_seal'] = 'data:image/png;base64,' . base64_encode($branch->seal_path);
            } else {
                $data['branch_seal'] = null;
            }
        }

        // --- ⑤ 明細データ (12行) の整形 ---
        $items = [];
        $maxRows = 12; // テンプレートが想定している行数

        foreach ($estimate->items as $item) {
            $items[] = [
                'summary'    => $item->summary,
                'quantity'   => $item->quantity,
                'unit'       => $item->unit,
                'unit_price' => $item->unit_price,
                'amount'     => $item->amount,
                'remark'     => $item->remark,
            ];
        }
        $currentCount = count($items);
        for ($i = $currentCount; $i < $maxRows; $i++) {
            $items[] = [
                'summary'    => '',
                'quantity'   => null,
                'unit'       => '',
                'unit_price' => null,
                'amount'     => null,
                'remark'     => '',
            ];
        }
        // --- ⑥ PDF生成 ---
        $html = view('estimate2.pdf', compact('data', 'items'))->render();
        $html = str_replace('　', '&emsp;', $html); // 全角スペース対策

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('A4');

        return $pdf->stream("見積書_{$estimate->estimate_number}.pdf");
    }

    // Estimate2Controller.php に追加
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $estimate = EstimateV2::findOrFail($id);
            $estimate->items()->delete(); // 明細を削除
            $estimate->delete();          // 親を削除
        });

        return redirect()->route('estimates.index')->with('success', '設計用見積を削除しました');
    }
}
