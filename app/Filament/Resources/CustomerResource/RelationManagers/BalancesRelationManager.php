<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\Balance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Attributes\On;

class BalancesRelationManager extends RelationManager
{
    protected static string $relationship = 'balances';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('note')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label(__('Date'))->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')->money('   ')->sortable()
                    ->prefix(settings('currency_text')),
                Tables\Columns\TextColumn::make('charge')->money('   ')->sortable()
                    ->prefix(settings('currency_text')),
                Tables\Columns\TextColumn::make('post_balance')->money('   ')
                    ->prefix(settings('currency_text')),
                Tables\Columns\TextColumn::make('trx_type')->alignCenter()->badge()->sortable(),
                Tables\Columns\TextColumn::make('notes')->searchable(),
                Tables\Columns\TextColumn::make('remark')->searchable(),
            ])

            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    #[On('refreshBalanceRelationManager')]
    public function refresh(): void
    {
    }
}
