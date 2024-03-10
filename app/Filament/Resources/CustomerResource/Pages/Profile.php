<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\CustomerResource\Widgets\TotalBalanceWidget;
use App\Models\Customer;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class Profile extends ViewRecord
{
    protected static string $resource = CustomerResource::class;


    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make([
                    ImageEntry::make('profile_photo_url')
                        ->hiddenLabel()
                        ->extraAttributes([
                            'class' => 'justify-center'
                        ])
                        ->extraImgAttributes([
                            'alt' => 'Profile photo',
                            'loading' => 'lazy',
                            'class' => 'border-2 border-primary'
                        ])
                        ->circular(),
                    TextEntry::make('full_name')
                        ->label(__('Name'))
                        ->inlineLabel()
                        ->icon('heroicon-o-user')
                        ->iconColor('primary')
                        ->extraAttributes(['class' => 'font-bold -mb-8'])
                        ->alignEnd(),
                    TextEntry::make('email')
                        ->inlineLabel()
                        ->alignEnd()
                        ->icon('heroicon-o-envelope')
                        ->iconColor('primary'),
                    TextEntry::make('distributorLevel.name')
                        ->label(__('Level'))
                        ->inlineLabel()
                        ->icon('heroicon-o-briefcase')
                        ->iconColor('primary')
                        ->extraAttributes(['class' => 'font-bold'])
                        ->visible(fn (Customer $record): bool => !empty($record->distributor_level_id))
                        ->alignEnd(),
                ])
                    ->columnSpan(1),
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Profile Details')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextEntry::make('first_name'),
                                TextEntry::make('last_name'),
                                TextEntry::make('email')
                                    ->icon(fn ($record): string => match ($record->has_verified_email) {
                                        true => 'heroicon-o-check-badge',
                                        false => 'heroicon-o-x-circle',
                                    })
                                    ->iconColor(fn (Customer $record): string => $record->has_verified_email ? 'success' : 'danger')
                                    ->iconPosition('after')
                                    ->tooltip(fn (Customer $record): string => $record->has_verified_email ? __('Email Verified') : __('Email Unverified')),
                                TextEntry::make('username')->default('-'),
                                TextEntry::make('phone'),
                                TextEntry::make('balance')->numeric(decimalPlaces: 2)->prefix('IDR ')
                                    ->badge()
                                    ->color('primary')
                                    ->size('xl'),
                            ])->inlineLabel(),
                        Tabs\Tab::make('Balance')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                TextEntry::make('balance')->numeric(decimalPlaces: 2)
                                    ->icon('heroicon-o-banknotes')
                                    ->iconColor('primary')
                                    ->color('primary')
                                    ->weight('bold')
                                    ->size('xl'),

                            ])->inlineLabel(),
                        Tabs\Tab::make('Activity')
                            ->schema([
                                // ...
                            ]),
                        Tabs\Tab::make('Logs')
                            ->schema([
                                // ...
                            ]),
                    ])
                    ->columnSpan(2),

            ])->columns(3);
    }


    protected function getHeaderWidgets(): array
    {

        return [
            // TotalBalanceWidget::class,
        ];
    }
}
