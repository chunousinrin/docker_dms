<?php

namespace App\Http\Controllers;

use App\Models\InventoryAudit;
use App\Models\StampItem;
use App\Models\StampTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** 棚卸監査（管理者のみ：routes 側で can:admin を付与） */
class InventoryAuditController extends Controller
{
    public function index()
    {
        $audits = InventoryAudit::with('auditor')
            ->withCount(['lines as diff_lines_count' => fn ($q) => $q->where('diff', '!=', 0)])
            ->orderByDesc('audited_on')
            ->orderByDesc('id')
            ->paginate(30);

        return view('stamps.audits.index', compact('audits'));
    }

    /** 棚卸入力画面：有効な全品目と現在の帳簿数を表示 */
    public function create()
    {
        $items = StampItem::active()->ordered()->withStock()->get();

        return view('stamps.audits.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'audited_on'            => ['required', 'date', 'before_or_equal:today'],
            'memo'                  => ['nullable', 'string', 'max:255'],
            'lines'                 => ['required', 'array', 'min:1'],
            'lines.*.stamp_item_id' => ['required', 'integer', 'distinct', 'exists:stamp_items,id'],
            'lines.*.counted_qty'   => ['required', 'integer', 'min:0', 'max:1000000'],
            'lines.*.note'          => ['nullable', 'string', 'max:255'],
        ]);

        // 全数監査：有効な品目がすべて含まれていること
        $activeIds    = StampItem::active()->pluck('id')->all();
        $submittedIds = collect($request->lines)->pluck('stamp_item_id')->map(fn ($v) => (int) $v)->all();
        if (array_diff($activeIds, $submittedIds)) {
            throw ValidationException::withMessages([
                'lines' => '有効な全品目の実数を入力してください。',
            ]);
        }

        $audit = DB::transaction(function () use ($request) {
            $audit = InventoryAudit::create([
                'audited_on' => $request->audited_on,
                'auditor_id' => auth()->id(),
                'memo'       => $request->memo,
            ]);

            // デッドロック回避のため品目ID順に処理
            $lines = collect($request->lines)->sortBy('stamp_item_id');

            foreach ($lines as $i => $line) {
                $item = StampItem::lockForUpdate()->findOrFail($line['stamp_item_id']);
                $book = $item->currentStock();
                $diff = (int) $line['counted_qty'] - $book;
                $note = $line['note'] ?? null;

                // 差異がある場合は理由の記載を必須にする
                if ($diff !== 0 && blank($note)) {
                    throw ValidationException::withMessages([
                        "lines.{$i}.note" => "{$item->name}：差異がある場合は理由を入力してください。",
                    ]);
                }

                $audit->lines()->create([
                    'stamp_item_id' => $item->id,
                    'book_qty'      => $book,
                    'counted_qty'   => (int) $line['counted_qty'],
                    'diff'          => $diff,
                    'face_value'    => $item->face_value,
                    'note'          => $note,
                ]);

                if ($diff !== 0) {
                    StampTransaction::create([
                        'stamp_item_id' => $item->id,
                        'type'          => StampTransaction::TYPE_ADJUST,
                        'quantity'      => $diff,
                        'transacted_on' => $audit->audited_on,
                        'memo'          => "棚卸差異調整 (audit #{$audit->id})",
                        'user_id'       => auth()->id(),
                        'audit_id'      => $audit->id,
                    ]);
                }
            }

            return $audit;
        });

        return redirect()->route('stamps.audits.show', $audit)->with('success', '棚卸を記録しました');
    }

    public function show(InventoryAudit $audit)
    {
        $audit->load(['auditor', 'lines.item']);

        $totalDiffAmount = $audit->lines->sum(fn ($l) => $l->diff * $l->face_value);

        return view('stamps.audits.show', compact('audit', 'totalDiffAmount'));
    }
}
