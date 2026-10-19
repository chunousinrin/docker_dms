<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;

use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Accounting\InvoiceRequest;
use App\Models\Master\User;
use App\Models\Accounting\{Invoice, InvoiceItem, TaxType, Estimate};
use App\Models\Master\{Classification, Branch, Bank};
use App\Models\Project\Project;


class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        // 一覧で使うのは作成者名だけ（taxType / classification は未使用だった）
        $query = Invoice::query()->with('creator:id,name');

        // ① キーワード検索（請求番号・顧客名・事業名）
        if ($request->filled('keyword')) {
            // LIKE のワイルドカード（% _ \）を文字として扱う
            $keyword = addcslashes($request->keyword, '%_\\');
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
        if ($request->input('trashed') === '1') {
            $query->onlyTrashed();
        }

        $invoices = $query
            ->orderByDesc('billing_date')
            ->orderByDesc('id')      // 同じ発行日の並びを固定（ページ間で重複・欠落しないように）
            ->paginate(20)
            ->withQueryString();     // ページ送りで検索条件を引き継ぐ

        // 選択肢用：必要な列だけ取得（users の全列＝パスワードハッシュ等を読み込まない）
        $categories = Classification::select('id', 'name')->get();
        $creators = DB::table('users')
            ->where(function ($q) use ($request) {
                $q->where('used', 1);
                if ($request->filled('creator_id')) {
                    $q->orWhere('id', $request->creator_id);
                }
            })
            ->select('id', 'name')
            ->get();

        return view('invoices.index', compact('invoices', 'categories', 'creators'));
    }
    /**
     * 保存処理
     */

    public function store(InvoiceRequest $request)
    {
        $data  = $request->validated();
        $items = $data['items'];
        unset($data['items']);

        DB::transaction(function () use ($data, $items) {
            // creator_id はサーバー側で確定。金額は calculateAmounts() が算出
            $invoice = Invoice::create($data + ['creator_id' => Auth::id()]);

            foreach ($items as $index => $item) {
                if (blank($item['description'] ?? null) && blank($item['quantity'] ?? null) && blank($item['unit_price'] ?? null)) {
                    continue;
                }
                $invoice->items()->create([
                    'description'  => $item['description'] ?? null,
                    'quantity'     => $item['quantity'] ?? 0,
                    'unit'         => $item['unit'] ?? null,
                    'unit_price'   => $item['unit_price'] ?? 0,
                    'order_number' => $item['order_number'] ?? ($index + 1),
                ]);
            }

            $invoice->load('taxType', 'items');
            $invoice->calculateAmounts();
            $invoice->save();
        });

        return redirect()->route('invoices.index')->with('success', '請求書を作成しました。');
    }

    public function destroy(Invoice $invoice)
    {
        try {
            $invoice->delete(); // 論理削除（明細は残す）

            return redirect()->route('invoices.index')
                ->with('success', '請求書（' . $invoice->invoice_number . '）を削除しました。');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->with('error', '削除中にエラーが発生しました。');
        }
    }

    public function restore(Invoice $invoice)   // ルート側で withTrashed() が必要
    {
        $invoice->restore();
        //$invoice->forceFill(['deleted_by' => null])->save();

        return redirect()->route('invoices.index')
            ->with('success', '請求書（' . $invoice->invoice_number . '）を復元しました。');
    }

    public function preview(InvoiceRequest $request)
    {
        $data      = $request->validated();
        $itemsData = $data['items'];
        unset($data['items']);

        // 未保存の請求書（creator_id はサーバー側で確定）
        $invoice = new Invoice($data + ['creator_id' => Auth::id()]);
        $invoice->setRelation('creator', $request->user());   // 保存前なので追加の問い合わせは不要

        // 明細行（未保存）。内容のない行は項番だけ持たせる
        $items = collect($itemsData)->map(function ($row) {
            $hasContent = !empty(array_filter([
                $row['description'] ?? null,
                $row['quantity'] ?? null,
                $row['unit_price'] ?? null,
                $row['unit'] ?? null,
            ]));

            return new InvoiceItem($hasContent ? $row : ['order_number' => $row['order_number'] ?? null]);
        });
        $invoice->setRelation('items', $items->values());

        // 金額計算（税率設定が必要）
        $invoice->load('taxType');
        $invoice->calculateAmounts();

        return $this->buildInvoicePdf($invoice)->stream('preview.pdf');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['items' => fn($query) => $query->orderBy('order_number')]);

        return $this->buildInvoicePdf($invoice)->stream("invoice_{$invoice->invoice_number}.pdf");
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

    /** 請求書作成画面の選択肢（create / createFromEstimate 共通） */
    private function formOptions(): array
    {
        return [
            'suggestedNumber' => '12' . time(),
            'taxTypes'        => TaxType::where('active', 1)->get(),
            'categories'      => DB::table('classifications')->get(),
            'branches'        => DB::table('branches')->orderBy('id')->get(),
            'banks'           => DB::table('banks')->orderBy('id')->get(),
        ];
    }

    public function create()
    {
        return view('invoices.create', $this->formOptions());
    }

    public function createFromEstimate(Estimate $estimate)
    {
        $estimate->load('items');

        return view('invoices.create', $this->formOptions() + [
            'estimate'         => $estimate,
            // 同じ見積から作成済みの請求書（二重請求の注意喚起用。削除済みは含まれない）
            'existingInvoices' => Invoice::where('estimate_id', $estimate->id)->pluck('invoice_number'),
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
        $v = $request->validate([
            'date'                => 'nullable|date_format:Y-m-d',
            'customer_name'       => 'required|string|max:100',
            'honorific'           => 'nullable|string|max:10',
            'show_staff'          => 'nullable|in:0,1',
            'title'               => 'nullable|string|max:100',
            'greeting'            => 'nullable|string|max:50',
            'body'                => 'nullable|string|max:5000',
            'attachments'         => 'nullable|array|max:30',
            'attachments.*.name'  => 'nullable|string|max:100',
            'attachments.*.qty'   => 'nullable|numeric|min:0|max:9999',
            'attachments.*.unit'  => 'nullable|string|max:10',
        ]);

        $data = [
            'date'          => $v['date'] ?? date('Y-m-d'),
            'customer_name' => $v['customer_name'],
            'honorific'     => $v['honorific'] ?? '様',
            'show_staff'    => ($v['show_staff'] ?? '0') === '1',
            'staff_name'    => Auth::user()->name,
            'title'         => $v['title'] ?? '書類送付のご案内',
            'body'          => $v['body'] ?? '',
            'attachments'   => $v['attachments'] ?? [],
            'greeting'      => $v['greeting'] ?? '',
        ];

        return Pdf::loadView('invoices.transmittal_pdf', $data)
            ->stream('送付案内書_' . date('Ymd') . '.pdf');
    }

    /**
     * 請求書PDFを組み立てる（preview / downloadPdf 共通）
     * $invoice は保存済みでも未保存でも可。items リレーションは呼び出し側でセット済みであること。
     */
    private function buildInvoicePdf(Invoice $invoice)
    {
        // 未ロードのものだけ取得（未保存のモデルでも外部キーから引ける）
        $invoice->loadMissing(['branch', 'creator', 'taxType']);

        $branch = $invoice->branch;   // 発行者（requester_id）

        // 印影（BranchのBLOBをBase64化）
        $stampBase64 = ($branch && $branch->seal_path) ? base64_encode($branch->seal_path) : null;

        // ロゴ
        $logoPath   = storage_path('app/public/jforest.svg');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        return Pdf::loadView('invoices.pdf', [
            'invoice'      => $invoice,
            'displayItems' => $invoice->items,     // ビュー側が項番で探し、無い行は空欄にする
            'requester'    => $branch,
            'banks'        => [
                Bank::find($invoice->bank_id1),
                Bank::find($invoice->bank_id2),
                Bank::find($invoice->bank_id3),
            ],
            'creator'      => $invoice->creator,
            'stampBase64'  => $stampBase64,
            'logoBase64'   => $logoBase64,
        ]);
    }
}
