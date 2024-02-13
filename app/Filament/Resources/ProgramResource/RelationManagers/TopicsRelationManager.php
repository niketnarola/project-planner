<?php

namespace App\Filament\Resources\ProgramResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TopicsRelationManager extends RelationManager
{
    protected static string $relationship = 'topics';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->placeholder('Name')
                    ->maxLength(100),

                Forms\Components\TextInput::make('timeline')
                    ->placeholder('E.g. 2 Days')
                    ->maxLength(10),

                Forms\Components\MarkdownEditor::make('description')
                    ->columnSpan('full')
                    ->required(),

                Forms\Components\Repeater::make('reference_links')
                    ->columnSpan('full')
                    ->defaultItems(0)
                    ->createItemButtonLabel('Add reference links')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->placeholder('Site Name')
                            ->required(),

                        Forms\Components\TextInput::make('url')
                            ->label('URL')
                            ->placeholder('Site URL')
                            ->url()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('timeline'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('dS F, Y h:i A'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\CreateAction::make(),
                    Tables\Actions\AssociateAction::make(),
                ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DissociateAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DissociateBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
