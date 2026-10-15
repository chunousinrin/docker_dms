<?php

namespace App\Http\Controllers;

use App\Models\StampItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** 品目マスタ（管理者のみ：routes 側で can:admin を付与） */
class StampItemController extends Controller
{
    public function index()
    {
        $items = StampItem::ordered()->withStock()->get();

        return view('master.stamp_items.index', compact('items'));
    }

    public function create()
    {
        return view('master.stamp_items.create', ['item' => new StampItem(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        StampItem::create($this->validated($request));

        return redirect()->route('master.stamp_items.index')->with('success', '品目を登録しました');
    }

    public function edit(StampItem $item)
    {
        return view('master.stamp_items.edit', compact('item'));
    }

    public function update(Request $request, StampItem $item)
    {
        $data = $this->validated($request, $item);

        // 取引履歴がある品目の額面変更は、過去の金額集計に影響するため禁止
        if ($item->transactions()->exists() && (int) $data['face_value'] !== $item->face_value) {
            return back()->withInput()->withErrors([
                'face_value' => '取引履歴のある品目の額面は変更できません。新しい品目を作成してください。',
            ]);
        }

        $item->update($data);

        return redirect()->route('master.stamp_items.index')->with('success', '品目を更新しました');
    }

    public function destroy(StampItem $item)
    {
        if ($item->transactions()->exists() || $item->auditLines()->exists()) {
            return back()->withErrors([
                'delete' => '履歴のある品目は削除できません。「無効」にしてください。',
            ]);
        }

        $item->delete();

        return redirect()->route('master.stamp_items.index')->with('success', '品目を削除しました');
    }

    private function validated(Request $request, ?StampItem $item = null): array
    {
        $data = $request->validate([
            'category'   => ['required', Rule::in(array_keys(StampItem::CATEGORIES))],
            'name'       => ['required', 'string', 'max:255', Rule::unique('stamp_items', 'name')->ignore($item?->id)],
            'face_value' => ['required', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
