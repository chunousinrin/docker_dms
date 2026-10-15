<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

use App\Models\Accounting\EstimateV2;

class EstimateV2Item extends Model
{
    protected $table = 'estimate_v2_items';

    protected $guarded = ['id'];

    protected $fillable = [
        'estimate_v2_id',
        'sort_order',
        'summary',
        'quantity',
        'unit',
        'unit_price',
        'amount',
        'remark',
    ];

    // 親テーブルとのリレーション（多対1）
    public function estimateV2()
    {
        return $this->belongsTo(EstimateV2::class);
    }
}
