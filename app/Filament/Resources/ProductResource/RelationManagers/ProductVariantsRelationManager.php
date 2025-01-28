<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\ProductVariantAttribute;
use App\Models\ProductVariantAttributeOption;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductVariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'productVariants';
    protected static ?string $recordTitleAttribute = 'sku';
    protected $attributeColorMapping = [
        'Renk' => 'danger',
        'Beden' => 'success',
        'Materyal' => 'warning',
    ];


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('attributeOptionSkus')
                    // ->relationship()
                    ->schema([
                        Select::make('attribute_id')
                            ->label('Özellik')
                            ->options(ProductVariantAttribute::pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->fixIndistinctState()
                            ->afterStateUpdated(function (callable $get) {
                                return ProductVariantAttributeOption::where('attribute_id', $get('attribute_id'))->pluck('value', 'id');
                            }),
                        Select::make('attribute_option_id')
                            ->label('Değer')
                            ->options(function (callable $get) {
                                return $get('attribute_id') ? ProductVariantAttributeOption::where('attribute_id', $get('attribute_id'))->pluck('value', 'id') : [];
                            })
                            ->fixIndistinctState()
                            ->multiple()
                            ->required(),
                    ])
                    ->columnSpanFull()
                    ->minItems(1)
                    ->label('Varyant'),
                TextInput::make('price')
                    ->label('Fiyat')
                    ->numeric()
                    ->required(),

                TextInput::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->required(),
                SpatieMediaLibraryFileUpload::make('image')
                    ->disk('productImages')
                    ->multiple()
                    ->reorderable()
                    ->responsiveImages()
                    ->manipulations([
                        'thumb' => ['orientation' => '90'],
                    ])
                    ->conversion('thumbnail')
                    ->collection('product_images')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Variants')
            ->columns([
                Tables\Columns\TextColumn::make('Variants'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
