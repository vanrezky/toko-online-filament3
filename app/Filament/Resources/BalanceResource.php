<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BalanceResource\Pages;
use App\Models\Balance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;


class BalanceResource extends Resource
{
    protected static ?string $model = Balance::class;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Customer';
    protected static ?string $slug = 'balances';
    protected static ?int $navigationSort = 3;

    protected static array $trxTypeOptions = [
        '+' => 'Deposit',
        '-' => 'Reduce',
    ];
    protected static array $trxTypeColor = [
        'Deposit' => 'success',
        'Reduce' => 'danger',
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer')
                            ->getOptionLabelFromRecordUsing(fn(Model $record) => $record->full_name)
                            ->searchable(['first_name', 'last_name'])
                            ->preload()
                            ->optionsLimit(20)
                            ->required(),
                        Forms\Components\Select::make('trx_type')
                            ->options(fn(): array => self::$trxTypeOptions)
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('notes')
                            ->required()
                            ->minLength(3)
                            ->maxLength(255),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.full_name')
                    ->numeric()
                    ->sortable(false),
                Tables\Columns\TextColumn::make('amount')
                    ->money('   ')
                    ->prefix(settings('currency_text'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('charge')
                    ->money('   ')
                    ->prefix(settings('currency_text'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('post_balance')
                    ->money('   ')
                    ->prefix(settings('currency_text'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('trx_type')
                    ->getStateUsing(fn(Balance $record): string => self::getTrxTypeLabel($record->trx_type))
                    ->badge()
                    ->sortable()
                    ->color(fn(string $state): string => self::getTrxTypeColor($state)),
                Tables\Columns\TextColumn::make('notes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('remark')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', direction: 'DESC')
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBalances::route('/'),
            'create' => Pages\CreateBalance::route('/create'),
            'edit' => Pages\EditBalance::route('/{record}/edit'),
        ];
    }

    public static function getTrxTypeColor(string $trxType): string
    {
        return self::$trxTypeColor[$trxType] ?? 'gray';
    }

    public static function getTrxTypeLabel(string $trxType): string
    {
        return self::$trxTypeOptions[$trxType] ?? 'Unknown';
    }
}
