<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'github', 'twitter', 'stackoverflow',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
