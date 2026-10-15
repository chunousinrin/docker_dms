<?php

namespace App\Http\Controllers\Shared;

use App\Models\Master\Member;
use App\Models\Shared\DepositReceipt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepositReceiptController extends Controller
{
    public function store(Request $request, Member $member)
    {
        $this->authorize('update', $member);

        $data = $request->validate([
            'issue_date' => 'required|date',
            'reason' => 'nullable|string|max:100',
            'issued_by' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        $data['capital_amount_at_issue'] = $member->current_capital['amount'];

        DepositReceipt::issueFor($member, $data);

        return back()->with('success', '出資預り書を発行しました（発行回数：' . $member->depositReceipts()->count() . '回目）。');
    }

    public function destroy(Member $member, $receiptId)
    {
        $this->authorize('update', $member);

        $member->depositReceipts()->findOrFail($receiptId)->delete();
        return back()->with('success', '出資預り書の発行履歴を削除しました。');
    }
}
