<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

use App\Models\Project\Project;

class Ratio extends Model
{
    protected $fillable = ['project_id', 'ratio_type', 'ratio'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
