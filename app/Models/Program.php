<?php

namespace App\Models;

use App\Models\ProgramUser;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'category', 'created_by', 'status',
    ];

    protected $casts = [
        'status' => 'bool',
    ];

    protected static function getCategories(): array
    {
        return [
            'Trainees',
        ];
    }

    /* Relationships */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    public function programUsers(): HasMany
    {
        return $this->hasMany(ProgramUser::class);
    }
}
