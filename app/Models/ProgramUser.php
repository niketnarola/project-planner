<?php

namespace App\Models;

use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class ProgramUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id', 'user_id', 'type',
    ];

    public const TYPE_MAINTAINER = 'Maintainer';
    public const TYPE_GUEST = 'Guest';

    protected static function getType(): Collection
    {
        return collect([
            self::TYPE_MAINTAINER,
            self::TYPE_GUEST,
        ]);
    }

    /* Relationships */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
