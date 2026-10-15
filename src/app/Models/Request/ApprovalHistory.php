<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Request\RingiRequest;
use App\Models\Request\ApprovalStep;
use App\Models\Master\User;

class ApprovalHistory extends Model
{
    protected $fillable = [
        'request_id',
        'step_id',
        'action',
        'comment',
        'approved_by',
        'approved_at',
        'file_version_at_approval',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */

    public function request(): BelongsTo
    {
        return $this->belongsTo(RingiRequest::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(ApprovalStep::class, 'step_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
