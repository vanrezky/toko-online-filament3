<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStats;
use Filament\Widgets\ChartWidget;

class TopProductsChart extends ChartWidget
{
    protected int $cacheSeconds = 300;

    protected static ?string $heading = 'Top Products';

    public ?string $filter = 'quantity';

    protected function getData(): array
    {
        $stats = new DashboardStats([]);
        $products = $stats->getTopProducts(5);

        $isQuantity = ($this->filter ?? 'quantity') === 'quantity';

        return [
            'datasets' => [
                [
                    'label' => $isQuantity ? 'Quantity Sold' : 'Revenue',
                    'data' => array_column($products, $isQuantity ? 'total_quantity' : 'total_revenue'),
                    'backgroundColor' => [
                        'rgba(37, 99, 235, 0.8)',
                        'rgba(37, 99, 235, 0.6)',
                        'rgba(37, 99, 235, 0.5)',
                        'rgba(37, 99, 235, 0.4)',
                        'rgba(37, 99, 235, 0.3)',
                    ],
                    'borderRadius' => 8,
                ],
            ],
            'labels' => array_column($products, 'name'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'callbacks' => [
                        'label' => function ($context) {
                            $isQuantity = ($this->filter ?? 'quantity') === 'quantity';
                            if ($isQuantity) {
                                return " {$context->raw} units";
                            }
                            return ' ' . toMoney($context->raw);
                        },
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => [
                        'callback' => function ($value) {
                            return toMoney($value);
                        },
                    ],
                ],
                'y' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'quantity' => 'By Quantity',
            'revenue' => 'By Revenue',
        ];
    }
}