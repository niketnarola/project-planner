<?php

namespace App\Models;

use App\Models\Technology;
use App\Models\UserAssignment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'created_by', 'level',
    ];

    protected static function getLevels(): array
    {
        return [
            'Basic',
            'Intermediate',
            'Semi-Advanced',
            'Advanced',
        ];
    }

    /* Relationships */

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class, 'assignment_technology', 'assignment_id', 'technology_id');
    }

    public function userAssignments(): HasMany
    {
        return $this->hasMany(UserAssignment::class);
    }
}
