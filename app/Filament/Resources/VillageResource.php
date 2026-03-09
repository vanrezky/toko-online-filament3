<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VillageResource\Pages;
use App\Filament\Resources\VillageResource\RelationManagers;
use App\Models\Village;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VillageResource extends Resource
{
    protected static ?string $model = Village::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master';
    protected static ?int $navigationSort = 94;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sub_district_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('postal_code')
                    ->maxLength(10),
                Forms\Components\TextInput::make('rajaongkir')
                    ->maxLength(255),
                Forms\Components\TextInput::make('apicoid_code')
                    ->maxLength(20),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subDistrict.name'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('postal_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rajaongkir')
                    ->label('Rajaongkir Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('apicoid_code')
                    ->label('Apicoid Code')
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
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListVillages::route('/'),
            // 'create' => Pages\CreateVillage::route('/create'),
            // 'edit' => Pages\EditVillage::route('/{record}/edit'),
        ];
    }
}
