<?php

namespace App\Http\Controllers\Master;

use App\Models\Master\District;
use App\Models\Master\Member;
use App\Models\Shared\NewsletterIssue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::query()->with(['district', 'section']);

        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('member_number', 'like', "%{$keyword}%")
                    ->orWhere('name1', 'like', "%{$keyword}%")
                    ->orWhere('furigana', 'like', "%{$keyword}%");
            });
        }

        if ($districtId = $request->input('district_id')) {
            $query->where('district_id', $districtId);
        }

        if ($request->input('status') === 'active') {
            $query->whereNull('withdrawn_date');
        } elseif ($request->input('status') === 'withdrawn') {
            $query->whereNotNull('withdrawn_date');
        }

        $members = $query->orderBy('member_number')->paginate(20)->withQueryString();

        $districts = District::ordered()->get();

        return view('members.index', compact('members', 'districts'));
    }

    public function create()
    {
        $this->authorize('create', Member::class);

        $member = new Member();
        $districts = District::ordered()->with('sections')->get();
        return view('members.form', compact('member', 'districts'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Member::class);

        $data = $this->validated($request);
        $data['newsletter_delivery'] = $request->boolean('newsletter_delivery');
        $data['is_soudai'] = $request->boolean('is_soudai');
        $data['last_updated_date'] = now()->toDateString();

        $member = Member::create($data);

        return redirect()->route('members.show', $member)->with('success', '組合員を登録しました。');
    }

    public function show(Member $member)
    {
        $member->load([
            'district',
            'section',
            'capitalTransactions' => fn($q) => $q->orderByDesc('transaction_date'),
            'forestAreaTransactions' => fn($q) => $q->orderByDesc('transaction_date'),
            'depositReceipts' => fn($q) => $q->orderByDesc('issue_number'),
            'newsletterShipments' => fn($q) => $q->orderByDesc('sent_date')->with('issue'),
        ]);

        $newsletterIssues = NewsletterIssue::orderByDesc('published_date')->get();

        return view('members.show', compact('member', 'newsletterIssues'));
    }

    public function edit(Member $member)
    {
        $this->authorize('update', $member);

        $districts = District::ordered()->with('sections')->get();
        return view('members.form', compact('member', 'districts'));
    }

    public function update(Request $request, Member $member)
    {
        $this->authorize('update', $member);

        $data = $this->validated($request, $member->id);
        $data['newsletter_delivery'] = $request->boolean('newsletter_delivery');
        $data['is_soudai'] = $request->boolean('is_soudai');
        $data['last_updated_date'] = now()->toDateString();

        $member->update($data);

        return redirect()->route('members.show', $member)->with('success', '組合員情報を更新しました。');
    }

    public function destroy(Member $member)
    {
        $this->authorize('delete', $member);

        $member->delete();
        return redirect()->route('members.index')->with('success', '組合員を削除しました。');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'member_number' => 'required|string|max:20|unique:members,member_number' . ($ignoreId ? ",{$ignoreId}" : ''),
            'furigana' => 'nullable|string|max:100',
            'name1' => 'required|string|max:100',
            'name2' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:8',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'member_type' => 'nullable|string|max:50',
            'withholding_tax_type' => 'nullable|string|max:50',
            'joined_date' => 'nullable|date',
            'withdrawn_date' => 'nullable|date',
            'district_id' => 'nullable|exists:districts,id',
            'section_id' => 'nullable|exists:sections,id',
            'selection' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
            'honorific' => 'nullable|string|max:10',
            'election_district' => 'nullable|string|max:100',
        ]);
    }
}
