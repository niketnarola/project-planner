<?php

namespace App\Models;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id', 'user_id', 'due_at', 'ratings', 'assigned_by', 'status',
    ];

    protected $casts = [
        'due_at' => 'timestamp:Y-m-d',
        'status' => 'bool',
    ];

    /* Relationships */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by', 'id');
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }
}
