<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageTags extends ManageRecords
{
    protected static string $resource = TagResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data): Model {
                    $data['user_id'] = auth()->id();
                    return static::getModel()::create($data);
                })
                ->successNotificationTitle('New tag has been created.'),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No tags found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Click on "Add tag" add new';
    }
}
