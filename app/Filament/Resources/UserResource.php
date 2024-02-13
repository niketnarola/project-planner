<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withWhereHas('roles', function ( $query ) {
                $query->whereIn('name', User::getRoles());
            })->where('id', '!=', auth()->id())->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->placeholder('Name')
                    ->maxLength(60)
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->placeholder('Email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->maxLength(90)
                    ->required(),

                Forms\Components\CheckboxList::make('role')
                    ->relationship('roles', 'name', function ($query) {
                        $query->whereIn('name', User::getRoles());
                    })
                    ->columnSpan('full')
                    ->columns(4)
                    ->helperText('Choose only one role.')
                    ->required(),

                Forms\Components\Placeholder::make('Note')
                    ->visible(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                    ->helperText('User will get email notification for the password reset.')
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn (Model $record) =>
                        $record?->roles->each->pluck('name')->first()->name
                    )
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('dS F, Y h:i A'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\UserAssignmentsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
