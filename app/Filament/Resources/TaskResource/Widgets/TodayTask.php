<?php

namespace App\Filament\Resources\TaskResource\Widgets;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use Closure;
use Filament\Forms;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TodayTask extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = "Today's Tasks";

    protected $listeners = [
        'updateTodayTaskEvent' => '$refresh',
    ];

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole([
            User::ROLE_SUPER_ADMIN, User::ROLE_PROJECT_MANAGER, User::ROLE_TEAM_LEADER,
            User::ROLE_TECHNICAL_TEAM_LEADER, User::ROLE_TRAINEE_SOFTWARE_ENGINEER,
        ]);
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No tasks found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Click on "New task" add new';
    }

    protected function getTableQuery(): Builder
    {
        return TaskResource::getEloquentQuery('today')
            ->where('created_at', 'LIKE', '%'. now()->format('Y-m-d') .'%')
            ->oldest('status')
            ->latest();
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Tables\Actions\Action::make('View All')
                ->icon('heroicon-o-external-link')
                ->iconPosition('after')
                ->url(route('filament.resources.tasks.index')),

            Tables\Actions\CreateAction::make()
                ->form(TaskResource::getForm())
                ->slideOver()
                ->successNotificationTitle('Task has been updated.'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make()
                ->form(TaskResource::getForm())
                ->slideOver()
                ->successNotificationTitle('Task has been updated.')
                ->using(function (Model $record, array $data): Model {
                    $data['completed_at'] = $data['status'] ? now() : null;

                    $record->update($data);
                
                    return $record;
                }),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\CheckboxColumn::make('status'),

            Tables\Columns\BadgeColumn::make('priority')
                ->enum(Task::getPriorities())
                ->colors(Task::getColors()),

            Tables\Columns\TextColumn::make('note')
                ->limit(40),

            Tables\Columns\TextColumn::make('project.title')
                ->placeholder('-')
                ->url(function (Model $record) {
                    if ( $record->project ) {
                        return route('filament.resources.projects.edit', ['record' => $record->project]);
                    }
                }, true),
            
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime('dS F, Y h:i A'),
        ];
    }
}
