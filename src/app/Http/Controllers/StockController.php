<?php

namespace App\Http\Controllers;

use App\Models\StampItem;

class StockController extends Controller
{
    /** 在庫一覧（ダッシュボード） */
    public function index()
    {
        $items = StampItem::active()->ordered()->withStock()->get();

        $totalAmount = $items->sum(fn ($i) => $i->stock * $i->face_value);

        return view('stamps.stock.index', compact('items', 'totalAmount'));
    }
}
