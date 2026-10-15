<?php

namespace App\Models\Machinery;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Machinery\{
    MachineryCategory,
    MachineryMasterAttachment,
    MachineryMaintenance,
    MachineryRefuel,
    MachineryUsage
};


class MachineryMaster extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'machine_name',
        'status',
        'ownership',
        'manufacturer',
        'base_machine',
        'standard',
        'introduction_date',
        'return_date',
        'current_hour_meter',
        'manager_name',
        'identification_number',
        'next_maintenance_date',
        'service_factory',
        'remarks',
        'order_no',
    ];

    protected $casts = [
        'introduction_date' => 'date',
        'return_date' => 'date',
        'next_maintenance_date' => 'date',
        'current_hour_meter' => 'decimal:1',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(MachineryCategory::class);
    }

    public function attachments()
    {
        return $this->belongsToMany(
            MachineryAttachment::class,
            'machinery_master_attachments',
            'machinery_master_id',
            'machinery_attachment_id'
        );
    }

    public function maintenances()
    {
        return $this->hasMany(
            MachineryMaintenance::class
        )->orderByDesc('effective_date');
    }

    public function refuels()
    {
        return $this->hasMany(MachineryRefuel::class);
    }

    public function usages()
    {
        return $this->hasMany(
            MachineryUsage::class,
            'machinery_master_id'
        )->orderByDesc('start_date');
    }
    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'active' => '稼働中',
            'maintenance' => '整備中',
            'repair' => '修理中',
            'retired' => '廃車',
            'returned' => '返却',
            default => '',
        };
    }

    public function getOwnershipLabelAttribute()
    {
        return match ($this->ownership) {
            'owned' => '自社',
            'lease' => 'リース',
            'rental' => 'レンタル',
            default => '',
        };
    }

    public function getMaintenanceBadgeClassAttribute()
    {
        $date = $this->next_maintenance_date;
        if (!$date) {
            return 'secondary';
        }
        if ($date->isPast()) {
            return 'danger';
        }
        if ($date->lte(today()->addDays(14))) {
            return 'warning';
        }
        return 'success';
    }

    public function getMaintenanceBadgeTextAttribute()
    {
        if (!$this->next_maintenance_date) {
            return '未設定';
        }
        $today = today();
        if ($this->next_maintenance_date->lt($today)) {
            return
                $this->next_maintenance_date->diffInDays($today)
                . '日超過';
        }
        $days = $today->diffInDays($this->next_maintenance_date);
        if ($days <= 14) {
            return '残り' . $days . '日';
        }
        return '正常';
    }
}
