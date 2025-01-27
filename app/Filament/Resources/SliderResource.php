<?php

namespace App\Filament\Resources;

use App\Constants\UploadPath;
use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Home Slider';
    protected static ?string $navigationGroup = 'Promotion';
    protected static ?string $slug = 'promotions/sliders';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Slider Information')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->label(__('Slider Image'))
                            ->maxSize(2048)
                            ->rules(['required', 'mimes:png,jpg,jpeg,webp,gif', 'max:2048'])
                            ->image()
                            ->directory(UploadPath::SLIDER_UPLOAD_PATH)
                            ->helperText(__('Maximum size is 2MB'))
                            ->disk(getActiveDisk())
                            ->required()
                            ->imageEditor()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->string()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('target_link')
                            ->label('Link')
                            ->rules('nullable')
                            ->maxLength(255),
                        Forms\Components\Select::make('target_anchor')
                            ->label('Target')
                            ->required()
                            ->options([
                                '_self' => 'Same Tab',
                                '_blank' => 'New Tab'
                            ])
                            ->default('_self')
                            ->native(false),


                    ])->columnSpan(2)->columns(2),
                Forms\Components\Section::make('Other')
                    ->schema([
                        Forms\Components\DatePicker::make('start_at')->native(false),
                        Forms\Components\DatePicker::make('end_at')->native(false),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                    ])->columnSpan(1)->columns(2),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->conversion('thumb'),
                Tables\Columns\TextColumn::make('description')->searchable(),
                Tables\Columns\TextColumn::make('target_link')->label('Link')->searchable(),
                Tables\Columns\TextColumn::make('target_anchor')->label('Target')->sortable(),
                Tables\Columns\TextColumn::make('start_at')->sortable()
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->date()
                    ->sortable(),
                self::getIsActiveColumn(),
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
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
        return auth()->user()->can('update_slider');
    }
}
