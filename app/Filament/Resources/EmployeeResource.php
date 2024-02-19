<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\City;
use App\Models\Employee;
use App\Models\Province;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

use function PHPSTORM_META\map;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Employee';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?string $recordTitleAttribute = 'first_name';
    protected static int $globalSearchResultsLimit = 10;

    public static  function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->first_name . ' ' .  $record->last_name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'departement.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Country' => $record->country->name,
            'Departement' => $record->departement->name
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['departement', 'country']);
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return EmployeeResource::getUrl('view', ['record' => $record]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'info' : 'primary';
    }

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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Personal Information')
                    ->schema([
                        TextEntry::make('first_name')->inlineLabel(),
                        TextEntry::make('last_name')->inlineLabel(),
                        TextEntry::make('birth_date')->inlineLabel(),
                        TextEntry::make('phone')->inlineLabel(),
                    ])->columns(2),
                Section::make('Address Information')
                    ->schema([
                        TextEntry::make('country.name')->label('Country name'),
                        TextEntry::make('province.name')->label('Province name'),
                        TextEntry::make('city.name')->label('City name'),

                    ])->columns(2),
                Section::make('Departement Information')
                    ->schema([
                        TextEntry::make('departement.name')
                            ->badge()
                            ->color('success')
                            ->prefix('Departement: ')
                            ->label(false),
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
                Tables\Filters\SelectFilter::make('departement_id')
                    ->relationship('departement', titleAttribute: 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->native(false),
                        DatePicker::make('created_until')->native(false),
                    ])->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder =>  $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
                            );
                    })->indicateUsing(function (array $data): ?string {
                        $text = null;
                        if ($data['created_from']) {
                            $text .= 'Created from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until']) {

                            $text .= ($data['created_from'] ? ' - ' : 'Created Before ') . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $text;
                    })->columnSpan(2)->columns(2)
            ], layout: FiltersLayout::AboveContent)->filtersFormColumns(3)
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
