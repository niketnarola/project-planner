<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Plan;
use App\Models\PlanCategory;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id', 'plan_category_id', 'project_id', 'note',
    ];

    /* Relationships */

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function plan_category(): BelongsTo
    {
        return $this->belongsTo(PlanCategory::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
