<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data): Model {
                    $data['user_id'] = auth()->id();
                    return static::getModel()::create($data);
                })
                ->successNotificationTitle('New category has been created.'),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No categories found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Click on "Add category" add new';
    }
}
