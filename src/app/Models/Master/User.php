<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\Master\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Accounting\Invoice;
use App\Models\Request\ApprovalStep;
use App\Models\Request\ApprovalHistory;
use App\Models\Request\Attachment;
use App\Models\Request\RingiRequest;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\Master\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'auth_type',
        'department',
        'section',
        'position',
        'manager_id',
        'used',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'auth_type' => 'integer', // 追加
        ];
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'creator_id');
    }

    public function isAdmin()
    {
        return $this->auth_type === 0;
    }

    public function isUser()
    {
        return $this->auth_type === 1;
    }

    public function isGuest()
    {
        return $this->auth_type === 2;
    }
    public function requests()
    {
        return $this->hasMany(RingiRequest::class);
    }

    public function approvalSteps()
    {
        return $this->hasMany(ApprovalStep::class, 'approver_id');
    }

    public function approvalHistories()
    {
        return $this->hasMany(ApprovalHistory::class, 'approved_by');
    }

    public function uploadedAttachments()
    {
        return $this->hasMany(Attachment::class, 'uploaded_by');
    }

    public function manager()
    {
        return $this->belongsTo(
            User::class,
            'manager_id'
        );
    }

    public function subordinates()
    {
        return $this->hasMany(
            User::class,
            'manager_id'
        );
    }
    public function getAuthTypeLabelAttribute()
    {
        return match ($this->auth_type) {
            0 => '管理者',
            1 => '一般',
            2 => 'ゲスト',
            default => '不明',
        };
    }

    public function getDisplayNameAttribute()
    {
        return collect([
            $this->department,
            $this->section,
            $this->position,
        ])->filter()->implode(' / ')
            . '　' . $this->name;
    }
}
