<?php

namespace App\Models;

use App\Models\Category;
use App\Models\ProjectPlan;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'slug', 'description', 'start_at', 'status', 'completed_at', 'priority', 'category_id',
    ];

    protected $casts = [
        'status' => 'boolean',
        'start_at' => 'date:Y-m-d',
    ];

    public const PRIORITY_HIGHEST = 1;
    public const PRIORITY_HIGH = 2;
    public const PRIORITY_NORMAL = 3;
    public const PRIORITY_LOW = 4;
    public const PRIORITY_LOWEST = 5;

    private const PRIORITY_MAP = [
        self::PRIORITY_HIGHEST => 'Highest',
        self::PRIORITY_HIGH => 'High',
        self::PRIORITY_NORMAL => 'Normal',
        self::PRIORITY_LOW => 'Low',
        self::PRIORITY_LOWEST => 'Lowest',
    ];

    public static function getPriorities(): array
    {
        return static::PRIORITY_MAP;
    }

    public static function getColors(): array
    {
        return [
            'danger' => static fn ($state): bool => ( $state === self::PRIORITY_HIGHEST || $state === self::PRIORITY_HIGH ),
            'warning' => static fn ($state): bool => $state === self::PRIORITY_NORMAL,
            'secondary' => static fn ($state): bool => ( $state === self::PRIORITY_LOWEST || $state === self::PRIORITY_LOW ),
        ];
    }

    /* Relationships */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'project_tag', 'project_id', 'tag_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function project_plans(): HasMany
    {
        return $this->hasMany(ProjectPlan::class);
    }
}
