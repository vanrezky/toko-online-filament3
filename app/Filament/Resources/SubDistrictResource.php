<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubDistrictResource\Pages;
use App\Filament\Resources\SubDistrictResource\RelationManagers;
use App\Models\City;
use App\Models\SubDistrict;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Laravel\Prompts\search;

class SubDistrictResource extends Resource
{
    protected static ?string $model = SubDistrict::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Sub District';
    protected static ?string $modelLabel = 'Master Sub District';
    protected static ?string $navigationGroup = 'Master Management';
    protected static ?string $slug = 'master-data/sub-district';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sub District Information')
                    ->schema([
                        Forms\Components\Select::make('city_id')
                            ->relationship('city', titleAttribute: 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('rajaongkir')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('postal_code')
                            ->required()
                            ->maxLength(10),
                    ])->columns(2)
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Subdistrict Information')
                    ->schema([
                        TextEntry::make('city.name')->label('City name'),
                        TextEntry::make('name')->label('Subdistrict name'),
                        TextEntry::make('postal_code'),
                        TextEntry::make('rajaongkir')->label('Rajaongkir code'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('city.name')

                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rajaongkir')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('postal_code')
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
                Tables\Filters\SelectFilter::make('city_id')
                    ->label('City')
                    ->relationship('city', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSubDistricts::route('/'),
            'create' => Pages\CreateSubDistrict::route('/create'),
            'edit' => Pages\EditSubDistrict::route('/{record}/edit'),
        ];
    }
}
