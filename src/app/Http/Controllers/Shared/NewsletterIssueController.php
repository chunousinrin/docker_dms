<?php

namespace App\Http\Controllers\Shared;

use App\Models\Shared\NewsletterIssue;
use App\Models\Master\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsletterIssueController extends Controller
{
    public function index()
    {
        $issues = NewsletterIssue::withCount('shipments')
            ->orderByDesc('published_date')
            ->paginate(20);

        return view('newsletter-issues.index', compact('issues'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'issue_name' => 'required|string|max:100',
            'published_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        NewsletterIssue::create($data);

        return back()->with('success', '広報誌の号を登録しました。');
    }

    public function update(Request $request, NewsletterIssue $newsletterIssue)
    {
        $data = $request->validate([
            'issue_name' => 'required|string|max:100',
            'published_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $newsletterIssue->update($data);

        return back()->with('success', '広報誌の号を更新しました。');
    }

    public function destroy(NewsletterIssue $newsletterIssue)
    {
        if ($newsletterIssue->shipments()->exists()) {
            return back()->with('error', '発送履歴が紐づいているため削除できません。');
        }

        $newsletterIssue->delete();

        return back()->with('success', '広報誌の号を削除しました。');
    }

    /**
     * 広報送付フラグが立っている在籍組合員全員に、この号の発送履歴を一括作成する。
     * 実際の宛名印刷・郵送作業とは別で、あくまで「発送管理台帳」への一括記帳。
     */
    public function shipToAll(Request $request, NewsletterIssue $newsletterIssue)
    {
        $data = $request->validate([
            'sent_date' => 'required|date',
            'method' => 'required|in:mail,hand,other',
        ]);

        // 既にこの号で発送記録がある組合員は重複作成しないようにする
        $alreadyShippedMemberIds = $newsletterIssue->shipments()->pluck('member_id');

        $members = Member::active()
            ->where('newsletter_delivery', true)
            ->whereNotIn('id', $alreadyShippedMemberIds)
            ->get();

        foreach ($members as $member) {
            $newsletterIssue->shipments()->create([
                'member_id' => $member->id,
                'sent_date' => $data['sent_date'],
                'method' => $data['method'],
                'status' => 'sent',
            ]);
        }

        return back()->with(
            'success',
            "在籍・広報送付対象の組合員 {$members->count()} 名分の発送履歴を一括登録しました。"
        );
    }
}
