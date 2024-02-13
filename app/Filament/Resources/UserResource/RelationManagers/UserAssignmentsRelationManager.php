<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Assignment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'userAssignments';

    protected static ?string $recordTitleAttribute = 'assignment_id';

    protected function getTableQuery(): Builder | Relation
    {
        return parent::getTableQuery()->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('assignment_id')
                    ->label('Assignment')
                    ->placeholder('Assignment')
                    ->searchable()
                    ->options(
                        Assignment::select(['id', 'title'])->pluck('title', 'id')
                    )
                    ->required(),

                Forms\Components\DatePicker::make('due_at')
                    ->format('Y-m-d')
                    ->placeholder('Due at')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('assignment.title')
                    ->description(fn (Model $record) =>
                        $record->assignment->technologies->pluck('name')->join(', ')
                    ),
                Tables\Columns\TextColumn::make('assignedByUser.name')
                    ->label('Assigned By'),
                Tables\Columns\TextColumn::make('due_at')
                    ->dateTime('dS F, Y'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('dS F, Y h:i A'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (HasRelationshipTable $livewire, array $data): Model {
                        $data['assigned_by'] = auth()->id();
                        return $livewire->getRelationship()->create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->modalHeading(fn (Model $record) =>
                            "View {$record->assignment->title}"
                        ),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
