<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'New project has been added.';
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($data['title']);
        $record = static::getModel()::create($data);

        return $record;
    }
}
