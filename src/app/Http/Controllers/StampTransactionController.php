<?php

namespace App\Http\Controllers;

use App\Models\StampItem;
use App\Models\StampTransaction;
use App\Models\Master\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StampTransactionController extends Controller
{
    /** 入出庫履歴（絞り込み付き） */
    public function index(Request $request)
    {
        $query = StampTransaction::with(['item', 'user'])
            ->when($request->filled('stamp_item_id'), fn($q) => $q->where('stamp_item_id', $request->stamp_item_id))
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('from'), fn($q) => $q->whereDate('transacted_on', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('transacted_on', '<=', $request->to))
            ->orderByDesc('transacted_on')
            ->orderByDesc('id');

        $transactions = $query->paginate(50)->withQueryString();
        $items        = StampItem::ordered()->get();
        // 履歴に登場する記録者のみ（退職・無効化したユーザーも絞り込めるよう全ユーザーではなく履歴ベース）
        $users        = User::whereIn('id', StampTransaction::query()->select('user_id')->distinct())
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('stamps.transactions.index', compact('transactions', 'items', 'users'));
    }

    public function create()
    {
        $items = StampItem::active()->ordered()->withStock()->get();

        return view('stamps.transactions.create', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'stamp_item_id' => ['required', Rule::exists('stamp_items', 'id')->where('is_active', true)],
            'type'          => ['required', Rule::in([StampTransaction::TYPE_IN, StampTransaction::TYPE_OUT])],
            'quantity'      => ['required', 'integer', 'min:1', 'max:1000000'],
            'transacted_on' => ['required', 'date', 'before_or_equal:today'],
            'counterparty'  => ['nullable', 'string', 'max:255'],
            'memo'          => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data) {
            // 同時操作によるマイナス在庫を防ぐため品目行をロック
            $item  = StampItem::lockForUpdate()->findOrFail($data['stamp_item_id']);
            $stock = $item->currentStock();

            if ($data['type'] === StampTransaction::TYPE_OUT && $stock < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "在庫不足です（現在庫: {$stock}）",
                ]);
            }

            StampTransaction::create([
                'stamp_item_id' => $item->id,
                'type'          => $data['type'],
                'quantity'      => $data['type'] === StampTransaction::TYPE_OUT
                    ? -$data['quantity']
                    : $data['quantity'],
                'transacted_on' => $data['transacted_on'],
                'counterparty'  => $data['counterparty'] ?? null,
                'memo'          => $data['memo'] ?? null,
                'user_id'       => auth()->id(),
            ]);
        });

        return redirect()->route('stamps.transactions.index')->with('success', '登録しました');
    }
}
