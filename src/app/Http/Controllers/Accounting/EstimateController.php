<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Master\User;

use App\Models\Accounting\{
    Estimate,
    EstimateItem,
    TaxType
};

use App\Models\Master\{
    Classification,
    Branch
};

use App\Services\EstimatePdfService;

class EstimateController extends Controller
{
    public function index(Request $request)
    {
        $query = Estimate::query()
            ->with([
                'estimateCategory',
                'creator',
            ]);

        // --- キーワード ---
        $query->when($request->keyword, function ($q, $keyword) {
            $q->where(function ($sub) use ($keyword) {
                $sub->where('estimate_number', 'like', "%{$keyword}%")
                    ->orWhere('customer_name', 'like', "%{$keyword}%")
                    ->orWhere('project_name', 'like', "%{$keyword}%");
            });
        });

        // --- 分類 ---
        $query->when($request->classification_id, function ($q, $id) {
            $q->where(
                'estimate_category_id',
                $id
            );
        });

        // --- 作成者 ---
        $query->when($request->creator_id, function ($q, $id) {
            $q->where(
                'creator_id',
                $id
            );
        });

        // --- 作成日 ---
        $query->when($request->created_from, function ($q, $from) {
            $q->whereDate(
                'created_at',
                '>=',
                $from
            );
        });

        $query->when($request->created_to, function ($q, $to) {
            $q->whereDate(
                'created_at',
                '<=',
                $to
            );
        });

        // --- 実施予定日 ---
        $query->when($request->scheduled_from, function ($q, $from) {
            $q->whereDate(
                'scheduled_date',
                '>=',
                $from
            );
        });

        $query->when($request->scheduled_to, function ($q, $to) {
            $q->whereDate(
                'scheduled_date',
                '<=',
                $to
            );
        });

        // --- 取得 ---
        $estimates = $query
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('estimates.index', [
            'estimates' => $estimates,
            'classifications' => Classification::orderBy('id')->get(),
            'users' => User::query()
                ->where('used', 1)
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function create()
    {
        // --- branches テーブルから全件取得 ---
        $branches = \DB::table('branches')->orderBy('id', 'asc')->get();

        // --- 1. 採番ルール: "11" + タイムスタンプ ---
        $suggestedNumber = '11' . time();

        // --- 2. 税率マスタを取得 ---
        $taxTypes = TaxType::where('active', 1)->get();

        // --- 3. 【重要】事業分類を取得 (invoice_categoriesテーブルがある想定) ---
        $categories = \DB::table('classifications')->get();

        $taxTypes = TaxType::where('active', true)
            ->get();

        return view('estimates.create', compact(
            'taxTypes',
            'categories',
            'suggestedNumber',
            'branches'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'estimate_number' => ['required'],
            'customer_name' => ['required'],
            'items.*.description' => ['nullable', 'max:255'],
            'items.*.quantity' => ['nullable', 'numeric'],
            'items.*.unit_price' => ['nullable', 'numeric'],
        ]);

        DB::transaction(function () use ($request) {
            $taxType = TaxType::findOrFail(
                $request->tax_type_id
            );

            $subtotal = 0;

            foreach ($request->items as $item) {
                if (
                    empty($item['description']) &&
                    empty($item['quantity']) &&
                    empty($item['unit_price'])
                ) {
                    continue;
                }

                $subtotal +=
                    ((float)($item['quantity'] ?? 0)) *
                    ((float)($item['unit_price'] ?? 0));
            }

            // --- 消費税 ---
            $taxAmount = 0;
            $totalEstimate = $subtotal;

            if ($taxType->type === 'exclusive') {
                $taxAmount = floor(
                    $subtotal * ($taxType->rate / 100)
                );

                $totalEstimate += $taxAmount;
            } elseif ($taxType->type === 'inclusive') {
                $taxAmount = floor(
                    $subtotal -
                        ($subtotal / (1 + ($taxType->rate / 100)))
                );
            }

            $estimate = Estimate::create([
                'estimate_category_id' => $request->estimate_category_id,
                'creator_id' => auth()->id(),
                'created_date' => now(),
                'requester_id' => $request->requester_id,
                'estimate_number' => $request->estimate_number,
                'project_id' => $request->project_id,
                'project_name' => $request->project_name,
                'customer_name' => $request->customer_name,
                'honorific' => $request->honorific,
                'tax_amount' => $taxAmount,
                'total_estimate' => $totalEstimate,
                'project_location' => $request->project_location,
                'scheduled_date' => $request->scheduled_date,
                'effective_date' => $request->effective_date,
                'tax_type_id' => $request->tax_type_id,
                'remarks' => $request->remarks,
                'memo' => $request->memo,
                'show_creator' => $request->boolean('show_creator'),
                'show_estimate_date' => $request->boolean('show_estimate_date'),
                'unit_price_estimate' => $request->boolean('unit_price_estimate'),
            ]);

            foreach ($request->items as $item) {
                if (
                    empty($item['description']) &&
                    empty($item['quantity']) &&
                    empty($item['unit_price'])
                ) {
                    continue;
                }

                $estimate->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'unit_price' => $item['unit_price'],
                    'order_number' => $item['order_number'],
                ]);
            }
        });

        return redirect()
            ->route('estimates.index')
            ->with('success', '見積を登録しました');
    }

    public function preview(Request $request)
    {
        return app(EstimatePdfService::class)
            ->previewFromRequest($request);
    }

    public function pdf(Estimate $estimate)
    {
        $estimate->load([
            'items',
            'creator',
            'requester',
            'estimateCategory',
            'taxType',
        ]);

        return app(EstimatePdfService::class)
            ->stream($estimate);
    }

    public function destroy(Estimate $estimate)
    {
        $estimate->items()->delete();

        $estimate->delete();

        return redirect()
            ->route('estimates.index')
            ->with('success', '見積書を削除しました。');
    }
}
