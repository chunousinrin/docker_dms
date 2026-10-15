<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\PurchaseOrder;
use App\Models\Accounting\PurchaseOrderItem;
use App\Models\Master\Branch;
use App\Models\Accounting\TaxType;
use App\Models\Master\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class PurchaseOrderController extends Controller
{
    /**
     * 一覧
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with([
            'branch',
            'user',
            'taxType',
            'items',
        ]);

        $users = User::where('used', 1)
            ->orderBy('id', 'asc')
            ->get();

        // キーワード
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');

            $query->where(function ($q) use ($keyword) {
                $q->where('order_number', 'like', "%{$keyword}%")
                    ->orWhere('supplier_name', 'like', "%{$keyword}%")
                    ->orWhere('supplier_person', 'like', "%{$keyword}%")
                    ->orWhere('remarks', 'like', "%{$keyword}%")
                    ->orWhereHas('items', function ($q2) use ($keyword) {
                        $q2->where('item_name', 'like', "%{$keyword}%");
                    });
            });
        }

        // 作成者
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // 発注日
        if ($request->filled('order_date_from')) {
            $query->whereDate('order_date', '>=', $request->input('order_date_from'));
        }

        if ($request->filled('order_date_to')) {
            $query->whereDate('order_date', '<=', $request->input('order_date_to'));
        }

        $purchaseOrders = $query
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('purchase_orders.index', compact('purchaseOrders', 'users'));
    }

    /**
     * 新規作成
     */
    public function create()
    {
        return view(
            'purchase_orders.create',
            array_merge(
                $this->getFormData(),
                [
                    'tempOrderNumber' =>  '14' . time(),
                ]
            )
        );
    }

    /**
     * 保存
     */
    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        DB::transaction(function () use ($validated, $request) {

            $purchaseOrder = PurchaseOrder::create($validated);

            $this->saveItems($purchaseOrder, $request);
        });

        return redirect()
            ->route('purchase-orders.index')
            ->with('success', '発注書を登録しました。');
    }

    /**
     * 詳細
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'items',
            'branch',
            'user',
            'taxType',
        ]);

        return view(
            'purchase_orders.show',
            compact('purchaseOrder')
        );
    }

    /**
     * 編集
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        return view(
            'purchase_orders.edit',
            array_merge(
                compact('purchaseOrder'),
                $this->getFormData()
            )
        );
    }

    /**
     * 更新
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $this->validateData($request);

        DB::transaction(function () use ($purchaseOrder, $validated, $request) {

            $purchaseOrder->update($validated);

            $purchaseOrder->items()->delete();

            $this->saveItems($purchaseOrder, $request);
        });

        return redirect()
            ->route('purchase-orders.show', $purchaseOrder)
            ->with('success', '発注書を更新しました。');
    }
    /**
     * 削除
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()
            ->route('purchase-orders.index')
            ->with('success', '発注書を削除しました。');
    }

    /**
     * PDF
     */
    public function pdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'items',
            'branch',
            'user',
            'taxType',
        ]);

        $branch = $purchaseOrder->branch;

        $stampBase64 = null;
        if ($branch && $branch->seal_path) {
            $stampBase64 = base64_encode($branch->seal_path);
        }

        return $this->generatePdf(
            $purchaseOrder,
            [
                'stampBase64' => $stampBase64,
            ]
        );
    }

    public function pdfPreview(Request $request)
    {
        $purchaseOrder = new PurchaseOrder();
        $purchaseOrder->fill($request->except('items'));
        $purchaseOrder->setRelation('branch', Branch::find($request->branch_id));
        $purchaseOrder->setRelation('user', User::find($request->user_id));
        $purchaseOrder->setRelation('taxType', TaxType::find($request->tax_type_id));

        $items = collect();

        $branch = $purchaseOrder->branch;

        $stampBase64 = null;
        if ($branch && $branch->seal_path) {
            $stampBase64 = base64_encode($branch->seal_path);
        }

        foreach ($request->input('items', []) as $row) {
            if (blank($row['item_name'] ?? null)) {
                continue;
            }
            $items->push(
                new PurchaseOrderItem($row)
            );
        }

        $purchaseOrder->setRelation('items', $items);
        return $this->generatePdf(
            $purchaseOrder,
            [
                'stampBase64' => $stampBase64,
            ]
        );
    }

    private function generatePdf(PurchaseOrder $purchaseOrder, array $extraData = [])
    {
        $data = array_merge([
            'purchaseOrder' => $purchaseOrder,
        ], $extraData);

        $pdf = Pdf::loadView('purchase_orders.pdf', $data);

        return response(
            $pdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="purchase_order.pdf"',
            ]
        );
    }
    /**
     * フォーム共通データ
     */
    private function getFormData(): array
    {
        return [
            'branches' => Branch::orderBy('id')->get(),
            'users' => User::orderBy('id')->get(),
            'taxTypes' => TaxType::orderBy('id')->get(),
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'order_date' => ['required', 'date'],
            'order_number' => ['required', 'string', 'max:255'],

            'supplier_name' => ['required', 'string', 'max:255'],
            'supplier_title' => ['nullable', 'string', 'max:20'],
            'supplier_address' => ['nullable', 'string', 'max:255'],
            'supplier_department' => ['nullable', 'string', 'max:255'],
            'supplier_person' => ['nullable', 'string', 'max:255'],
            'supplier_person_title' => ['nullable', 'string', 'max:20'],

            'branch_id' => ['required', 'exists:branches,id'],
            'user_id' => ['required', 'exists:users,id'],
            'tax_type_id' => ['required', 'exists:tax_types,id'],

            'subtotal' => ['required', 'numeric'],
            'tax_amount' => ['required', 'numeric'],
            'total_amount' => ['required', 'numeric'],

            'remarks' => ['nullable', 'string'],
        ]);
    }

    private function saveItems(PurchaseOrder $purchaseOrder, Request $request): void
    {
        for ($i = 1; $i <= 15; $i++) {

            $itemName = $request->input("items.$i.item_name");

            // 空行は保存しない
            if (blank($itemName)) {
                continue;
            }

            $purchaseOrder->items()->create([
                'order_number' => $i,

                'item_name' => $itemName,

                'quantity' => $request->input("items.$i.quantity"),

                'unit' => $request->input("items.$i.unit"),

                'unit_price' => $request->input("items.$i.unit_price"),

                'amount' => $request->input("items.$i.amount"),

                'remarks' => $request->input("items.$i.remarks"),
            ]);
        }
    }
}
