<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\City;
use App\Models\Employee;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Employee';
    protected static ?string $navigationGroup = 'Employee Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                    ])->columns(2),
                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\Select::make('country_id')
                            ->relationship('country', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('province_id', null);
                                $set('city_id', null);
                            })
                            ->required(),
                        Forms\Components\Select::make('province_id')
                            ->options(
                                fn (Get $get): Collection => Province::query()
                                    ->where(
                                        'country_id',
                                        $get('country_id')
                                    )->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('city_id', null);
                            })
                            ->required(),
                        Forms\Components\Select::make('city_id')
                            ->options(
                                fn (Get $get): Collection => City::query()
                                    ->where(
                                        'province_id',
                                        $get('province_id')
                                    )->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Select::make('departement_id')
                            ->relationship('departement', titleAttribute: 'name')
                            ->required()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->tooltip('address')
                    ->description(function (Employee $record) {
                        return $record->country->name . ', ' . $record->province->name . ', ' . $record->city->name;
                    })
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
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
                // Tables\Filters\SelectFilter::make('country_id')
                //     ->relationship('country', titleAttribute: 'name')
                //     ->searchable()
                //     ->preload()
                //     ->live()
                //     ->afterStateUpdated(function (Set $set) {
                //         $set('province_id', null);
                //         $set('city_id', null);
                //     })
                //     ->required(),
                // Tables\Filters\SelectFilter::make('province_id')
                //     ->options(
                //         fn (Get $get): Collection => Province::query()
                //             ->where(
                //                 'country_id',
                //                 $get('country_id')
                //             )->pluck('name', 'id')
                //     )
                //     ->searchable()
                //     ->preload()
                //     ->live()
                //     ->afterStateUpdated(function (Set $set) {
                //         $set('city_id', null);
                //     })
                //     ->required(),
                // Tables\Filters\SelectFilter::make('city_id')
                // ->options(
                //     fn (Get $get): Collection => City::query()
                //         ->where(
                //             'province_id',
                //             $get('province_id')
                //         )->pluck('name', 'id')
                // )
                // ->searchable()
                // ->preload()
                // ->required(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
