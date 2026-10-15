<?php

namespace App\Models\WorkerV2;

use Illuminate\Database\Eloquent\Model;


class WorkerGroupHistory extends Model
{
    protected $table = 'worker_group_histories';

    protected $fillable = [
        'worker_id',
        'group_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function worker()
    {
        return $this->belongsTo(WorkerV2::class, 'worker_id');
    }

    public function group()
    {
        return $this->belongsTo(WorkerGroupV2::class, 'group_id');
    }
}
