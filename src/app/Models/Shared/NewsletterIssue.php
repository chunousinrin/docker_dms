<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Member;

class NewsletterIssue extends Model
{
    protected $fillable = ['issue_name', 'published_date', 'remarks'];

    protected $casts = [
        'published_date' => 'date',
    ];

    public function shipments()
    {
        return $this->hasMany(NewsletterShipment::class);
    }

    /**
     * この号を「広報送付」対象の在籍組合員全員に一括発送記録する
     */
    public function shipToAllActiveMembers(string $sentDate, string $method = 'mail'): int
    {
        $members = Member::active()->where('newsletter_delivery', true)->get();

        foreach ($members as $member) {
            $this->shipments()->create([
                'member_id' => $member->id,
                'sent_date' => $sentDate,
                'method' => $method,
                'status' => 'sent',
            ]);
        }

        return $members->count();
    }
}
