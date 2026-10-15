<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Member;

class CapitalTransaction extends Model
{
    protected $fillable = [
        'member_id',
        'transaction_date',
        'transaction_type',
        'units',
        'amount',
        'balance_units_after',
        'balance_amount_after',
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
