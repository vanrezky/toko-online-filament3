<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStats;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class SalesStatsOverviewWidget extends BaseWidget
{
    protected int $cacheSeconds = 300;
    
    protected static bool $isDiscovered = true;

    protected function getStats(): array
    {
        $filters = $this->filter ?? [];
        $stats = new DashboardStats($filters);

        $revenueStats = $stats->getRevenueStats();
        $ordersStats = $stats->getOrdersStats();
        $customersStats = $stats->getNewCustomersStats();
        $aovStats = $stats->getAverageOrderValueStats();

        return [
            Stat::make('Total Revenue', toMoney($revenueStats['value']))
                ->description($this->formatPercentChange($revenueStats['percent_change']) . ' dari periode sebelumnya')
                ->descriptionIcon($revenueStats['percent_change'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($this->getMiniChart())
                ->color($revenueStats['percent_change'] >= 0 ? 'success' : 'danger'),

            Stat::make('Total Orders', number_format($ordersStats['value']))
                ->description($this->formatPercentChange($ordersStats['percent_change']) . ' dari periode sebelumnya')
                ->descriptionIcon($ordersStats['percent_change'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ordersStats['percent_change'] >= 0 ? 'success' : 'danger'),

            Stat::make('New Customers', number_format($customersStats['value']))
                ->description($this->formatPercentChange($customersStats['percent_change']) . ' dari periode sebelumnya')
                ->descriptionIcon($customersStats['percent_change'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($customersStats['percent_change'] >= 0 ? 'success' : 'danger'),

            Stat::make('Avg. Order Value', toMoney($aovStats['value']))
                ->description($this->formatPercentChange($aovStats['percent_change']) . ' dari periode sebelumnya')
                ->descriptionIcon($aovStats['percent_change'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($aovStats['percent_change'] >= 0 ? 'success' : 'danger'),
        ];
    }

    protected function getMiniChart(): array
    {
        $filters = $this->filter ?? [];
        $stats = new DashboardStats($filters);
        $trend = $stats->getSalesTrend();
        
        return array_slice($trend['current'], -7);
    }

    protected function formatPercentChange(float $percent): string
    {
        $sign = $percent >= 0 ? '+' : '';
        return $sign . $percent . '%';
    }
}