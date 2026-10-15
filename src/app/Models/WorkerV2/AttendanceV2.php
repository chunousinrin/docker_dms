<?php

namespace App\Models\WorkerV2;

use Illuminate\Database\Eloquent\Model;


class AttendanceV2 extends Model
{
    protected $table = 'attendances_v2';

    protected $fillable = [
        'worker_id',
        'group_id',
        'site_id',
        'work_date',
        'clock_in',
        'clock_out',
        'comment',
    ];

    protected $casts = [
        'work_date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function worker()
    {
        return $this->belongsTo(WorkerV2::class, 'worker_id');
    }

    public function group()
    {
        return $this->belongsTo(WorkerGroupV2::class, 'group_id');
    }

    // ステータス判定（UIで使う）
    public function isClockedIn()
    {
        return !is_null($this->clock_in);
    }

    public function isClockedOut()
    {
        return !is_null($this->clock_out);
    }
}
