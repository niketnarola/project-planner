<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ManageTasks extends ManageRecords
{
    protected static string $resource = TaskResource::class;

    public function created($name)
    {
        if (Str::of($name)->contains(['mountedActionData'])) {
            $this->emit('updateTodayTaskEvent');
        }
    }

    public function updated($name)
    {
        if (Str::of($name)->contains(['mountedTableAction'])) {
            $this->emit('updateTodayTaskEvent');
        }
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver()
                ->using(function (array $data): Model {
                    $data['user_id'] = auth()->id();
                    return static::getModel()::create($data);
                })
                ->successNotificationTitle('Task has been created.'),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No tasks found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Click on "New task" add new';
    }
}
