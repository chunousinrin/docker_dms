<?php

namespace App\Models\Accounting;

use App\Models\Master\Customer;
use App\Models\Master\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    /** 電帳法の検索要件に使う項目。登録後は変更不可（訂正は取消→再登録） */
    public const IMMUTABLE = ['transaction_date', 'amount', 'customer_name', 'customer_id'];

    protected $fillable = [
        'user_id',
        'customer_id',
        'transaction_date',
        'amount',
        'customer_name',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount'           => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Transaction $t) {
            if ($t->isDirty(self::IMMUTABLE)) {
                throw new \LogicException('登録済みの取引情報（日付・金額・取引先）は変更できません。取消のうえ再登録してください。');
            }
        });

        static::forceDeleting(function () {
            throw new \LogicException('取引データの物理削除はできません。');
        });
    }

    public function files()
    {
        return $this->hasMany(TransactionFile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(TransactionAuditLog::class);
    }
}
