<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = ['name', 'code', 'sort_order'];

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('sort_order');
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
