<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Constants\UploadPath;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
use Closure;
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
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductVariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'productVariants';

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
                            ->options(fn(): array => $this->getProductAttributes())
                            ->required()
                            ->fixIndistinctState()
                            ->searchable()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->afterStateUpdated(fn(Set $set) => $set('product_attribute_options', []))
                            ->disabledOn('edit')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Attribute'))
                                    ->required()
                                    ->maxLength(100)
                                    ->rules([
                                        fn(): Closure => function (string $attribute, $value, Closure $fail) {
                                            $productId = $this->getOwnerRecord()->id;
                                            $exists = ProductAttribute::where(fn($query) => $query->where('product_id', $productId)->where('name', $value))
                                                ->orWhere(fn($query) => $query->where('product_id', null)->where('name', $value)->where('is_global', true))
                                                ->exists();

                                            if ($exists) {
                                                $fail(__('This attribute has already been created.'));
                                            }
                                        },
                                    ]),
                            ])->createOptionUsing(fn(array $data): int => $this->createAttribute($data)),

                        Repeater::make('product_attribute_options')
                            ->label(__('Options'))
                            ->hidden(fn(Get $get) => empty($get('product_attribute_id')))
                            ->simple(
                                Select::make('product_attribute_option_id')
                                    ->label(__('Options'))
                                    ->options(fn(Get $get, string $context, ?string $state): array => self::getProductAttributeOptions($get('../../product_attribute_id'), $context, $state))
                                    ->fixIndistinctState()
                                    ->required()
                                    ->searchable()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('Option'))
                                            ->required()
                                            ->maxLength(100)
                                            ->rules([
                                                fn(): Closure => function (string $attribute, $value, Closure $fail) {
                                                    $productId = $this->getOwnerRecord()->id;
                                                    $value = strtolower($value);
                                                    $exists = ProductAttributeOption::where(fn($query) => $query->where('product_id', $productId)->where('name', $value))
                                                        ->orWhere(fn($query) => $query->where('product_id', null)->where('name', $value)->where('is_global', true))
                                                        ->exists();

                                                    if ($exists) {
                                                        $fail(__('This attribute has already been created.'));
                                                    }
                                                },
                                            ]),
                                    ])
                                    ->createOptionUsing(function (array $data, Get $get): int {
                                        return self::createAttributeOption($data, $get('../../product_attribute_id'));
                                    })

                            )
                            ->minItems(1)
                            ->disabledOn('edit')
                            ->deletable(fn(string $context): bool => $context === 'create')
                            ->reorderable(fn(string $context): bool => $context === 'create')
                            ->addable(fn(string $context): bool => $context === 'create'),
                    ])
                    ->columnSpanFull()
                    ->minItems(1)
                    ->maxItems(2)
                    ->deletable(fn(string $context): bool => $context === 'create')
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
                SpatieMediaLibraryFileUpload::make('image')
                    ->hiddenOn('create')
                    ->image()
                    ->imageEditor()
                    ->required()
                    ->multiple()
                    ->reorderable()
                    ->imageCropAspectRatio('1:1')
                    ->imageEditorAspectRatios([
                        '1:1'
                    ])
                    ->optimize('webp')
                    ->disk(getActiveDisk())
                    ->rules(['required', 'mimes:png,jpg,jpeg,webp,gif', 'max:1024'])
                    ->maxSize(1024)
                    ->helperText(__('Ratio Is 1:1. Maximum size is 1MB'))
                    ->directory(UploadPath::PRODUCT_UPLOAD_PATH)
                    ->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Variants')
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->conversion('thumb')
                    ->square(),
                Tables\Columns\TextColumn::make('attributes.productAttribute.name')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('attributes.productAttributeOption.name')
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
                        $this->deleteUnusedAttributesAndOptions();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, $record): array {
                        $data['variant_attributes'] = $record->attributes
                            ->map(function ($attribute) {
                                return [
                                    'product_attribute_id' => $attribute->product_attribute_id,
                                    'product_attribute_options' => [$attribute->product_attribute_option_id],
                                ];
                            })
                            ->toArray();

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private function getProductAttributes(): array
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

    private function getProductAttributeOptions(?string $attributeId, string $context, ?string $currentOptionId): array
    {
        if (!$attributeId) {
            return [];
        }

        $productId = $this->getOwnerRecord()->id;
        return ProductAttributeOption::where('product_attribute_id', $attributeId)
            ->where(function ($query) use ($productId) {
                $query->where('is_global', true)
                    ->orWhere('product_id', $productId);
            })
            ->approved()
            ->when($context === 'edit', function ($query) use ($productId, $currentOptionId) {
                // ignored the option has been selected
                $query->whereDoesntHave('productVariantAttributeOption.productVariant', function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                })
                    ->orWhere('id', $currentOptionId);
            })
            ->pluck('name', 'id')
            ->toArray();
    }

    private function createAttribute(?array  $data): int
    {
        $productId = $this->getOwnerRecord()->id;

        return ProductAttribute::create([
            'name' => $data['name'],
            'product_id' => $productId,
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

    private function deleteUnusedAttributesAndOptions(): void
    {
        $productId = $this->getOwnerRecord()->id;
        $productVariants = ProductVariant::with('attributes')->where('product_id', $productId)->get();

        // Extract product attribute IDs and product attribute option IDs using collections
        $productAttributeIds = $productVariants
            ->pluck('attributes')
            ->flatten()
            ->pluck('product_attribute_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $productAttributeOptionIds = $productVariants
            ->pluck('attributes')
            ->flatten()
            ->pluck('product_attribute_option_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Perform deletions within a transaction
        DB::transaction(function () use ($productId, $productAttributeIds, $productAttributeOptionIds) {
            // Delete unused product attributes
            ProductAttribute::where('product_id', $productId)
                ->when(!empty($productAttributeIds), fn($query) => $query->whereNotIn('id', $productAttributeIds))
                ->delete();

            // Delete unused product attribute options
            ProductAttributeOption::where('product_id', $productId)
                ->when(!empty($productAttributeOptionIds), fn($query) => $query->whereNotIn('id', $productAttributeOptionIds))
                ->delete();
        });
    }
}
