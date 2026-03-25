<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStats;
use Filament\Widgets\ChartWidget;

class SalesTrendChart extends ChartWidget
{
    protected int $cacheSeconds = 300;

    protected static ?string $heading = 'Sales Trend (7 Hari)';

    protected function getData(): array
    {
        $filters = $this->filter ?? [];
        $stats = new DashboardStats($filters);
        $trend = $stats->getSalesTrend();

        return [
            'datasets' => [
                [
                    'label' => 'Periode Ini',
                    'data' => $trend['current'],
                    'borderColor' => '#2563eb',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Periode Sebelumnya',
                    'data' => $trend['previous'],
                    'borderColor' => '#94a3b8',
                    'backgroundColor' => 'rgba(148, 163, 184, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'borderDash' => [5, 5],
                ],
            ],
            'labels' => $trend['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'callbacks' => [
                        'label' => function ($context) {
                            $label = $context->dataset->label ?? '';
                            $value = toMoney($context->raw);
                            return "{$label}: {$value}";
                        },
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => ['maxRotation' => 45],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => 'rgba(0, 0, 0, 0.05)'],
                    'ticks' => [
                        'callback' => function ($value) {
                            return toMoney($value);
                        },
                    ],
                ],
            ],
        ];
    }
}