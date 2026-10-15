<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

use App\Models\Accounting\EstimateItem;
use App\Models\Accounting\TaxType;
use App\Models\Master\Branch;
use App\Models\Master\Classification;
use App\Models\Master\User;


class Estimate extends Model
{
    protected $fillable = [
        'estimate_category_id',
        'creator_id',
        'created_date',
        'requester_id',
        'estimate_number',
        'project_id',
        'project_name',
        'customer_name',
        'honorific',
        'tax_amount',
        'total_estimate',
        'project_location',
        'scheduled_date',
        'effective_date',
        'tax_type_id',
        'remarks',
        'memo',
        'show_creater',
        'show_estimate_date',
        'unit_price_estimate',
    ];

    protected $casts = [
        'created_date' => 'date',
        'scheduled_date' => 'date',
        'effective_date' => 'date',

        'show_creater' => 'boolean',
        'show_estimate_date' => 'boolean',
        'unit_price_estimate' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(EstimateItem::class)
            ->orderBy('order_number');
    }

    public function taxType()
    {
        return $this->belongsTo(TaxType::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'requester_id');
    }

    public function estimateCategory()
    {
        return $this->belongsTo(
            Classification::class,
            'estimate_category_id'
        );
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function requester()
    {
        return $this->belongsTo(
            Branch::class,
            'requester_id'
        );
    }
}
