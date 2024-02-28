<?php

namespace App\Filament\Resources;

use App\Constants\UploadPath;
use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Home Slider';
    protected static ?string $navigationGroup = 'PROMO';
    protected static ?string $slug = 'promo/slider';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Slider Information')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->required()
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->directory(UploadPath::IMAGES_UPLOAD_PATH),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('target_link')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('target_anchor')
                            ->required()
                            ->options([
                                '_self' => 'Same Tab',
                                '_blank' => 'New Tab'
                            ]),
                        Forms\Components\DatePicker::make('start_at')->native(false),
                        Forms\Components\DatePicker::make('end_at')->native(false),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),

                    ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('description')->searchable(),
                Tables\Columns\TextColumn::make('target_link')->searchable(),
                Tables\Columns\TextColumn::make('target_anchor')->sortable(),
                Tables\Columns\TextColumn::make('start_at')->sortable()
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->afterStateUpdated(function ($record, $state) {
                        return Notification::make()
                            ->title('Activation status updated successfully')
                            ->success()
                            ->send();
                    }),

            ])
            ->filters([
                //
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
}
