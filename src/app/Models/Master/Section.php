<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['district_id', 'name', 'code', 'sort_order'];

    public function district()
    {
        return $this->belongsTo(District::class);
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
