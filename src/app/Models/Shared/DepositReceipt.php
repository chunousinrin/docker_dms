<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Member;

class DepositReceipt extends Model
{
    protected $fillable = [
        'member_id',
        'issue_number',
        'issue_date',
        'capital_amount_at_issue',
        'reason',
        'issued_by',
        'remarks',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * 次の発行回数を採番して自動生成するヘルパー
     */
    public static function issueFor(Member $member, array $attributes): self
    {
        $nextNumber = $member->depositReceipts()->max('issue_number') + 1;

        return $member->depositReceipts()->create(array_merge($attributes, [
            'issue_number' => $nextNumber,
        ]));
    }
}
