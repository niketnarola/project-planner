<?php

namespace App\Filament\Resources\PlanResource\Widgets;

use App\Models\Plan;
use App\Models\User;
use Filament\Widgets\PieChartWidget;
use Illuminate\Support\Carbon;

class PlanChart extends PieChartWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?string $maxHeight = '250px';

    public ?string $filter = 'month';

    public ?string $reportHeading;

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole([
            User::ROLE_SUPER_ADMIN, User::ROLE_PROJECT_MANAGER, User::ROLE_TEAM_LEADER,
            User::ROLE_TECHNICAL_TEAM_LEADER,
        ]);
    }

    protected function getFilters(): ?array
    {
        return [
            'month' => 'This Month',
            'last_month' => 'Last Month',
            'year' => 'This Year',
        ];
    }

    protected function getHeading(): string
    {
        return $this->reportHeading;
    }

    protected function getData(): array
    {
        $activeFilter = $this->prepareData();
        
        $this->reportHeading = "Plan Report of $activeFilter";
        $user = auth()->user();
        $plan = $user->withWhereHas('plan_categories.project_plans', function ( $query ) use ( $activeFilter, $user ) {
            $query->whereHas('plan', function ($q) use ( $activeFilter, $user ) {
                $q->where('month', 'LIKE', "%$activeFilter%")
                    ->where('user_id', $user->id);
            });
        })->first();

        $statistics = collect();
        $colors = [];
        if ( $plan ) {
            $plan->plan_categories->each(fn ($category) => $category->sum_project_plans = $category->project_plans->count());
            $colors = $plan->plan_categories->pluck('color')->toArray();
            $statistics = $plan->plan_categories->pluck('sum_project_plans', 'name');
        } else {
            static::$maxHeight = '0px';
        }

        return [
            'datasets' => [
                [
                    'label' => 'Plan Report',
                    'data' => $statistics->values(),
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $statistics->keys(),
        ];
    }

    protected function prepareData(): string
    {
        return match ($this->filter) {
            'year' => date('Y'),
            'last_month' => (new Carbon('first day of last month'))->format('F - Y'),
            default => date('F - Y'),
        };
    }
}
