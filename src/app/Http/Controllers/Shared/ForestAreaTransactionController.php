<?php

namespace App\Http\Controllers\Shared;

use App\Models\Master\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ForestAreaTransactionController extends Controller
{
    public function store(Request $request, Member $member)
    {
        $this->authorize('update', $member);

        $data = $request->validate([
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|in:increase,decrease',
            'area_ha' => 'required|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        $latest = $member->forestAreaTransactions()->latest('transaction_date')->latest('id')->first();
        $prevArea = $latest->balance_area_ha_after ?? 0;

        $sign = $data['transaction_type'] === 'increase' ? 1 : -1;
        $data['balance_area_ha_after'] = $prevArea + $sign * $data['area_ha'];

        $member->forestAreaTransactions()->create($data);

        return back()->with('success', '山林面積異動を登録しました。');
    }

    public function destroy(Member $member, $transactionId)
    {
        $this->authorize('update', $member);

        $member->forestAreaTransactions()->findOrFail($transactionId)->delete();
        return back()->with('success', '山林面積異動を削除しました。');
    }
}
