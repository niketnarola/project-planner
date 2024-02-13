<?php

namespace App\Filament\Resources\AssignmentResource\RelationManagers;

use App\Models\User;
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
    protected static ?string $title = 'Users';

    protected static string $relationship = 'userAssignments';

    protected static ?string $recordTitleAttribute = 'user_id';

    protected function getTableQuery(): Builder | Relation
    {
        return parent::getTableQuery()->latest();
    }

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return auth()->user()->hasAnyRole([
            User::ROLE_SUPER_ADMIN, User::ROLE_PROJECT_MANAGER, User::ROLE_TEAM_LEADER,
            User::ROLE_TECHNICAL_TEAM_LEADER,
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->placeholder('User')
                    ->searchable()
                    ->options(
                        User::select(['id', 'name'])->role(User::getRoles())->pluck('name', 'id')
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
                Tables\Columns\TextColumn::make('user.name')
                    ->description(fn (Model $record) =>
                        $record->user->roles->pluck('name')->join(', ')
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
                    ->label('Add User')
                    ->modalHeading('Assign Assignment')
                    ->using(function (HasRelationshipTable $livewire, array $data): Model {
                        $data['assigned_by'] = auth()->id();
                        return $livewire->getRelationship()->create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
