<?php

namespace App\Models\WorkerV2;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project\Project;

class WorkerGroupV2 extends Model
{
    protected $table = 'workergroups_v2';

    protected $fillable = ['name',];


    /*
    |--------------------------------------------------------------------------
    | 現在有効な班
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query, $date = null)
    {
        $date = $date ?? today();

        return $query->whereHas(
            'histories',
            function ($q) use ($date) {

                $q->where('start_date', '<=', $date)
                    ->where(function ($q2) use ($date) {

                        $q2->whereNull('end_date')
                            ->orWhere('end_date', '>=', $date);
                    });
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 班所属履歴
    |--------------------------------------------------------------------------
    */
    public function histories()
    {
        return $this->hasMany(
            WorkerGroupHistory::class,
            'group_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 勤怠
    |--------------------------------------------------------------------------
    */
    public function attendances()
    {
        return $this->hasMany(
            AttendanceV2::class,
            'group_id'
        );
    }
    public function activeWorkers($date = null)
    {
        $date = $date ?? today();

        return WorkerV2::whereHas(
            'groupHistories',
            function ($q) use ($date) {

                $q->where('group_id', $this->id)
                    ->where('start_date', '<=', $date)
                    ->where(function ($q2) use ($date) {

                        $q2->whereNull('end_date')
                            ->orWhere('end_date', '>=', $date);
                    });
            }
        );
    }

    public function projects()
    {
        return $this->hasMany(
            Project::class,
            'group_id'
        );
    }
}
