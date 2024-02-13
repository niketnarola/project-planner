<?php

namespace App\Filament\Resources\PlanResource\Pages;

use App\Filament\Pages\Widgets;
use App\Filament\Resources\PlanCategoryResource;
use App\Filament\Resources\PlanResource;
use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Pages\Actions;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ViewPlan extends Page
{
    protected static string $resource = PlanResource::class;

    protected static string $view = 'filament.resources.plan-resource.pages.view-plan';

    public $record;
    
    public $plan_categories = array();

    protected function getHeaderWidgets(): array
    {
        return [
            Widgets\PlanProjectList::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('Add Category')
                ->form(PlanCategoryResource::getForm())
                ->color('secondary')
                ->modalHeading('Create Category')
                ->action(function (array $data): void {
                    $data['user_id'] = auth()->id();
                    auth()->user()->plan_categories()->create($data);
                    $this->redirect(
                        static::$resource::getUrl(
                            name: 'view',
                            params: [ 'record' => $this->record ]
                        )
                    );
                }),
        ];
    }

    public function mount(): void
    {
        $this->plan_categories = $this->getPlanCategories();
        
        parent::authorizeResourceAccess();
    }

    private function getPlanCategories(): Collection
    {
        return PlanCategoryResource::getEloquentQuery()
            ->pluck('name', 'id');
    }

    protected function getHeading(): string | Htmlable
    {
        $plan = static::$resource::getEloquentQuery()->where('slug', $this->record)->first();
        
        abort_if(!$plan, 404);

        return $plan->month;
    }
}
