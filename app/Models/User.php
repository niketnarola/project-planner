<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Plan;
use App\Models\PlanCategory;
use App\Models\ProgramUser;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\UserAssignment;
use App\Models\UserSocialLink;
use App\Traits\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasAvatar, HasRoles;

    /* Roles */
    public const ROLE_SUPER_ADMIN = 'Super Admin';
    public const ROLE_PROJECT_MANAGER = 'Project Manager';
    public const ROLE_TEAM_LEADER = 'Team Leader';
    public const ROLE_TECHNICAL_TEAM_LEADER = 'Technical Team Leader';
    public const ROLE_TRAINEE_SOFTWARE_ENGINEER = 'Trainee Software Engineer';

    protected static function getRoles(): Collection
    {
        return collect([
            self::ROLE_TEAM_LEADER,
            self::ROLE_TECHNICAL_TEAM_LEADER,
            self::ROLE_TRAINEE_SOFTWARE_ENGINEER,
        ]);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'avatar', 'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
        'avatar_url',
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    /* Relationships */

    public function social_link(): HasOne
    {
        return $this->hasOne(UserSocialLink::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function plan_categories(): HasMany
    {
        return $this->hasMany(PlanCategory::class);
    }

    public function programUsers(): HasMany
    {
        return $this->hasMany(ProgramUser::class);
    }

    public function userAssignments(): HasMany
    {
        return $this->hasMany(UserAssignment::class);
    }
}
