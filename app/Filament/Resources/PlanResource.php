<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Filament\Resources\PlanResource\RelationManagers;
use App\Filament\Resources\PlanResource\Widgets;
use App\Models\Plan;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Cache;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-report';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()->whereBelongsTo(auth()->user())->latest();
    }

    protected static function getMonths(): array
    {
        $months = [];
        for ($m = 1; $m <= 12; ++$m) {
            $k = $m < 10 ? "0$m" : $m;
            $months[$m] = date('F', mktime(0, 0, 0, $m, 1));
        }
        return $months;
    }

    public static function canCreate(): bool
    {
        $user_id = auth()->id();
        $key = "plans.$user_id";

        if ( Cache::has($key) ) {
            $exists = Cache::get($key);
            if ( $exists ) {
                return !$exists;
            }
        }

        $exists = static::getEloquentQuery()->where([
            'month' => date('F - Y'),
            'user_id' => auth()->id(),
        ])->count();

        Cache::add($key , $exists, now()->endOfMonth() );

        return !$exists;
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\PlanChart::class,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\Select::make('month')
                        ->searchable()
                        ->unique(ignoreRecord: true)
                        ->default((int) date('m'))
                        ->options(static::getMonths())
                        ->required(),

                    Forms\Components\TextInput::make('year')
                        ->default(date('Y'))
                        ->disabled()
                        ->required(),
                ])
                ->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('month'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('View')
                        ->icon('heroicon-o-eye')
                        ->color('secondary')
                        ->url(fn (Model $record) => route('filament.resources.plans.view', [ 'record' => $record->slug ])),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'view' => Pages\ViewPlan::route('/{record}/view'),
            'create' => Pages\CreatePlan::route('/create'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::canCreate() ? 'Pending' : '';
    }

    protected static function getNavigationBadgeColor(): ?string
    {
        return static::canCreate() ? 'text-orange-300' : '';
    }
}
