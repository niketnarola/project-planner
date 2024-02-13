<?php

namespace App\Filament\Resources\PlanResource\Pages;

use App\Filament\Resources\PlanResource;
use App\Filament\Resources\PlanResource\Widgets;
use Closure;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListPlans extends ListRecords
{
    protected static string $resource = PlanResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            Widgets\PlanChart::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No plans found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Click on "Add plan" add new';
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Model $record): ?string {
            $resource = static::getResource();

            foreach (['view'] as $action) {
                if (! $resource::hasPage($action)) {
                    continue;
                }

                if (! $resource::{'can' . ucfirst($action)}($record)) {
                    continue;
                }

                return $resource::getUrl($action, ['record' => $record->slug]);
            }

            return null;
        };
    }
}
