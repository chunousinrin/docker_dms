<?php

namespace App\Models\Machinery;

use Illuminate\Database\Eloquent\Model;
use App\Models\Machinery\MachineryMaster;

class MachineryCategory extends Model
{
    public function machineries()
    {
        return $this->hasMany(MachineryMaster::class, 'category_id');
    }
}
