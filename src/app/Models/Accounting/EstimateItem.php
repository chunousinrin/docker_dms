<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

use App\Models\Accounting\Estimate;


class EstimateItem extends Model
{
    protected $fillable = [
        'estimate_id',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'order_number',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function getAmountAttribute()
    {
        return $this->quantity * $this->unit_price;
    }
}
