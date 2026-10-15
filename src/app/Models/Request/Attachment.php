<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;

use App\Models\Master\User;
use App\Models\Request\RingiRequest;

class Attachment extends Model
{
    protected $fillable = [
        'request_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'version',
        'is_current',
        'uploaded_by',
        'replaced_at',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'replaced_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */

    public function request()
    {
        return $this->belongsTo(RingiRequest::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
    */

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /*
    |--------------------------------------------------------------------------
    | ヘルパー
    |--------------------------------------------------------------------------
    */

    public function isLatest()
    {
        return $this->is_current;
    }
}
