<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'rate', 'active'];

    // キャスト設定：rateを数値として扱いやすくする
    protected $casts = ['rate' => 'decimal:2', 'active' => 'boolean',];

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
