<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssignmentResource\Pages;
use App\Filament\Resources\AssignmentResource\RelationManagers;
use App\Models\Assignment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-desktop-computer';

    protected static ?string $navigationGroup = 'Programs';

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()->whereHas('userAssignments', function ($query) {
            $query->whereBelongsTo(auth()->user());
        })->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('Title'),

                Forms\Components\Select::make('level')
                    ->required()
                    ->searchable()
                    ->options(Assignment::getLevels())
                    ->placeholder('Select Level'),

                Forms\Components\Select::make('technologies')
                    ->required()
                    ->multiple()
                    ->relationship('technologies', 'name')
                    ->placeholder('Select technologies')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->placeholder('Name')
                            ->maxLength(50)
                            ->autofocus()
                            ->required(),
                    ])
                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                        return $action
                            ->modalHeading('Create technology')
                            ->modalButton('Create technology');
                    }),

                Forms\Components\MarkdownEditor::make('description')
                    ->required()
                    ->columnSpan('full')
                    ->placeholder('Explain assignment...'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TagsColumn::make('technologies.name')
                    ->separator(','),

                Tables\Columns\BadgeColumn::make('level')
                    ->enum(Assignment::getLevels())
                    ->colors([
                        'primary' => 0,
                        'secondary' => 1,
                        'warning' => 2,
                        'danger' => 3,
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('dS F, Y h:i A'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('technologies')
                    ->searchable()
                    ->relationship('technologies', 'name'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\UserAssignmentsRelationManager::class
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignments::route('/'),
            'create' => Pages\CreateAssignment::route('/create'),
            'edit' => Pages\EditAssignment::route('/{record}/edit'),
        ];
    }    
}
