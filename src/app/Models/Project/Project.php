<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Accounting\Invoice;

use App\Models\Project\{
    ProjectCost,
    Ratio,
    Destination,
    /*WorkOrder*/
};
use App\Models\WorkerV2\WorkerGroupV2;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        // 既存項目
        'order_type',
        'business_type',
        'owner_name',
        'site_alias',
        'group_id',
        'started_on',
        'completed_on',
        'area_value',
        'volume_value',
        'labor_units',
        'note',
        'order_no',
        'ordered_on',
        'owner_contact',
        'site_manager',
        'staff_name',
        'location_address',
        'location_subcompartment',
        'project_name',
        'work_content',
        'work_volume',
        'machines',
        'work_cost',
        'notes',
        'attachments',
        'fiscal_year',

        // 新規追加項目（案件情報）
        'is_protection_forest',
        'logging_notice_date',
        'operation_history',
        'first_approver',
        'second_approver',
        'subsidy_grant_date',

        // 新規追加項目（作業情報）
        'area_or_volume1',
        'workloaded_title1',
        'area_or_volume2',
        'workloaded_title2',
        'area_or_volume3',
        'workloaded_title3',
        'area_or_volume4',
        'workloaded_title4',
        'unit_price',
        'expected_yield1',
        'expected_yield2',
        'ratio_a',
        'ratio_b',
        'ratio_c',
        'destination',
        'average_sales',
        'area_unit',
        'volume_unit',
        'team_name',
        'personnel'
    ];



    // 案件に紐づく請求書
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // 案件の費用明細（必要なら）
    public function costs()
    {
        return $this->hasMany(ProjectCost::class);
    }

    /*/ 案件の作業指示（必要なら）
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }*/

    public function ratios()
    {
        return $this->hasMany(Ratio::class);
    }

    public function destinations()
    {
        return $this->hasMany(Destination::class);
    }

    public function group()
    {
        return $this->belongsTo(
            WorkerGroupV2::class,
            'group_id'
        );
    }
}
