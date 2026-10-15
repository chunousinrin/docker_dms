<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

use App\Models\Project\Project;

class Destination extends Model
{
    protected $fillable = ['project_id', 'destination_type', 'destination'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
