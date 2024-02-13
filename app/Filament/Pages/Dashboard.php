<?php
 
namespace App\Filament\Pages;
 
use App\Filament\Resources;
use Filament\Pages\Dashboard as BasePage;
 
class Dashboard extends BasePage
{
    protected function getFooterWidgets(): array
    {
        return [
            Resources\PlanResource\Widgets\PlanChart::class,
            Resources\TaskResource\Widgets\TodayTask::class,
        ];
    }
}
