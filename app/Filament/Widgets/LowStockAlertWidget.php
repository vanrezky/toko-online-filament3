<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStats;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class LowStockAlertWidget extends BaseWidget
{
    protected int $cacheSeconds = 300;

    protected function getStats(): array
    {
        $filters = $this->filter ?? [];
        $stats = new DashboardStats($filters);
        
        $lowStockCount = $stats->getLowStockCount();

        $description = $lowStockCount > 0 
            ? "{$lowStockCount} products need restock"
            : "All products sufficiently stocked";

        return [
            Stat::make('Low Stock Items', $lowStockCount)
                ->description($description)
                ->descriptionIcon($lowStockCount > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($lowStockCount > 0 ? 'warning' : 'success'),
        ];
    }
}