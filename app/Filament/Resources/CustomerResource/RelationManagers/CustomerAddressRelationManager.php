<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\City;
use App\Models\Province;
use App\Models\SubDistrict;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class CustomerAddressRelationManager extends RelationManager
{
    protected static string $relationship = 'address';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name Address'))
                    ->placeholder(__('e.g: ') . 'Apartement')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('province_id')
                    ->label(__('Province'))
                    ->options(Province::where('country_id', 1)->get()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('city', null);
                        $set('sub_district_id', null);
                    })
                    ->required(),
                Forms\Components\Select::make('city_id')
                    ->label(__('City'))
                    ->options(function (Get $get, string $operation): Collection {
                        return City::where('province_id', $get('province_id'))->get()->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('sub_district_id', null);
                    })
                    ->required(),
                Forms\Components\Select::make('sub_district_id')
                    ->label(__('Subdistrict'))
                    ->options(function (Get $get, string $operation, ?string $state): Collection {
                        return SubDistrict::where('city_id', $get('city_id'))->get()->pluck('name', 'id');
                    })
                    ->searchable()->preload()
                    ->required(),
                Forms\Components\Textarea::make('address')
                    ->maxLength(300)
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('postal_code')
                    ->numeric()
                    ->required()
                    ->minLength(5)
                    ->maxLength(5)

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name Address')),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Phone')),
                Tables\Columns\TextColumn::make('province.name')
                    ->label(__('Province')),
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('City')),
                Tables\Columns\TextColumn::make('subDistrict.name')
                    ->label(__('Sub District')),
                Tables\Columns\TextColumn::make('postal_code')
                    ->label(__('Postal Code')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
