<?php

namespace App\Http\Controllers\Shared;

use App\Models\Master\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CapitalTransactionController extends Controller
{
    public function store(Request $request, Member $member)
    {
        $this->authorize('update', $member);

        $data = $request->validate([
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|in:increase,decrease',
            'units' => 'required|integer|min:1',
            'amount' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        $latest = $member->capitalTransactions()->latest('transaction_date')->latest('id')->first();
        $prevUnits = $latest->balance_units_after ?? 0;
        $prevAmount = $latest->balance_amount_after ?? 0;

        $sign = $data['transaction_type'] === 'increase' ? 1 : -1;
        $data['balance_units_after'] = $prevUnits + $sign * $data['units'];
        $data['balance_amount_after'] = $prevAmount + $sign * $data['amount'];

        $member->capitalTransactions()->create($data);

        return back()->with('success', '出資異動を登録しました。');
    }

    public function destroy(Member $member, $transactionId)
    {
        $this->authorize('update', $member);

        $member->capitalTransactions()->findOrFail($transactionId)->delete();
        return back()->with('success', '出資異動を削除しました。');
    }
}
