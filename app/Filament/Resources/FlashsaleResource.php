<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlashsaleResource\Pages;
use App\Filament\Resources\FlashsaleResource\RelationManagers;
use App\Filament\Resources\FlashsaleResource\RelationManagers\ProductsRelationManager;
use App\Models\Flashsale;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlashsaleResource extends Resource
{
    protected static ?string $model = Flashsale::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Flashsales';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?string $slug = 'shop/flashsales';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        Group::make(
                            [
                                Forms\Components\DateTimePicker::make('start_time')
                                    ->required(),
                                Forms\Components\DateTimePicker::make('end_time')
                                    ->required(),
                            ]
                        )->columns(2)
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable(),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Products')
                        ->action(fn (Flashsale $record) => $record->products)
                        ->icon('heroicon-o-squares-2x2'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            ProductsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlashsales::route('/'),
            'create' => Pages\CreateFlashsale::route('/create'),
            'edit' => Pages\EditFlashsale::route('/{record}/edit'),
        ];
    }
}
