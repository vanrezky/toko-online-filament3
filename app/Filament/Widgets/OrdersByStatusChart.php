<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStats;
use Filament\Widgets\ChartWidget;

class OrdersByStatusChart extends ChartWidget
{
    protected int $cacheSeconds = 300;

    protected static ?string $heading = 'Orders by Status';

    protected function getData(): array
    {
        $filters = $this->filter ?? [];
        $stats = new DashboardStats($filters);
        $data = $stats->getOrdersByStatus();

        $statusLabels = [
            'unpaid' => 'Unpaid',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
        ];

        $statusColors = [
            'unpaid' => '#f59e0b',
            'shipped' => '#3b82f6',
            'delivered' => '#8b5cf6',
            'rejected' => '#ef4444',
            'completed' => '#22c55e',
        ];

        return [
            'datasets' => [
                [
                    'data' => array_values($data),
                    'backgroundColor' => array_map(fn($status) => $statusColors[$status], array_keys($data)),
                    'borderWidth' => 0,
                ],
            ],
            'labels' => array_map(fn($status) => $statusLabels[$status], array_keys($data)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'cutout' => '65%',
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 15,
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => function ($context) {
                            $total = array_sum($context->dataset->data);
                            $value = $context->raw;
                            $percent = $total > 0 ? round(($value / $total) * 100, 1) : 0;
                            return "{$context->label}: {$value} ({$percent}%)";
                        },
                    ],
                ],
            ],
        ];
    }
}