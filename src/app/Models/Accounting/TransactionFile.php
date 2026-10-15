<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

use App\Models\Accounting\Transaction;

class TransactionFile extends Model
{
    // テーブル名を明示的に指定
    protected $table = 'transaction_files';

    protected $fillable = [
        'transaction_id',
        'file_type',
        'file_path',
        'original_filename',
        'file_hash'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
