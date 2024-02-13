<?php

namespace App\Policies;

use App\Models\PlanCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlanCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            User::ROLE_SUPER_ADMIN, User::ROLE_PROJECT_MANAGER, User::ROLE_TEAM_LEADER,
            User::ROLE_TECHNICAL_TEAM_LEADER,
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PlanCategory $planCategory): bool
    {
        return $user->hasAnyRole([
            User::ROLE_SUPER_ADMIN, User::ROLE_PROJECT_MANAGER, User::ROLE_TEAM_LEADER,
            User::ROLE_TECHNICAL_TEAM_LEADER,
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            User::ROLE_PROJECT_MANAGER, User::ROLE_TEAM_LEADER, User::ROLE_TECHNICAL_TEAM_LEADER,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PlanCategory $planCategory): bool
    {
        return $user->hasAnyRole([
            User::ROLE_PROJECT_MANAGER, User::ROLE_TEAM_LEADER, User::ROLE_TECHNICAL_TEAM_LEADER,
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PlanCategory $planCategory): bool
    {
        return $user->hasAnyRole([
            User::ROLE_PROJECT_MANAGER, User::ROLE_TEAM_LEADER, User::ROLE_TECHNICAL_TEAM_LEADER,
        ]);
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasAnyRole([
            User::ROLE_PROJECT_MANAGER, User::ROLE_TEAM_LEADER, User::ROLE_TECHNICAL_TEAM_LEADER,
        ]);
    }
}
