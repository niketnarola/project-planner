<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Forms;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;
use Konnco\FilamentImport\Actions\ImportAction;
use Konnco\FilamentImport\Actions\ImportField;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
                ->color('secondary')
                ->icon('heroicon-o-upload')
                ->label('Import Projects')
                ->fields([
                    ImportField::make('title')
                        ->required()
                        ->label('Title')
                        ->rules([
                            'required', 'string', 'max:100',
                        ]),

                    ImportField::make('description')
                        ->label('Description')
                        ->rules([
                            'string',
                        ]),

                    ImportField::make('start_at')
                        ->label('Start At')
                        ->helperText('Format should be ' . date('Y-m-d H:i:s'))
                        ->rules([
                            'date_format:Y-m-d H:i:s',
                        ]),

                    ImportField::make('status')
                        ->label('Status')
                        ->helperText('1 - Completed, 0 - Pending')
                        ->rules([
                            'numeric',
                        ]),

                    ImportField::make('priority')
                        ->label('Priority')
                        ->helperText('1 - Highest, 2 - High, 3 - Normal, 4 - Low, 5 - Lowest')
                        ->rules([
                            'numeric',
                        ]),

                ], columns: 2)
                ->handleRecordCreation(function( $data ) {
                    $data['user_id'] = auth()->id();
                    $data['slug'] = Str::slug($data['title']);
                    return Project::create($data);
                }),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No projects found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Click on "New Project" add new';
    }
}
