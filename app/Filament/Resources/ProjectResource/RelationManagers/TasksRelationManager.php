<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $recordTitleAttribute = 'note';

    public function getRelationship(): Relation | Builder
    {
        return parent::getRelationship()
            ->whereBelongsTo(auth()->user())
            ->oldest('status')->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(TaskResource::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\CheckboxColumn::make('status'),

                Tables\Columns\BadgeColumn::make('priority')
                    ->enum(Task::getPriorities())
                    ->colors(Task::getColors()),

                Tables\Columns\TextColumn::make('note')
                    ->limit(40),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('dS F, Y h:i A'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->slideOver()
                    ->using(function (array $data): Model {
                        $data['user_id'] = auth()->id();
                        return static::getModel()::create($data);
                    })
                    ->successNotificationTitle('Task has been created.'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalHeading('Edit Task')
                    ->successNotificationTitle('Task has been updated.'),
                Tables\Actions\DeleteAction::make(),
            ]);
    }    
}
