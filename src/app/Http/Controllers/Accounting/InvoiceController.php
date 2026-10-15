<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;

use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Master\User;
use App\Models\Accounting\{Invoice, InvoiceItem, TaxType, Estimate};
use App\Models\Master\{Classification, Branch, Bank};
use App\Models\Project\Project;


class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::query()->with(['taxType', 'classification']); // 必要に応じてリレーション追加

        // ① キーワード検索（請求番号・顧客名・事業名）
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('invoice_number', 'like', "%{$keyword}%")
                    ->orWhere('customer_name', 'like', "%{$keyword}%")
                    ->orWhere('project_name', 'like', "%{$keyword}%");
            });
        }

        // ② 作成日（発行日）期間
        if ($request->filled('billing_start')) {
            $query->whereDate('billing_date', '>=', $request->billing_start);
        }
        if ($request->filled('billing_end')) {
            $query->whereDate('billing_date', '<=', $request->billing_end);
        }

        // ③ 入金日 期間
        if ($request->filled('payment_start')) {
            $query->whereDate('payment_date', '>=', $request->payment_start);
        }
        if ($request->filled('payment_end')) {
            $query->whereDate('payment_date', '<=', $request->payment_end);
        }

        // ④ 分類
        if ($request->filled('category_id')) {
            $query->where('invoice_category_id', $request->category_id);
        }

        // ⑤ 作成者
        if ($request->filled('creator_id')) {
            $query->where('creator_id', $request->creator_id);
        }

        // ⑥ 未入金チェックボックスの判定
        if ($request->input('unpaid') === '1') {
            $query->where(function ($q) {
                $q->whereNull('payment_date')
                    ->orWhere('payment_date', '')
                    ->orWhere('payment_date', '0000-00-00');
            });
        }

        $invoices = $query->orderBy('billing_date', 'desc')->paginate(20);

        // 選択肢用のデータ
        $categories = Classification::all();
        $creators = DB::table('users')->get(); // 作成者（ユーザー）

        return view('invoices.index', compact('invoices', 'categories', 'creators'));
    }

    /**
     * 新規作成画面
     */
    public function create()
    {
        // branches テーブルから全件取得
        $branches = \DB::table('branches')->orderBy('id', 'asc')->get();
        // 1. 採番ルール: "12" + タイムスタンプ
        $suggestedNumber = '12' . time();
        // 2. 税率マスタを取得
        $taxTypes = TaxType::where('active', 1)->get();
        // 3. 【重要】事業分類を取得 (invoice_categoriesテーブルがある想定)
        $categories = \DB::table('classifications')->get();
        // 4. 【重要】作成者（ユーザー）を取得
        $creators = User::all();
        // 5. 【追加】銀行口座一覧を取得
        $banks = \DB::table('banks')->orderBy('id', 'asc')->get();

        // 全ての変数を view に渡す
        return view('invoices.create', compact(
            'suggestedNumber',
            'taxTypes',
            'categories',
            'creators',
            'branches',
            'banks'
        ));
    }
    /**
     * 保存処理
     */
    public function store(Request $request)
    {
        // バリデーション（必要に応じて項目を追加してください）
        $validated = $request->validate([
            'invoice_number' => 'required|unique:invoices,invoice_number',
            'tax_type_id' => 'required|exists:tax_types,id',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'nullable|numeric',
            'items.*.unit_price' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request) {
            // 1. 親レコードの作成（一旦、金額は0で作成）
            $invoice = Invoice::create($request->all());

            // 2. 明細の保存（入力がある行のみ）
            $itemsData = $request->input('items', []);
            foreach ($itemsData as $index => $item) {
                // 品名、数量、単価のいずれかが入力されている場合のみ保存
                if (!empty($item['description']) || !empty($item['quantity']) || !empty($item['unit_price'])) {
                    $invoice->items()->create([
                        'description' => $item['description'],
                        'quantity'    => $item['quantity'] ?? 0,
                        'unit'        => $item['unit'],
                        'unit_price'  => $item['unit_price'] ?? 0,
                        'order_number' => $index, // 1からの連番
                    ]);
                }
            }

            // 3. 金額の再計算と更新
            // 先ほどModelに定義した calculateAmounts() を呼び出す
            $invoice->load('taxType', 'items'); // 最新の状態をロード
            $invoice->calculateAmounts();
            $invoice->save();
        });

        return redirect()->route('invoices.index')->with('success', '請求書を作成しました。');
    }
    public function destroy(Invoice $invoice)
    {
        try {
            // 1. 紐付いている明細行を先に削除
            $invoice->items()->delete();

            // 2. 請求書本体を削除
            $invoice->delete();

            return redirect()->route('invoices.index')
                ->with('success', '請求書（' . $invoice->invoice_number . '）を削除しました。');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '削除中にエラーが発生しました。');
        }
    }

    public function preview(Request $request)
    {
        // 1. 基本データのセット
        $invoice = new Invoice($request->all());

        // 2. 支所データ（発行者）を取得
        $branch = Branch::find($request->requester_id);

        // 3. リレーション先の手動セット
        $invoice->taxType = TaxType::find($request->tax_type_id);
        $invoice->classification = Classification::find($request->invoice_category_id);

        // 4. 明細行の作成
        $items = collect();
        foreach ($request->items as $itemData) {
            $hasContent = !empty(array_filter([
                $itemData['description'] ?? null,
                $itemData['quantity'] ?? null,
                $itemData['unit_price'] ?? null,
                $itemData['unit'] ?? null,
            ]));

            if ($hasContent) {
                $items->push(new InvoiceItem($itemData));
            } else {
                $items->push(new InvoiceItem(['order_number' => $itemData['order_number'] ?? null]));
            }
        }

        $invoice->setRelation('items', $items);

        // 5. 金額計算
        $invoice->calculateAmounts();

        // 6. 印影データの取得（BranchモデルのBLOBデータから取得）
        $stampBase64 = null;
        if ($branch && $branch->seal_path) {
            // DBから取得したバイナリデータをBase64エンコード
            $stampBase64 = base64_encode($branch->seal_path);
        }
        $logoPath = storage_path('app/public/jforest.svg');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        // 7. PDF生成
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'displayItems' => $invoice->getItemsForDisplay(),
            'requester' => $branch, // 取得済みの$branchを渡す
            'banks' => [
                Bank::find($request->bank_id1),
                Bank::find($request->bank_id2),
                Bank::find($request->bank_id3),
            ],
            'creator' => User::find($request->creator_id),
            'stampBase64' => $stampBase64,
            'logoBase64' => $logoBase64,
        ]);

        return $pdf->stream('preview.pdf');
    }
    public function downloadPdf(Invoice $invoice)
    {
        // 1. リレーションをロード（order_number順に並べるように強制）
        $invoice->load(['items' => function ($query) {
            $query->orderBy('order_number', 'asc');
        }]);

        $branch = Branch::find($invoice->requester_id);

        // 3. リレーション先の手動セット
        $invoice->taxType = TaxType::find($invoice->tax_type_id);
        $invoice->classification = Classification::find($invoice->invoice_category_id);

        $stampBase64 = null;
        if ($branch && $branch->seal_path) {
            $stampBase64 = base64_encode($branch->seal_path);
        }
        $logoPath = storage_path('app/public/jforest.svg');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        // 2. 重要：getItemsForDisplay() の結果を別の変数（$displayItems）に入れる
        $displayItems = $invoice->getItemsForDisplay();

        // 3. PDF生成
        $pdf = \Pdf::loadView('invoices.pdf', [
            'invoice'      => $invoice,
            'displayItems' => $displayItems, // ★ここを 'items' ではなく 'displayItems' にする
            'requester'    => $branch,
            'banks'        => [
                Bank::find($invoice->bank_id1),
                Bank::find($invoice->bank_id2),
                Bank::find($invoice->bank_id3),
            ],
            'creator'      => User::find($invoice->creator_id),
            'stampBase64'  => $stampBase64,
            'logoBase64' => $logoBase64,
        ]);

        return $pdf->stream("invoice_{$invoice->invoice_number}.pdf");
    }
    public function edit(Invoice $invoice)
    {
        // リレーションをロード
        $invoice->load(['items', 'taxType', 'classification']);

        // 紐付け選択肢用のプロジェクト一覧を取得
        $projects = Project::orderBy('order_no', 'asc')->get();

        return view('invoices.edit', compact('invoice', 'projects'));
    }

    // 2. データの更新処理
    public function update(Request $request, Invoice $invoice)
    {
        // バリデーション（3項目のみ対象にする）
        $validated = $request->validate([
            'project_id'   => 'nullable|exists:projects,id',
            'payment_date' => 'nullable|date_format:Y-m-d',
            'memo'         => 'nullable|string|max:2000',
        ]);

        // 許可された3項目だけを更新して保存
        $invoice->update($validated);

        return redirect()->route('invoices.index')
            ->with('flash_message', "請求番号: {$invoice->invoice_number} の情報を更新しました。");
    }

    public function createFromEstimate(Estimate $estimate)
    {
        $estimate->load('items');
        $suggestedNumber = '12' . time();

        return view('invoices.create', [
            'estimate'        => $estimate,
            'suggestedNumber' => $suggestedNumber,
            'categories'      => Classification::all(),
            'branches'        => Branch::all(),
            'taxTypes'        => TaxType::all(),
            'banks'           => Bank::all(),
        ]);
    }

    // ① フォーム画面の表示
    public function transmittalForm(Request $request)
    {
        $customerName = $request->query('customer', '');
        $honorific = $request->query('honorific', '様');
        $user = Auth::user(); // ログイン中のユーザー情報を取得

        return view('invoices.transmittal', compact('customerName', 'honorific', 'user'));
    }

    // ② PDFの生成・出力（DB保存はしない）
    public function generateTransmittalPdf(Request $request)
    {
        $data = [
            'date'          => $request->input('date'),
            'customer_name' => $request->input('customer_name'),
            'honorific'     => $request->input('honorific', '様'),

            // ★ 修正：届いた値が '1'（チェックあり）なら true、それ以外（'0'など）なら false
            'show_staff'    => $request->input('show_staff') === '1',

            'staff_name'    => Auth::user()->name,
            'title'         => $request->input('title', '書類送付のご案内'),
            'body'          => $request->input('body'),
            'attachments'   => $request->input('attachments', []),
            'greeting'      => $request->input('greeting', ''),
        ];

        $pdf = Pdf::loadView('invoices.transmittal_pdf', $data);
        return $pdf->stream('送付案内書_' . date('Ymd') . '.pdf');
    }
}
