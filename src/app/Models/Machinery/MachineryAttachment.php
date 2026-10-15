<?php

namespace App\Models\Machinery;

use Illuminate\Database\Eloquent\Model;
use App\Models\Machinery\MachineryMaster;

class MachineryAttachment extends Model
{
    public function machineryMasters()
    {
        return $this->belongsToMany(
            MachineryMaster::class,
            'machinery_master_attachments'
        );
    }
}
