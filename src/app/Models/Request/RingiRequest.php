<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;

use App\Models\Master\User;

use App\Models\Request\{
    ApprovalStep,
    ApprovalHistory,
    Attachment,
    RingiType
};

class RingiRequest extends Model
{
    protected $fillable = [
        'title',
        'content',
        'user_id',
        'ringi_type_id',
        'status',
        'current_step',
        'last_file_updated_at',
        'applied_date',
        'document_no',
    ];

    protected $casts = [
        'last_file_updated_at' => 'datetime',
        'applied_date' => 'date',
    ];

    const STATUS_DRAFT = 'draft'; //下書き
    const STATUS_PENDING = 'pending'; //申請中
    const STATUS_APPROVED = 'approved'; //承認済
    const STATUS_RETURNED = 'returned'; //差戻し
    const STATUS_WITHDRAWN = 'withdrawn'; //取下げ

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function steps()
    {
        return $this->hasMany(
            ApprovalStep::class,
            'request_id'
        );
    }

    public function histories()
    {
        return $this->hasMany(
            ApprovalHistory::class,
            'request_id'
        );
    }

    public function attachments()
    {
        return $this->hasMany(
            Attachment::class,
            'request_id'
        );
    }

    public function currentAttachments()
    {
        return $this->hasMany(
            Attachment::class,
            'request_id'
        )->where('is_current', true);
    }
    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', RingiRequest::STATUS_PENDING);
    }

    /*
    |--------------------------------------------------------------------------
    | ヘルパー
    |--------------------------------------------------------------------------
    */

    public function isEditableBy($userId)
    {
        return $this->user_id === $userId
            && in_array($this->status, [
                self::STATUS_DRAFT,
                self::STATUS_RETURNED,
            ]);
    }

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_DRAFT => '下書き',
            self::STATUS_PENDING => '申請中',
            self::STATUS_APPROVED => '承認済み',
            self::STATUS_RETURNED => '差戻し',
            self::STATUS_WITHDRAWN => '取り下げ',
        ][$this->status] ?? '';
    }

    public function scopeDraft($query)
    {
        return $query->where(
            'status',
            self::STATUS_PENDING
        );
    }

    public static function generateDocumentNo()
    {
        $ym = '10' . time();
        return sprintf($ym);
    }

    public function ringiType()
    {
        return $this->belongsTo(
            RingiType::class,
            'ringi_type_id'
        );
    }
}
