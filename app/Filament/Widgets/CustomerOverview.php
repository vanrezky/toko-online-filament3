<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class CustomerOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        return [
            Stat::make('New Customers', Customer::query()
                ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->count()),
            // ->chart([7, 2, 10, 3, 15, 4, 17])
            // ->color('success'),
            Stat::make('Revenue', money('12,000', 'USD')),
            Stat::make('Average time on page', '3:12'),
        ];
    }
}
