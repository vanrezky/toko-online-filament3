<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('uuid')
                    ->label('UUID')
                    ->required()
                    ->maxLength(36),
                Forms\Components\TextInput::make('customer_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('timelimit'),
                Forms\Components\TextInput::make('customer_address_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('weight')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('shipping_cost')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('courier_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('from_district_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('to_district_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('cod')
                    ->required(),
                Forms\Components\TextInput::make('cod_fee')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('receipt_code')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('delivery_date'),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\DateTimePicker::make('complete_date'),
                Forms\Components\Toggle::make('request_cancellation')
                    ->required(),
                Forms\Components\TextInput::make('notes')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('timelimit')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_address_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_cost')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('courier_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('from_district_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('to_district_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('cod')
                    ->boolean(),
                Tables\Columns\TextColumn::make('cod_fee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('receipt_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('complete_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('request_cancellation')
                    ->boolean(),
                Tables\Columns\TextColumn::make('notes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
