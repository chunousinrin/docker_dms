<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Member;

class NewsletterShipment extends Model
{
    protected $fillable = [
        'member_id',
        'newsletter_issue_id',
        'sent_date',
        'method',
        'status',
        'remarks',
    ];

    protected $casts = [
        'sent_date' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function issue()
    {
        return $this->belongsTo(NewsletterIssue::class, 'newsletter_issue_id');
    }
}
