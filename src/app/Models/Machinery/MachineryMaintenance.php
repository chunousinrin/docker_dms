<?php

namespace App\Models\Machinery;

use Illuminate\Database\Eloquent\Model;

class MachineryMaintenance extends Model
{
    protected $fillable = [
        'machinery_master_id',
        'maintenance_details',
        'cost',
        'effective_date',
        'remarks',
        'input_user_id',
        'input_date',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'input_date' => 'date',
    ];

    public function maintenances()
    {
        return $this->hasMany(
            MachineryMaintenance::class
        )->orderByDesc('effective_date');
    }
    public function machineryMaster()
    {
        return $this->belongsTo(
            MachineryMaster::class,
            'machinery_master_id'
        );
    }
    public function refuels()
    {
        return $this->hasMany(
            MachineryRefuel::class,
            'machinery_master_id'
        )->orderByDesc('refuel_date');
    }
}
