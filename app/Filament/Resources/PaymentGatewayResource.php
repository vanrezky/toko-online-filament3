<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentGatewayResource\Pages;
use App\Filament\Resources\PaymentGatewayResource\RelationManagers;
use App\Models\PaymentGateway;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentGatewayResource extends Resource
{
    protected static ?string $model = PaymentGateway::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'setting/payment-gateway';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('form_id')
                    ->numeric(),
                Forms\Components\TextInput::make('code')
                    ->numeric(),
                Forms\Components\TextInput::make('name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('alias')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('status')
                    ->required(),
                Forms\Components\TextInput::make('gateway_parameters'),
                Forms\Components\TextInput::make('support_currencies'),
                Forms\Components\TextInput::make('extra_data'),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                Tables\Columns\TextColumn::make('form_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alias')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('description')
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
            'index' => Pages\ListPaymentGateways::route('/'),
            // 'create' => Pages\CreatePaymentGateway::route('/create'),
            // 'edit' => Pages\EditPaymentGateway::route('/{record}/edit'),
        ];
    }
}
