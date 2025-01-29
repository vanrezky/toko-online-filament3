<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductVariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'productVariants';
    // protected static ?string $recordTitleAttribute = 'sku';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('variant_attributes')
                    // ->relationship('variantAttributes')
                    ->label('Product Variants')
                    ->schema([
                        Select::make('product_attribute_id')
                            ->label(__('Attribute'))
                            ->options(fn() => $this->getAttribute())
                            ->required()
                            ->fixIndistinctState()
                            ->searchable()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->afterStateUpdated(fn(Set $set) => $set('product_attribute_options', []))
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Attribute'))
                                    ->required(),
                            ])->createOptionUsing(fn(array $data): int => $this->createAttribute($data)),

                        Repeater::make('product_attribute_options')
                            ->label(__('Options'))
                            ->hidden(fn(Get $get) => empty($get('product_attribute_id')))
                            ->simple(
                                Select::make('product_attribute_option_id')
                                    ->label(__('Options'))
                                    ->options(fn(Get $get) => self::getAttributeOption($get('../../product_attribute_id')))
                                    ->fixIndistinctState()
                                    ->required()
                                    ->searchable()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('Option'))
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function (array $data, Get $get): int {
                                        return self::createAttributeOption($data, $get('../../product_attribute_id'));
                                    })
                            )
                            ->minItems(1)
                    ])
                    ->columnSpanFull()
                    ->minItems(1)
                    ->maxItems(2)
                    ->label(__('Variant'))
                    ->reorderable(false),
                TextInput::make('price')
                    ->label(__('Price'))
                    ->numeric()
                    ->required()
                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
                TextInput::make('stock')
                    ->label(__('Stock'))
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Variants')
            ->columns([
                Tables\Columns\TextColumn::make('attributes.productAttribute.name')
                    ->getStateUsing(function ($record) {
                        $attributeNames = [];
                        $record->loadMissing('attributes.productAttribute');
                        foreach ($record->attributes as $key => $attribute) {
                            $attributeNames[] = $attribute->productAttribute->name;
                        }
                        return !empty($attributeNames) ? $attributeNames : __('Not specified');
                    })
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('attributes.productAttributeOption.name')
                    ->getStateUsing(function ($record) {
                        $attributeNames = [];
                        $record->loadMissing('attributes.productAttributeOption');
                        foreach ($record->attributes as $key => $attribute) {
                            $attributeNames[] = $attribute->productAttributeOption->name;
                        }
                        return !empty($attributeNames) ? $attributeNames : __('Not specified');
                    })
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('sku'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\TextColumn::make('stock'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->before(function () {
                        $this->handleVariantCreation($this->mountedTableActionsData[0]);
                    })
                    ->after(function () {
                        $this->getOwnerRecord()->productVariants()->whereNull('sku')->delete();
                    }),
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

    private function getAttribute(): array
    {
        $productId = $this->getOwnerRecord()->id;
        return ProductAttribute::where(function ($query) use ($productId) {
            $query->where('is_global', true) // Ambil atribut global
                ->orWhere('product_id', $productId); // Ambil atribut khusus untuk produk ini
        })
            ->where(function ($query) {
                $query->whereNull('user_id') // Atribut yang dibuat oleh sistem (default)
                    ->orWhere('user_id', auth()->id()); // Atribut yang dibuat oleh user saat ini
            })
            ->approved() // Hanya ambil atribut yang telah disetujui
            ->pluck('name', 'id')
            ->toArray();
    }

    private function getAttributeOption(?string $id): array
    {
        if (!$id) {
            return [];
        }

        $productId = $this->getOwnerRecord()->id;
        return ProductAttributeOption::where('product_attribute_id', $id)
            ->where(function ($query) use ($productId) {
                $query->where('is_global', true) // Ambil opsi global
                    ->orWhere('product_id', $productId); // Ambil opsi yang spesifik untuk produk ini
            })
            ->where('status', 'approved') // Hanya ambil opsi yang telah disetujui
            ->pluck('name', 'id')
            ->toArray();
    }

    private function createAttribute(?array  $data): int
    {
        return ProductAttribute::create([
            'name' => $data['name'],
            'product_id' => $this->getOwnerRecord()->id,
            'user_id' => auth()->id(),
            'status' => 'approved',
        ])->getKey();
    }

    private function createAttributeOption(?array $data, string $parentId): int
    {
        return ProductAttributeOption::create([
            'name' => $data['name'],
            'product_attribute_id' => $parentId,
            'product_id' => $this->getOwnerRecord()->id,
            'user_id' => auth()->id(),
            'status' => 'approved',
        ])->getKey();
    }

    private function handleVariantCreation(array $data): array
    {
        DB::transaction(function () use ($data) {
            $productId = $this->getOwnerRecord()->id;

            $attributes = $data['variant_attributes'];
            $combinations = $this->generateCombinations($attributes);

            $price = $data['price'];
            $stock = $data['stock'];

            foreach ($combinations as $combination) {
                $variant = ProductVariant::create([
                    'product_id' => $productId,
                    'price' => $price,
                    'stock' => $stock,
                    'sku' => $this->generateUniqueCode(),
                ]);

                foreach ($combination as $attributeData) {
                    ProductVariantAttribute::create([
                        'product_variant_id' => $variant->id,
                        'product_attribute_id' => $attributeData['product_attribute_id'],
                        'product_attribute_option_id' => $attributeData['product_attribute_option_id']
                    ]);
                }
            }
        });

        return [];
    }

    private function generateUniqueCode()
    {
        do {
            $sku = rand(1, 999999999);
        } while (ProductVariant::where('sku', $sku)->exists());

        return $sku;
    }

    private function generateCombinations(array $attributes)
    {
        $options = [];

        foreach ($attributes as $attribute) {
            if (isset($attribute['product_attribute_options']) && isset($attribute['product_attribute_id'])) {
                $attributeOptions = [];
                foreach ($attribute['product_attribute_options'] as $option) {
                    $attributeOptions[] = [
                        'product_attribute_id' => $attribute['product_attribute_id'],
                        'product_attribute_option_id' => $option['product_attribute_option_id'],
                    ];
                }
                $options[] = $attributeOptions;
            }
        }


        return $this->cartesianProduct($options);
    }

    private function cartesianProduct(array $input)
    {
        $result = [[]];

        foreach ($input as $values) {
            $append = [];
            foreach ($result as $product) {
                foreach ($values as $item) {
                    $append[] = array_merge($product, [$item]);
                }
            }
            $result = $append;
        }

        return $result;
    }

    protected function getAttributeColor(string $attributeName): string
    {
        return $this->attributeColorMapping[$attributeName] ?? 'gray';
    }

    protected function getAttributeValueColor($attributeName)
    {
        return $this->attributeColorMapping[$attributeName] ?? 'gray';
    }


    protected function getAttributeOptionColor(string $attributeName, string $optionName): string
    {
        return $this->getAttributeColor($attributeName);
    }
}
