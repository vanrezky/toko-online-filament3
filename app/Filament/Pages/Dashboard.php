<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CustomerOverview;
use App\Filament\Widgets\CustomerWidget;
use App\Filament\Widgets\EarningStatisticWidget;
use App\Filament\Widgets\Revenue;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Filament\Widgets\AccountWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected static string $view = 'filament.pages.dashboard';

    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate'),
                        DatePicker::make('endDate'),
                    ])
                    ->columns(3),
            ]);
    }

    public function getWidgetData(): array
    {
        return [];
    }
}
