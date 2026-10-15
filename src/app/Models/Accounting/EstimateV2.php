<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

use App\Models\Accounting\EstimateV2Item;

class EstimateV2 extends Model
{
    protected $table = 'estimate_v2s';

    protected $guarded = ['id'];

    protected $fillable = [
        'classification_id',
        'user_id',
        'user_name',
        'staff_display',
        'estimate_number',
        'created_date',
        'cd_display',
        'branch_id',
        'customer_name',
        'honorific',
        'biz_name',
        'location',
        'works_type',
        'works_period_start',
        'works_period_end',
        'effective_date',
        'total_unit_price',
        'tax_rate',
        'note',
    ];

    // 明細テーブルとのリレーション（1対多）
    public function items()
    {
        return $this->hasMany(EstimateV2Item::class, 'estimate_v2_id')->orderBy('sort_order');
    }
}
