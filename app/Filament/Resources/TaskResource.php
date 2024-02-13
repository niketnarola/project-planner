<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $pluralModelLabel = 'All Tasks';

    protected static ?string $navigationIcon = 'heroicon-o-template';

    protected static ?string $navigationGroup = 'Projects';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery( $accessFrom = null ): Builder
    {
        $builder = static::getModel()::query()->whereBelongsTo(auth()->user());
        if ( !$accessFrom ) {
            $builder = $builder
                // ->where('created_at', 'NOT LIKE', '%'. now()->format('Y-m-d') .'%')
                ->oldest('status')->latest();
        }

        return $builder;
    }

    public static function getForm(): array
    {
        return [
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\MarkdownEditor::make('note')
                        ->required()
                        ->columnSpan('full')
                        ->toolbarButtons([
                            'bold', 'bulletList',
                            'edit', 'italic',
                            'link', 'orderedList',
                            'strike', 'attachFiles',
                            'preview',
                        ]),

                    Forms\Components\Select::make('project_id')
                        ->label('Add to project')
                        ->searchable()
                        ->preload()
                        ->relationship('project', 'title', function ($query)  {
                            $query->where('user_id', auth()->id());
                        }),

                    Forms\Components\Select::make('priority')
                        ->searchable()
                        ->options(Task::getPriorities()),

                    Forms\Components\Placeholder::make('created_at')
                        ->visible(fn ($livewire) => $livewire->mountedTableAction === 'edit')
                        ->content(fn (Model $record) => date('dS F, Y', strtotime($record->created_at))),

                    Forms\Components\Checkbox::make('status')
                        ->label('Completed')
                        ->visible(fn ($livewire) => $livewire->mountedTableAction === 'edit')
                        ->columnSpan('full'),
                ])
                ->columnSpan(2)
                ->columns(2),
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
                Tables\Columns\CheckboxColumn::make('status'),

                Tables\Columns\BadgeColumn::make('priority')
                    ->placeholder('N/A')
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
            ->actions([
                Tables\Actions\EditAction::make()
                    ->using(function (Model $record, array $data): Model {
                        $data['completed_at'] = $data['status'] ? now() : null;

                        $record->update($data);
                 
                        return $record;
                    })
                    ->slideOver()
                    ->successNotificationTitle('Task has been updated.'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTasks::route('/'),
        ];
    }
}
