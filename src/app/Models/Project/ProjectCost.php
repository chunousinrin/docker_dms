<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

use App\Models\Project\Project;

class ProjectCost extends Model
{
    protected $fillable = ['project_id', 'category', 'amount'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
