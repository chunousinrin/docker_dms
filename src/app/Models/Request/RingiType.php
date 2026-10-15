<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;

use App\Models\Request\RingiRequest;

class RingiType extends Model
{
    protected $fillable = ['name'];

    public function requests()
    {
        return $this->hasMany(RingiRequest::class);
    }
}
