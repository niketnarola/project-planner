<?php

namespace App\Filament\Resources\ProgramResource\RelationManagers;

use App\Filament\Resources\UserResource;
use App\Models\ProgramUser;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramUsersRelationManager extends RelationManager
{
    protected static ?string $modelLabel = 'Users';

    protected static ?string $title = 'Users';

    protected static string $relationship = 'programUsers';

    protected static ?string $recordTitleAttribute = 'type';

    protected function getTableQuery(): Builder | Relation
    {
        return parent::getTableQuery()->oldest('type');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->searchable()
                    ->options(UserResource::getEloquentQuery()->pluck('name', 'id'))
                    ->columnSpan('full')
                    ->required(),
                
                Forms\Components\Select::make('type')
                    ->searchable()
                    ->options(ProgramUser::getType())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->description(fn (Model $record) =>
                        $record->user?->roles->each->pluck('name')->first()->name
                    ),

                Tables\Columns\BadgeColumn::make('type')
                    ->enum([
                        'Maintainer',
                        1 => 'Guest',
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Users'),
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
