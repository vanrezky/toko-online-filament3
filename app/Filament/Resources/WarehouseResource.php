<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseResource\Pages;
use App\Filament\Resources\WarehouseResource\RelationManagers;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationLabel = 'Warehouse Location';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?string $slug = 'setting/warehouse';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Warehouse Information')
                    ->schema([
                        Forms\Components\Select::make('sub_district_id')
                            ->relationship('subDistrict', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('contact_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contact_phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('courier')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subDistrict.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('courier')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->afterStateUpdated(function ($record, $state) {
                        return Notification::make()
                            ->title('Featured status updated successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }
}
