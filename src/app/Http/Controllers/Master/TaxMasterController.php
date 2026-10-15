<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Accounting\TaxType;
use Illuminate\Http\Request;

class TaxMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxes = TaxType::orderBy('id')->paginate(15);
        return view('master.taxes.index', compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'type'   => ['required', 'in:exclusive,inclusive,none'],
            'rate'   => ['required', 'numeric', 'between:0,99.99'],
            'active' => ['required', 'boolean'],
        ]);

        TaxType::create($request->all());

        return redirect()->route('master.taxes.index')->with('success', '税率を追加しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaxType $tax)
    {
        $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'type'   => ['required', 'in:exclusive,inclusive,none'],
            'rate'   => ['required', 'numeric', 'between:0,99.99'],
            'active' => ['required', 'boolean'],
        ]);

        $tax->update($request->all());

        return redirect()->route('master.taxes.index')->with('success', '税率を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaxType $tax)
    {
        $tax->delete();
        return redirect()->route('master.taxes.index')->with('success', '税率を削除しました。');
    }
}
