<?php

namespace App\Models;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'user_id', 'note', 'status', 'completed_at', 'priority',
    ];

    protected $casts = [
        'status' => 'boolean',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
