<?php

namespace App\Filament\Resources\PlanCategoryResource\Pages;

use App\Filament\Resources\PlanCategoryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManagePlanCategories extends ManageRecords
{
    protected static string $resource = PlanCategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data): Model {
                    $data['user_id'] = auth()->id();
                    return static::getModel()::create($data);
                }),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No plan categories found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Click on "Add plan category" add new';
    }
}
