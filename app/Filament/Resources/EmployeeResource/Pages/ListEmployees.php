<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\Employee;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'This Week' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subWeek()))
                ->badge(Employee::where('created_at', '>=', now()->subWeek())->count())
                ->badgeColor('warning'),
            'This Month' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subMonth()))
                ->badge(Employee::where('created_at', '>=', now()->subMonth())->count())
                ->badgeColor('warning'),
            'This Year' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subYear()))
                ->badge(Employee::where('created_at', '>=', now()->subYear())->count())
                ->badgeColor('warning'),

        ];
    }
}
