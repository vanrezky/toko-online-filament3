<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return $this->getStatusActions();
    }

    protected function getStatusActions(): array
    {
        $record = $this->getRecord();
        $actions = [];

        switch ($record->status) {
            case 'unpaid':
                $actions[] = Actions\Action::make('markShipped')
                    ->label('Mark as Shipped')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(fn() => $this->updateStatus('shipped'));

                $actions[] = Actions\Action::make('markRejected')
                    ->label('Mark as Rejected')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn() => $this->updateStatus('rejected'));
                break;

            case 'shipped':
                $actions[] = Actions\Action::make('markDelivered')
                    ->label('Mark as Delivered')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn() => $this->updateStatus('delivered'));

                $actions[] = Actions\Action::make('markRejected')
                    ->label('Mark as Rejected')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn() => $this->updateStatus('rejected'));
                break;

            case 'delivered':
                $actions[] = Actions\Action::make('markCompleted')
                    ->label('Mark as Completed')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn() => $this->updateStatus('completed'));
                break;
        }

        return $actions;
    }

    protected function updateStatus(string $status): void
    {
        $record = $this->getRecord();

        $updateData = ['status' => $status];

        if ($status === 'shipped') {
            $updateData['delivery_date'] = now();
        } elseif ($status === 'completed') {
            $updateData['complete_date'] = now();
        }

        DB::beginTransaction();
        try {
            $record->update($updateData);
            DB::commit();

            Notification::make()
                ->title('Status Updated')
                ->body("Order status changed to {$status}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Update Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Order Information')
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        TextEntry::make('uuid')
                            ->label('Order ID')
                            ->copyable()
                            ->weight('bold')
                            ->size('lg'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'unpaid' => 'warning',
                                'shipped' => 'info',
                                'delivered' => 'success',
                                'rejected' => 'danger',
                                'completed' => 'success',
                            }),
                        TextEntry::make('created_at')
                            ->label('Order Date')
                            ->dateTime('d M Y, H:i'),
                        TextEntry::make('timelimit')
                            ->label('Payment Deadline')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Not set'),
                    ])->columns(4),

                Section::make('Order Summary')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('IDR'),
                        TextEntry::make('total_discount')
                            ->label('Total Discount')
                            ->money('IDR')
                            ->color('danger'),
                        TextEntry::make('shipping_cost')
                            ->label('Shipping Cost')
                            ->money('IDR'),
                        TextEntry::make('cod_fee')
                            ->label('COD Fee')
                            ->money('IDR')
                            ->visible(fn(Transaction $record) => $record->cod),
                        TextEntry::make('total_amount')
                            ->label('Total Amount')
                            ->money('IDR')
                            ->weight('bold')
                            ->size('lg')
                            ->color('primary'),
                    ])->columns(5),

                Section::make('Customer Information')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextEntry::make('customer.full_name')
                            ->label('Name')
                            ->icon('heroicon-o-user')
                            ->iconColor('primary'),
                        TextEntry::make('customer.email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope')
                            ->iconColor('primary'),
                        TextEntry::make('customer.phone')
                            ->label('Phone')
                            ->icon('heroicon-o-phone')
                            ->iconColor('primary'),
                        TextEntry::make('address.full_address')
                            ->label('Shipping Address')
                            ->icon('heroicon-o-map-pin')
                            ->iconColor('primary'),
                    ])
                    ->columns(2),

                Section::make('Products')
                    ->icon('heroicon-o-shopping-cart')
                    ->description(fn(Transaction $record) => $record->total_items . ' items')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('products')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Product'),
                                TextEntry::make('quantity')
                                    ->label('Qty'),
                                TextEntry::make('price')
                                    ->label('Price')
                                    ->money('IDR'),
                                TextEntry::make('discount')
                                    ->label('Discount')
                                    ->money('IDR')
                                    ->color('danger'),
                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->money('IDR')
                                    ->weight('bold'),
                                IconEntry::make('is_digital')
                                    ->label('Digital')
                                    ->boolean()
                                    ->trueIcon('heroicon-m-cloud-arrow-down')
                                    ->falseIcon(null)
                                    ->trueColor('info'),
                            ])->columns(6),
                    ]),

                Section::make('Shipping Information')
                    ->icon('heroicon-o-truck')
                    ->schema([
                        TextEntry::make('shippingDetails courier.name')
                            ->label('Courier')
                            ->placeholder('Not set'),
                        TextEntry::make('receipt_code')
                            ->label('Receipt Code')
                            ->copyable()
                            ->placeholder('Not set'),
                        TextEntry::make('weight')
                            ->label('Weight')
                            ->suffix(' gram'),
                    ])->columns(3),

                Section::make('Vouchers Applied')
                    ->icon('heroicon-o-ticket')
                    ->visible(fn(Transaction $record) => $record->vouchers->count() > 0)
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('vouchers')
                            ->schema([
                                TextEntry::make('voucher_name')
                                    ->label('Voucher'),
                                TextEntry::make('voucher_type')
                                    ->label('Type')
                                    ->badge()
                                    ->formatStateUsing(fn(string $state) => ucfirst($state)),
                                TextEntry::make('formatted_discount')
                                    ->label('Discount'),
                            ])->columns(3),
                    ]),

                Section::make('Digital Products')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->visible(fn(Transaction $record) => $record->hasDigitalProducts())
                    ->schema([
                        TextEntry::make('digital_products_count')
                            ->label('This order contains digital products')
                            ->weight('bold')
                            ->color('info'),
                    ]),

                Section::make('Additional Information')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('cod')
                            ->label('Payment Method')
                            ->formatStateUsing(fn(bool $state) => $state ? 'COD (Cash on Delivery)' : 'Transfer')
                            ->badge()
                            ->color(fn(bool $state) => $state ? 'warning' : 'info'),
                        TextEntry::make('payment_method')
                            ->label('Payment Gateway')
                            ->placeholder('Not specified'),
                        TextEntry::make('request_cancellation')
                            ->label('Cancellation Request')
                            ->formatStateUsing(fn(bool $state) => $state ? 'Yes' : 'No')
                            ->color(fn(bool $state) => $state ? 'danger' : 'gray'),
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('No notes'),
                        TextEntry::make('delivery_date')
                            ->label('Delivery Date')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Not delivered yet'),
                        TextEntry::make('complete_date')
                            ->label('Completed Date')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Not completed yet'),
                    ])->columns(2),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->load(['customer', 'products.product', 'vouchers', 'shippingDetails']);
        return $data;
    }
}
