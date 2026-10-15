<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Accounting\Invoice;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'branch_name',
        'account_type',
        'account_number',
    ];

    // 逆方向の関連付け（請求書）
    public function invoices1()
    {
        return $this->hasMany(Invoice::class, 'bank_id1');
    }

    public function invoices2()
    {
        return $this->hasMany(Invoice::class, 'bank_id2');
    }

    public function invoices3()
    {
        return $this->hasMany(Invoice::class, 'bank_id3');
    }
}
