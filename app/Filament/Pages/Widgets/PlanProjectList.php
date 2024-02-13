<?php

namespace App\Filament\Pages\Widgets;

use App\Filament\Resources\PlanResource;
use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Closure;
use Filament\Forms;
use Filament\Tables;
use Filament\Widgets\TableWidget as PageWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PlanProjectList extends PageWidget
{    
	public $tableHeading;

	public $tableHeadingId;

	public $record;

	public $plan;

	public function mount(): void
	{
	    $plan = PlanResource::getEloquentQuery()
	        ->select('id')
	        ->where('slug', $this->record)
	        ->first();
	    
	    abort_if(!$plan, 404);

	    $this->plan = $plan;
	}

	protected function getTableHeading(): string | Htmlable | Closure | null
	{
	    return $this->tableHeading;
	}

    protected function getTableQuery(): Builder
    {
        return ProjectResource::getEloquentQuery()->withWhereHas('project_plans', function ($query) {
            $query->where([
                'plan_id' => $this->plan->id,
                'plan_category_id' => $this->tableHeadingId,
            ]);
        });
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No projects found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Click on "Add Project" add new';
    }

	protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\BadgeColumn::make('priority')
                ->placeholder('N/A')
                ->enum(Project::getPriorities())
                ->colors(Project::getColors()),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\ActionGroup::make([
                Tables\Actions\Action::make('Add Note')
                	->color('secondary')
                	->icon('heroicon-o-plus')
                    ->modalHeading(fn (Model $record) => "Add Note | {$record->title}")
                    ->action(function (Project $record, array $data): void {
                        $this->plan->project_plans()->where('project_id', $record->id)->update($data);
                        $this->plan->save();
                    })
                    ->mountUsing(fn (Forms\ComponentContainer $form, Project $record) => $form->fill([
                        'note' => $this->plan->project_plans()->where('project_id', $record->id)->first()?->note,
                    ]))
                    ->form([
                        Forms\Components\MarkdownEditor::make('note')
                            ->toolbarButtons([
                                'bold', 'bulletList',
                                'italic', 'orderedList',
                            ]),
                    ]),
         
                Tables\Actions\Action::make('Remove')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->modalHeading('Remove from ' . $this->tableHeading)
                    ->requiresConfirmation()
                    ->action(function (Model $record) {
                        $this->plan->project_plans()->where('project_id', $record->id)->delete();
                    }),
            ])
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Tables\Actions\Action::make('Add Project')
                ->modalHeading($this->tableHeading)
                ->button()
                ->action(function (array $data): Model {
                    return $this->plan->project_plans()->create([
                        'user_id' => auth()->id(),
                        'plan_category_id' => $this->tableHeadingId,
                        'project_id' => $data['project_id'],
                        'note' => $data['note'],
                    ]);
                })
                ->form([
                    Forms\Components\Select::make('project_id')
                        ->label('Project')
                        ->required()
                        ->searchable()
                        ->placeholder('Select project')
                        ->options(
                            ProjectResource::getEloquentQuery()
                                ->whereDoesntHave('project_plans', function ($query) {
                                    $query
                                        ->whereHas('plan', function ($q) {
                                            $q->where([
                                                'slug' => $this->record,
                                                'user_id' => auth()->id(),
                                            ]);
                                        })
                                        ->has('plan_category');
                                })
                                ->pluck('title', 'id')
                        ),

                    Forms\Components\MarkdownEditor::make('note')
                        ->toolbarButtons([
                            'bold', 'bulletList',
                            'italic', 'orderedList',
                        ]),
                ]),
        ];
    }
}