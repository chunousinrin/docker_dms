<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Classification;
use Illuminate\Http\Request;

class ClassificationMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classifications = Classification::orderBy('id')->paginate(15);
        return view('master.classifications.index', compact('classifications'));
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
            'name' => 'required|max:100',
            'description' => 'nullable|max:255',
        ]);

        Classification::create($request->all());

        return redirect()->route('master.classifications.index')->with('success', '分類を追加しました。');
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
    public function update(Request $request, Classification $classification)
    {
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:255',
        ]);

        $classification->update($request->all());

        return redirect()->route('master.classifications.index')->with('success', '分類を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classification $classification)
    {
        $classification->delete();
        return redirect()->route('master.classifications.index')->with('success', '分類を削除しました。');
    }
}
