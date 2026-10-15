<?php

namespace App\Models\Machinery;

use Illuminate\Database\Eloquent\Model;

class MachineryUsage extends Model
{
    protected $fillable = [
        'machinery_master_id',
        'start_date',
        'end_date',
        'work_place',
        'instruction_number',
        'worker_group_in_use',
        'remarks',
        'input_user_id ',
        'input_date',
        'hour_meter_start',
        'hour_meter_end',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'input_date' => 'date',
        'hour_meter_start' => 'decimal:1',
        'hour_meter_end' => 'decimal:1',
    ];

    public function machineryMaster()
    {
        return $this->belongsTo(
            MachineryMaster::class,
            'machinery_master_id'
        );
    }
}
