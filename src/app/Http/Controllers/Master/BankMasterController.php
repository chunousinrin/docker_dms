<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Bank;
use Illuminate\Http\Request;

class BankMasterController extends Controller
{
    public function index()
    {
        $banks = Bank::orderBy('id')->paginate(15);
        return view('master.banks.index', compact('banks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|max:255',
            'branch_name' => 'required|max:255',
            'account_type' => 'required|string',
            'account_number' => 'required|max:50',
        ]);

        Bank::create($request->all());

        return redirect()->route('master.banks.index')->with('success', '銀行口座を追加しました。');
    }

    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'bank_name' => 'required|max:255',
            'branch_name' => 'required|max:255',
            'account_type' => 'required|string',
            'account_number' => 'required|max:50',
        ]);

        $bank->update($request->all());

        return redirect()->route('master.banks.index')->with('success', '銀行口座を更新しました。');
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return redirect()->route('master.banks.index')->with('success', '銀行口座を削除しました。');
    }
}
