<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Balance;
use App\Models\Customer;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;
use Filament\Actions as Act;
use Livewire\Component;

class Profile extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    public function balance($data, $record, string $trx_type)
    {
        DB::beginTransaction();
        try {
            // Determine the new balance based on the transaction type
            if ($trx_type === '+') {
                $record->increment('balance', $data['balance']);
                $text = __('Balance added successfully');
            } else {
                $record->decrement('balance', $data['balance']);
                $text = __('Balance reduced successfully');
            }

            // Use the updateBalance function to generate the transaction data
            $data = (new Balance)->updateBalance(
                customer_id: $record->id,
                amount: $data['balance'],
                charge: 0,
                post_balance: $record->balance,
                trx_type: $trx_type,
                notes: $data['notes'] ?? null
            );

            // Create the balance record
            $record->balances()->create($data);

            DB::commit();
            return notification($text);
        } catch (\Exception $e) {
            DB::rollBack();
            return notification(__('Balance update failed'), 'danger');
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
                    TextEntry::make('reseller.name')
                        ->label(__('Level'))
                        ->inlineLabel()
                        ->icon('heroicon-o-briefcase')
                        ->iconColor('primary')
                        ->extraAttributes(['class' => 'font-bold'])
                        ->visible(fn(Customer $record): bool => !empty($record->reseller_id))
                        ->alignEnd(),
                ])
                    ->columnSpan(1),

                Section::make('Profile Details')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        TextEntry::make('first_name'),
                        TextEntry::make('last_name'),
                        TextEntry::make('email')
                            ->icon(fn($record): string => match ($record->has_verified_email) {
                                true => 'heroicon-o-check-badge',
                                false => 'heroicon-o-x-circle',
                            })
                            ->iconColor(fn(Customer $record): string => $record->has_verified_email ? 'success' : 'danger')
                            ->iconPosition('after')
                            ->tooltip(fn(Customer $record): string => $record->has_verified_email ? __('Email Verified') : __('Email Unverified')),
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
