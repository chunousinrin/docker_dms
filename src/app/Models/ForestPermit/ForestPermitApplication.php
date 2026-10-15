<?php

namespace App\Models\ForestPermit;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ForestPermitApplication extends Model
{

    protected $table = 'forest_permit_applications';

    protected $fillable = [
        'management_no',
        'branch_no',
        'facility_name',
        'protection_forest_type',
        'forest_location',
        'parcel_count',
        'applicant_name',
        'contact',
        'permit_area',
        'application_date',
        'permit_date',
        'order_no',
        'permit_start_date',
        'permit_end_date',
        'clear_cut_completion_date',
        'planting_completion_date',
        'submission_date',
        'start_date',
        'completion_date',
        'remarks',
    ];

    protected $casts = [
        'application_date' => 'date',
        'permit_date' => 'date',
        'permit_start_date' => 'date',
        'permit_end_date' => 'date',
        'clear_cut_completion_date' => 'date',
        'planting_completion_date' => 'date',
        'submission_date' => 'date',
        'start_date' => 'date',
        'completion_date' => 'date',
        'parcel_count' => 'decimal:2',
        'permit_area' => 'decimal:5',
    ];

    public function scopeOrdered($query)
    {
        return $query
            ->orderByDesc('management_no')
            ->orderBy('branch_no');
    }

    public function getDisplayNoAttribute(): string
    {
        return $this->branch_no > 0
            ? "{$this->management_no}-{$this->branch_no}"
            : (string) $this->management_no;
    }

    public function scopeSearch($query, array $filters)
    {
        return $query
            ->when($filters['management_no'] ?? null, function ($q, $value) {
                $q->where('management_no', $value);
            })
            ->when($filters['facility_name'] ?? null, function ($q, $value) {
                $q->where('facility_name', 'like', "%{$value}%");
            })
            ->when($filters['applicant_name'] ?? null, function ($q, $value) {
                $q->where('applicant_name', 'like', "%{$value}%");
            });
    }
    public function getRemainingDaysAttribute(): ?int
    {
        if (!$this->permit_end_date) {
            return null;
        }

        return now()->startOfDay()->diffInDays(
            $this->permit_end_date,
            false
        );
    }
    public function getPermitStatusAttribute(): string
    {
        if (!$this->permit_end_date) {
            return '未設定';
        }

        if ($this->remaining_days < 0) {
            return '期限切';
        }

        if ($this->remaining_days <= 30) {
            return '期限間近';
        }

        return '有効';
    }
}
