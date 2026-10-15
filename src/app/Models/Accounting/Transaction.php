<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

use App\Models\Master\User;
use App\Models\Accounting\TransactionFile;


class Transaction extends Model
{
    protected $fillable = [
        'user_id', // 追加
        'customer_id',
        'transaction_date',
        'amount',
        'customer_name',
        'note'
    ];

    public function files()
    {
        return $this->hasMany(TransactionFile::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
