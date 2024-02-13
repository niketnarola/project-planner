<?php

namespace App\Models;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id', 'name', 'description', 'timeline', 'reference_links',
    ];

    protected $casts = [
        'reference_links' => 'array',
    ];

    protected $attributes = [
        'reference_links' => '[]',
    ];

    /* Relationships */

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

}
