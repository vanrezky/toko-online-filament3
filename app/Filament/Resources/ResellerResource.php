<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResellerResource\Pages;
use App\Filament\Resources\ResellerResource\RelationManagers;
use App\Models\Reseller;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResellerResource extends Resource
{
    protected static ?string $model = Reseller::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Reseller Level';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?string $slug = 'setting/reseller';
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
                Tables\Columns\ToggleColumn::make('is_active')
                    ->afterStateUpdated(function ($record, $state) {
                        return Notification::make()
                            ->title('Activation status updated successfully')
                            ->success()
                            ->send();
                    })->disabled(!auth()->user()->can('update_reseller')),
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
                        return Notification::make()
                            ->title('Reseller Level cannot be deleted')
                            ->warning()
                            ->send();
                    }

                    $record->delete();
                    return Notification::make()
                        ->title('Deleted')
                        ->success()
                        ->send();
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
}
