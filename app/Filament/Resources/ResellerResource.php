<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResellerResource\Pages;
use App\Models\Reseller;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ResellerResource extends Resource
{
    protected static ?string $model = Reseller::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Reseller Level';
    protected static ?string $navigationGroup = 'Customer';
    protected static ?string $slug = 'reseller-level';
    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->placeholder(__('e.g: Agent'))
                            ->maxLength(50)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('description')
                            ->placeholder(__('e.g: Agent is a user who can sell products'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('level')
                            ->placeholder(__('e.g: 5'))
                            ->required()
                            ->maxValue(10)
                            ->numeric()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Is Level Active')
                            ->required()
                            ->columnSpanFull()
                            ->default(true),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('level')
                    ->searchable(),
                self::getIsActiveColumn(),
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
                Tables\Actions\DeleteAction::make()->action(function ($record) {
                    if ($record->customers()->count()) {
                        return notification(__('Reseller Level cannot be deleted'), 'warning');
                    }

                    $record->delete();
                    return notification(__('Reseller Level cannot be deleted'), 'success');
                }),
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
            'index' => Pages\ListResellers::route('/'),
            'create' => Pages\CreateReseller::route('/create'),
            'edit' => Pages\EditReseller::route('/{record}/edit'),
        ];
    }

    public static function getIsActiveColumn()
    {
        if (self::shouldCanUpdate()) {
            return Tables\Columns\ToggleColumn::make('is_active')
                ->afterStateUpdated(fn() => notification(__('Activation status updated successfully'), 'success'))
                ->label(__('Active'));
        }

        return Tables\Columns\IconColumn::make('is_active')->boolean()->label(__('Active'));
    }

    public static function shouldCanUpdate(): bool
    {
        return auth()->user()->can('update_reseller');
    }
}
