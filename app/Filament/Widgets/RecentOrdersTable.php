<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\TransactionProduct;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RecentOrdersTable extends BaseWidget
{
    protected int $cacheSeconds = 300;

    protected int $pageSize = 5;

    protected static ?string $heading = 'Recent Orders';

    protected function getTableQuery(): Builder
    {
        return Transaction::query()
            ->with('customer')
            ->select(['transactions.*'])
            ->selectSub(function ($query) {
                $query->selectRaw('SUM((price * quantity) - discount)')
                    ->from('transcation_products')
                    ->whereColumn('transaction_id', 'transactions.id');
            }, 'total_amount')
            ->orderByDesc('created_at')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('uuid')
                ->label('Order ID')
                ->searchable(),
            
            Tables\Columns\TextColumn::make('customer.full_name')
                ->label('Customer')
                ->searchable(),
            
            Tables\Columns\TextColumn::make('total_amount')
                ->label('Amount')
                ->money('IDR')
                ->sortable(),
            
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'unpaid' => 'warning',
                    'shipped' => 'info',
                    'delivered' => 'primary',
                    'rejected' => 'danger',
                    'completed' => 'success',
                }),
            
            Tables\Columns\TextColumn::make('created_at')
                ->label('Date')
                ->dateTime('d M Y, H:i')
                ->sortable(),
        ];
    }
}