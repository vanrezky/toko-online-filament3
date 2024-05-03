<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\CustomerResource\RelationManagers\BalanceRelationManager;
use App\Filament\Resources\CustomerResource\Widgets\TotalBalanceWidget;
use App\Models\Balance;
use App\Models\Customer;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Actions as Act;
use Livewire\Component;

class Profile extends ViewRecord
{
    protected static string $resource = CustomerResource::class;


    public function balance($data, $record, string $trx_type)
    {
        DB::beginTransaction();
        try {

            if ($trx_type === '+') {
                $balance = $record->balance + $data['balance'];
                $record->fill(['balance' => $balance]);
                $record->save();

                $data = (new Balance)->addBalance(customer_id: $record->id, amount: $data['balance'], charge: 0, post_balance: $balance, trx_type: '+', notes: $data['notes']);

                $text = __('Balance added successfully');
            } else {

                $record->decrement('balance', $data['balance']);

                $data = (new Balance)->reduceBalance(customer_id: $record->id, amount: $data['balance'], charge: 0, post_balance: $record->balance, notes: $data['notes']);

                $text = __('Balance reduce successfully');
            }

            $record->balances()->create($data);

            DB::commit();

            return Notification::make()
                ->title($text)
                ->success()
                ->send();
        } catch (\Exception $e) {
            dump($e->getMessage());
            DB::rollBack();
            return Notification::make()
                ->title(__('Balance added failed'))
                ->danger();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Act\EditAction::make()->label('Edit'),
        ];
    }

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

                Section::make('Profile Details')
                    ->icon('heroicon-o-user-circle')
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
                        TextEntry::make('balance')->numeric(decimalPlaces: 2)
                            ->icon('heroicon-o-banknotes')
                            ->iconColor('primary')
                            ->color('primary')
                            ->weight('bold')
                            ->size('xl')
                            ->id('balance')
                            ->prefix(settings('currency_text')  . ' ')
                            ->suffixActions([
                                Action::make('addBalance')
                                    ->tooltip(__('Add Balance'))
                                    ->form([
                                        TextInput::make('balance')
                                            ->required()
                                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
                                        TextInput::make('notes')
                                            ->required(),
                                    ])
                                    ->icon('heroicon-m-plus')
                                    ->action(function (array $data, Customer $record) {
                                        return $this->balance($data, $record, '+');
                                    })
                                    ->after(function (Component $livewire) {
                                        $livewire->dispatch('refreshBalanceRelationManager');
                                    }),
                                Action::make('reduceBalance')
                                    ->tooltip(__('Reduce Balance'))
                                    ->form([
                                        TextInput::make('balance')
                                            ->required()
                                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
                                        TextInput::make('notes')
                                            ->required(),
                                    ])
                                    ->icon('heroicon-m-minus')
                                    ->action(function (array $data, Customer $record) {
                                        return $this->balance($data, $record, '-');
                                    })
                                    ->after(function (Component $livewire) {
                                        $livewire->dispatch('refreshBalanceRelationManager');
                                    }),
                            ])
                            ->inlineLabel(),
                    ])->inlineLabel()->columnSpan(2)

            ])->columns(3);
    }


    protected function getHeaderWidgets(): array
    {

        return [
            // TotalBalanceWidget::class,
        ];
    }
}
