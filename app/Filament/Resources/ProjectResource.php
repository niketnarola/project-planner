<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Filament\Resources\TagResource;
use App\Models\Category;
use App\Models\Project;
use App\Models\Tag;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Projects';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()->whereBelongsTo(auth()->user())->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Card::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->autocomplete('off')
                                    ->placeholder('Title')
                                    ->maxLength(100)
                                    ->autofocus()
                                    ->columnSpan('full')
                                    ->required(),

                                Forms\Components\MarkdownEditor::make('description')
                                    ->columnSpan('full')
                                    ->toolbarButtons([
                                        'bold', 'bulletList',
                                        'edit', 'italic',
                                        'link', 'orderedList',
                                        'preview', 'strike',
                                    ]),
                            ])
                            ->columnSpan(2)
                            ->columns(2),

                    Forms\Components\Grid::make(1)
                        ->schema([
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Select::make('priority')
                                        ->searchable()
                                        ->options(Project::getPriorities()),

                                    Forms\Components\DatePicker::make('start_at')
                                        ->format('Y-m-d'),

                                    Forms\Components\Select::make('category_id')
                                        ->label('Category')
                                        ->searchable()
                                        ->preload()
                                        ->options(CategoryResource::getEloquentQuery()->pluck('name', 'id'))
                                        ->createOptionForm(CategoryResource::getForm())
                                        ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                            return $action
                                                ->modalHeading('Creating new category')
                                                ->modalButton('Create');
                                        })
                                        ->createOptionUsing(function (Category $category, array $data) {
                                            $data['user_id'] = auth()->id();
                                            return $category::create($data)->id;
                                        }),

                                    Forms\Components\Select::make('tags')
                                        ->multiple()
                                        ->searchable()
                                        ->preload()
                                        ->relationship('tags', 'name', function ($query) {
                                            return $query->where('user_id', auth()->id());
                                        })
                                        ->createOptionForm(TagResource::getForm())
                                        ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                            return $action
                                                ->modalHeading('Creating new tag')
                                                ->modalButton('Create');
                                        })
                                        ->createOptionUsing(function (Tag $tag, array $data) {
                                            $data['user_id'] = auth()->id();
                                            return $tag::create($data)->id;
                                        }),
                                ]),
                        ])
                        ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),

                Tables\Columns\BadgeColumn::make('priority')
                    ->placeholder('N/A')
                    ->enum(Project::getPriorities())
                    ->colors(Project::getColors()),

                Tables\Columns\TextColumn::make('category.name')
                    ->placeholder('N/A'),
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
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\TasksRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
