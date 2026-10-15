<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Member;

class ForestAreaTransaction extends Model
{
    protected $fillable = [
        'member_id',
        'transaction_date',
        'transaction_type',
        'area_ha',
        'balance_area_ha_after',
        'location',
        'reason',
        'remarks',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
