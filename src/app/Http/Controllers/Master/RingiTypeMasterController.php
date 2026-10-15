<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Request\RingiType;
use Illuminate\Http\Request;

class RingiTypeMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ringiTypes = RingiType::orderBy('id')->paginate(15);
        return view('master.ringi.index', compact('ringiTypes'));
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
            'name' => 'required|max:255',
        ]);

        RingiType::create($request->all());

        return redirect()->route('master.ringi.index')->with('success', '稟議種類を追加しました。');
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
    public function update(Request $request, RingiType $ringi)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $ringi->update($request->all());

        return redirect()->route('master.ringi.index')->with('success', '稟議種類を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RingiType $ringi)
    {
        $ringi->delete();

        return redirect()->route('master.ringi.index')->with('success', '稟議種類を削除しました。');
    }
}
