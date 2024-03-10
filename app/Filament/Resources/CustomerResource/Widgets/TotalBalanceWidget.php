<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalBalanceWidget extends BaseWidget
{

    public function getStats(): array
    {
        return [
            Stat::make('Transaction', '2'),
            Stat::make('Order Completed', '2'),
            Stat::make('Balance', 25000),
        ];
    }
}
