<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LowStockAlertWidget;
use App\Filament\Widgets\OrdersByStatusChart;
use App\Filament\Widgets\RecentOrdersTable;
use App\Filament\Widgets\SalesStatsOverviewWidget;
use App\Filament\Widgets\SalesTrendChart;
use App\Filament\Widgets\TopProductsChart;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $title = 'Dashboard';

    protected static ?string $slug = 'dashboard';

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->placeholder('Start Date')
                            ->displayFormat('d M Y'),
                        DatePicker::make('endDate')
                            ->placeholder('End Date')
                            ->displayFormat('d M Y'),
                    ])
                    ->columns(2),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            SalesStatsOverviewWidget::class,
            SalesTrendChart::class,
            TopProductsChart::class,
            OrdersByStatusChart::class,
            RecentOrdersTable::class,
            LowStockAlertWidget::class,
        ];
    }
}
