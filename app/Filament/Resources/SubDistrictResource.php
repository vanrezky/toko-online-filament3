<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubDistrictResource\Pages;
use App\Models\SubDistrict;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubDistrictResource extends Resource
{
    protected static ?string $model = SubDistrict::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Master';
    protected static ?int $navigationSort = 93;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sub District Information')
                    ->schema([
                        Forms\Components\Select::make('district_id')
                            ->relationship('district', titleAttribute: 'name')
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
                        TextEntry::make('district.name')->label('District'),
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
                Tables\Columns\TextColumn::make('district.name')

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
                Tables\Filters\SelectFilter::make('district_id')
                    ->label('District')
                    ->relationship('district', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSubDistricts::route('/'),
            // 'create' => Pages\CreateSubDistrict::route('/create'),
            // 'edit' => Pages\EditSubDistrict::route('/{record}/edit'),
        ];
    }
}
