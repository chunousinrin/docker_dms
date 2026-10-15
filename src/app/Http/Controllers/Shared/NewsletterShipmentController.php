<?php

namespace App\Http\Controllers\Shared;

use App\Models\Master\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class NewsletterShipmentController extends Controller
{
    public function store(Request $request, Member $member)
    {
        $this->authorize('update', $member);

        $data = $request->validate([
            'newsletter_issue_id' => 'nullable|exists:newsletter_issues,id',
            'sent_date' => 'required|date',
            'method' => 'required|in:mail,hand,other',
            'status' => 'required|in:sent,returned,skipped',
            'remarks' => 'nullable|string',
        ]);

        $member->newsletterShipments()->create($data);

        return back()->with('success', '広報誌の発送履歴を登録しました。');
    }

    public function destroy(Member $member, $shipmentId)
    {
        $this->authorize('update', $member);

        $member->newsletterShipments()->findOrFail($shipmentId)->delete();
        return back()->with('success', '発送履歴を削除しました。');
    }
}
