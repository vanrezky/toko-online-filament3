<?php

namespace App\Filament\Resources;

use App\Constants\UploadPath;
use App\Filament\Resources\PromotionResource\Pages;
use App\Filament\Resources\PromotionResource\RelationManagers;
use App\Filament\Resources\SliderResource\Pages\ListSliders;
use App\Models\Promotion;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Promotion';
    protected static ?string $slug = 'promotion/promotions';
    protected static ?int $navigationSort = 1;
    public Promotion $record;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Promotion Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->minLength(3)
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(255)
                            ->string()
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('image')
                            ->image()
                            ->imageEditor()
                            ->imagePreviewHeight(250)
                            ->maxSize(2048)
                            ->rules(['required', 'mimes:png,jpg,jpeg,webp,gif', 'max:2048'])
                            ->directory(UploadPath::PROMOTION_UPLOAD_PATH)
                            ->hint(__('Maximum size is 2MB'))
                            ->disk(getActiveDisk())
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('start_at')->native(false),
                        Forms\Components\DatePicker::make('end_at')->native(false),
                    ])->columnSpan(2)->columns(2),

                Forms\Components\Section::make('Promotion Information')
                    ->schema([
                        Forms\Components\DatePicker::make('start_at')->native(false),
                        Forms\Components\DatePicker::make('end_at')->native(false),
                        Forms\Components\TextInput::make('position')
                            ->required()
                            ->maxLength(255)
                            ->default('top')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                    ])->columnSpan(1)->columns(2)


            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->conversion('thumb'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
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
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
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
        return auth()->user()->can('update_promotion');
    }
}
