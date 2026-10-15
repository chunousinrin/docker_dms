<?php

namespace App\Http\Controllers;

use App\Models\StampItem;
use App\Models\StampTransaction;

class StockController extends Controller
{
    /** 在庫一覧（ダッシュボード） */
    public function index()
    {
        $items = StampItem::active()->ordered()->withStock()->get();

        $totalAmount = $items->sum(fn($i) => $i->stock * $i->face_value);

        $unverifiedCount = StampTransaction::unverified()->count();

        return view('stamps.stock.index', compact('items', 'totalAmount', 'unverifiedCount'));
    }
}
