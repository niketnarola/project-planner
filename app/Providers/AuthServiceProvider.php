<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models;
use App\Policies;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Models\Assignment::class => Policies\AssignmentPolicy::class,
        Models\Program::class => Policies\ProgramPolicy::class,
        Models\Category::class => Policies\CategoryPolicy::class,
        Models\Tag::class => Policies\TagPolicy::class,
        Models\User::class => Policies\UserPolicy::class,
        Models\Task::class => Policies\TaskPolicy::class,
        Models\Project::class => Policies\ProjectPolicy::class,
        Models\PlanCategory::class => Policies\PlanCategoryPolicy::class,
        Models\Plan::class => Policies\PlanPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
