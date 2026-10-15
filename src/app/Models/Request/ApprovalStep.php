<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;

use App\Models\Master\User;
use App\Models\Request\ApprovalHistory;
use App\Models\Request\RingiRequest;


class ApprovalStep extends Model
{
    protected $fillable = [
        'request_id',
        'approver_id',
        'step_order',
        'status',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */

    public function request()
    {
        return $this->belongsTo(
            RingiRequest::class,
            'request_id'
        );
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function histories()
    {
        return $this->hasMany(ApprovalHistory::class, 'step_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ヘルパー
    |--------------------------------------------------------------------------
    */

    public function isCurrent()
    {
        return $this->step_order === $this->request->current_step;
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }
}
