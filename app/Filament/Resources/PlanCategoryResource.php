<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanCategoryResource\Pages;
use App\Filament\Resources\PlanCategoryResource\RelationManagers;
use App\Models\PlanCategory;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanCategoryResource extends Resource
{
    protected static ?string $model = PlanCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-add';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()->whereBelongsTo(auth()->user())->latest();
    }

    public static function getForm(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->autocomplete('off')
                ->placeholder('Name')
                ->required(),
            Forms\Components\ColorPicker::make('color')
                ->placeholder('Color')
                ->required()
                ->helperText('It will use in Chart.')
                ->hex(),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('color'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('dS F, Y h:i A'),
            ])
            ->filters([
                //
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
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePlanCategories::route('/'),
        ];
    }    
}
