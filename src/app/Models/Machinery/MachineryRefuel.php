<?php

namespace App\Models\Machinery;

use Illuminate\Database\Eloquent\Model;

class MachineryRefuel extends Model
{

    protected $fillable = [
        'machinery_master_id',
        'refuel_date',
        'fuel_volume',
        'gas_station',
        'fuel_location',
        'remarks',
        'input_user_id ',
        'input_date',
    ];

    protected $casts = [
        'refuel_date' => 'date',
        'input_date' => 'date',
        'fuel_volume' => 'decimal:2',
    ];

    public function machineryMaster()
    {
        return $this->belongsTo(
            MachineryMaster::class,
            'machinery_master_id'
        );
    }
}
