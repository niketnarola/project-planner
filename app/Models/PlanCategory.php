<?php

namespace App\Models;

use App\Models\ProjectPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'color',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project_plans(): HasMany
    {
        return $this->hasMany(ProjectPlan::class);
    }
}
