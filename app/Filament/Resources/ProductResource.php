<?php

namespace App\Filament\Resources;

use App\Constants\Status;
use App\Constants\UploadPath;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\RelationManagers\ProductVariantRelationManager;
use App\Filament\Resources\Schema\MetaSchema;
use App\Filament\Resources\Schema\TitleSchema;
use App\Models\Reseller;
use App\Models\Product;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?string $slug = 'shop/products';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Images')
                    ->description(__('Drag the image to the top to make it the main image'))
                    ->schema([
                        Forms\Components\FileUpload::make('images')
                            ->image()
                            ->imageEditor()
                            ->required()
                            ->hiddenLabel()
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(5)
                            ->imageCropAspectRatio('1:1')
                            ->imageEditorAspectRatios([
                                '1:1'
                            ])
                            ->downloadable()
                            ->directory(UploadPath::IMAGES_UPLOAD_PATH)
                            ->optimize('webp')
                            ->panelLayout('grid'),
                    ])->collapsible()->collapsed(fn(string $context): bool => $context === 'edit'),
                Tabs::make()
                    ->schema([
                        Tabs\Tab::make('Name & Description')
                            ->schema([
                                TitleSchema::title('name')
                                    ->label(__('Product Name'))
                                    ->hiddenLabel()
                                    ->placeholder('Product Name')
                                    ->helperText(__('Product name of at least 15 letters/characters.'))
                                    ->minLength(5)
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->id('product-name')
                                    ->extraInputAttributes(['class' => 'column-title'], true),
                                Forms\Components\RichEditor::make('description')
                                    ->hiddenLabel()
                                    ->placeholder('Product Description')
                                    ->helperText(__('Add product descriptions to make it easier for buyers to understand the products being sold.'))
                                    ->required()
                                    ->string()
                                    ->disableToolbarButtons([
                                        'attachFiles',
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('SEO')
                            ->schema([
                                TitleSchema::slug()
                                    ->required()
                                    ->maxLength(255)
                                    ->required()
                                    ->columnSpanFull(),
                                TitleSchema::hidden(),
                                MetaSchema::get(),
                            ])
                    ])->columnSpanFull(),

                Forms\Components\Section::make(__('Code & Product Category'))
                    ->schema(
                        [
                            Forms\Components\TextInput::make('code')
                                ->label(__('Product Code'))
                                ->helperText(__('Enter the unique code for the product and make sure it is not the same as another product.'))
                                ->required()
                                ->maxLength(20)
                                ->columnSpanFull()
                                ->unique(column: 'code', ignoreRecord: true)
                                ->maxLength('20'),
                            Forms\Components\Select::make('category_id')
                                ->label(__('Product Category'))
                                ->helperText(__('Select the category that corresponds to the product.'))
                                ->relationship('category', 'name', fn(Builder $query): Builder => $query->active())
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpanFull(),
                            Forms\Components\Select::make('digital')
                                ->label(__('Product Type'))
                                ->helperText(__('Determine the type of product whether it is a physical or digital product.'))
                                ->required()
                                ->options([
                                    Status::PHYSICAL_PRODUCT => __('Physical Product'),
                                    Status::DIGITAL_PRODUCT => __('Digital Product'),
                                ])
                                ->native(false)
                                ->default(Status::PHYSICAL_PRODUCT)
                                ->live(onBlur: true)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('digital_url')
                                ->placeholder('https://urlweb.com/path/to/file/download.zip')
                                ->helperText(__('Enter the URL address for product access for buyers, such as the file download URL if the product is in the form of a file and the like.'))
                                ->columnSpanFull()
                                ->visible(fn(Get $get): bool => $get('digital'))
                                ->required(fn(Get $get): bool => $get('digital'))
                                ->url()
                                ->columnSpanFull()
                        ]
                    )->inlineLabel()->columns(2),

                Forms\Components\Section::make('Other Settings Product')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Product Tags')
                            ->placeholder(__('e.g: electronics, phone, laptop'))
                            ->separator(','),
                        Forms\Components\Select::make('is_active')
                            ->label('Product Status')
                            ->helperText(__('Publish the product or save it as a draft.'))
                            ->options(self::getStatusOptions())
                            ->default(Status::ACTIVE)
                            ->native(false)
                            // ->canSelectPlaceholder(false)
                            ->required(),
                    ]),

                Forms\Components\Section::make('Inventory')
                    ->schema([
                        Forms\Components\TextInput::make('weight')
                            ->rules('nullable|numeric')
                            ->label(__('Product Weight (Gram)'))
                            ->helperText(__('Only enter numbers. e.g: 1000'))
                            ->visible(fn(Get $get): bool => !$get('digital'))
                            ->required(fn(Get $get): bool => !$get('digital'))
                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
                        Forms\Components\Select::make('warehouse_id')
                            ->label(__('Shipping Warehouse'))
                            ->helperText(str(__('Select the warehouse where the product will be shipped from, to change the warehouse can be seen in the **<a href="/admin/setting/warehouse" target="_blank">Warehouse Location</a>** menu.'))->inlineMarkdown()->toHtmlString())
                            ->relationship('warehouse', 'name', fn(Builder $query): Builder => $query->active())
                            ->searchable()
                            ->preload()
                            ->visible(fn(Get $get): bool => !$get('digital'))
                            ->required(fn(Get $get): bool => !$get('digital'))
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('stock')
                            ->rules('nullable|numeric')
                            ->helperText(__('Only enter numbers. e.g: 50'))
                            ->required()
                            ->default(1)
                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0)
                            ->live(),
                        Forms\Components\TextInput::make('security_stock')
                            ->rules('nullable|numeric')
                            ->helperText(__('The safety stock is the limit stock for your products which alerts you if the product stock will soon be out of stock.'))
                            ->required()
                            ->default(0)
                            ->maxValue(fn(Get $get) => $get('stock'))
                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
                    ])->inlineLabel(),
                Forms\Components\Section::make('Price')
                    ->label(__('Price'))
                    ->schema([
                        Forms\Components\TextInput::make('min_order')
                            ->label(__('Minimal Order'))
                            ->rules('nullable|numeric')
                            ->helperText(__('Only enter numbers. e.g: 50'))
                            ->required()
                            ->default(1)
                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0)
                            ->maxValue(fn(Get $get) => $get('stock')),
                        Forms\Components\TextInput::make('sale_price')
                            ->rules('nullable|numeric')
                            ->label(__('Price Before Discount'))
                            ->helperText(__('Normal price before discount. just enter the number only. e.g: 100000'))
                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
                        Forms\Components\TextInput::make('price')
                            ->rules('numeric')
                            ->label(__('Normal Price'))
                            ->helperText(__('Normal After discount. just enter  the number only. e.g: 100000'))
                            ->required()
                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
                        Forms\Components\TextInput::make('afiliate_price')
                            ->rules('nullable|numeric')
                            ->label('Sales Commission Fee')
                            ->helperText(__('Sales commission given to the affiliator. just enter a number. e.g: 10000'))
                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
                    ])->inlineLabel(),

                Forms\Components\Section::make('Product Variants')
                    ->description(__('Set the product variants price'))
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('variant')->placeholder('e.g: Color')->label(__('Variant Title')),
                            Forms\Components\TextInput::make('sub_variant')->placeholder('e.g: Size')->label(__('Sub Variant Title'))
                        ])->columns(2),
                        Forms\Components\TagsInput::make('variants')
                            ->live()
                            ->inlineLabel()
                            ->label(fn() => str(sprintf(
                                '%s<br/><span class="italic font-light text-gray-500 dark:text-gray-400">%s</span>',
                                __('Variants'),
                                __('Maximum 5 variants')
                            ))->toHtmlString())
                            ->placeholder(__('Enter variants'))
                            ->helperText(__('Be careful when deleting an item, as the data cannot be recovered once saved. Please refresh the page to restore the data.'))
                            ->afterStateUpdated(function (Set $set, Get $get, string $context) {
                                self::updateProductVariants($set, $get, $context);
                            })
                            ->rules([
                                fn(): Closure => function (string $attribute, $value, Closure $fail) {
                                    $maxVariants = 5;
                                    if (count($value) > $maxVariants) {
                                        $fail('You can only add a maximum of ' . $maxVariants . ' variants.');
                                    }
                                },
                            ]),
                        Forms\Components\TagsInput::make('sub_variants')
                            ->live()
                            ->inlineLabel()
                            ->label(fn() => str(sprintf(
                                '%s<br/><span class="italic font-light text-gray-500 dark:text-gray-400">%s</span>',
                                __('Sub Variants'),
                                __('Maximum 5 sub variants')
                            ))->toHtmlString())
                            ->afterStateUpdated(function (Set $set, Get $get, string $context) {
                                self::updateProductVariants($set, $get, $context);
                            })
                            ->disabled(fn(Get $get): bool => empty($get('variants')))
                            ->placeholder(fn(Get $get): string => empty($get('variants')) ? __('Please input a variant first') : __('Enter sub variants'))
                            ->helperText(__('Be careful when deleting an item, as the data cannot be recovered once saved. Please refresh the page to restore the data.')),
                        Repeater::make('product_variants')
                            ->model('product_variants')
                            ->hiddenLabel()
                            ->schema([
                                Forms\Components\Hidden::make('variant'),
                                Forms\Components\Hidden::make('sub_variant'),
                                Forms\Components\Hidden::make('variant_name'),
                                Forms\Components\Hidden::make('sku'),
                                Forms\Components\TextInput::make('price')
                                    ->label('Price')
                                    ->numeric()
                                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0)
                                    ->required()
                                    ->suffixIcon('heroicon-m-banknotes'),
                                Forms\Components\TextInput::make('stock')
                                    ->label('Stock')
                                    ->numeric()
                                    ->required()
                                    ->suffixIcon('heroicon-m-cube'),
                                Forms\Components\FileUpload::make('image')
                                    ->image()
                                    ->imageEditor()
                                    ->hiddenLabel()
                                    ->imageCropAspectRatio('1:1')
                                    ->imageEditorAspectRatios(['1:1'])
                                    ->downloadable()
                                    ->directory(UploadPath::IMAGES_UPLOAD_PATH)
                                    ->optimize('webp'),
                            ])
                            ->reactive()
                            ->afterStateHydrated(function (Forms\Components\Repeater $component, Get $get, string $context) {
                                $data = self::mapProductVariants($get, $context);
                                $component->state($data);
                            })
                            ->itemLabel(fn(array $state): ?string => self::getVariantLabel($state))
                            ->collapsible()
                            ->addActionLabel('Add Variant')
                            ->reorderable(false)
                            ->deletable(false)
                            ->addable(false)
                            ->columns(3)
                            ->grid(['lg' => 2])
                    ]),

                Forms\Components\Section::make('Reseller Price')
                    ->description(__('Set prices based on reseller level'))
                    ->schema([
                        Forms\Components\Repeater::make('resellerPrices')
                            ->relationship('resellerPrices')
                            ->hiddenLabel()
                            ->reorderable(false)
                            // ->collapsible()
                            ->deleteAction(function (Action $action) {
                                $action->requiresConfirmation();
                                // ->action(function (array $arguments, Repeater $component) {
                                //     $itemData = $component->getItemState($arguments['item']);
                                // });
                            })
                            ->defaultItems(0)
                            ->schema([
                                Forms\Components\Select::make('reseller_id')
                                    ->label(__('Reseller Level'))
                                    ->options(Reseller::active()->get()->pluck('name_level', 'id')->toArray())
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->hintAction(
                                        Action::make('wholesale')
                                            ->icon('heroicon-m-currency-dollar')
                                            ->label('Add wholesale')
                                            ->form([
                                                Forms\Components\Repeater::make('wholesales')
                                                    ->default(fn($record): array => $record ? $record->wholesales->toArray() : [])
                                                    ->schema(self::getwholesalesSchema())
                                                    ->hiddenLabel()
                                                    ->grid(['lg' => 2])

                                            ])
                                            ->action(fn(array $data, $record) => self::setActionWholesales($data, $record))
                                            ->visible(fn($record): bool => !empty($record))
                                    ),

                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),

                            ])->grid(['md' => 2]),

                    ])->compact()->collapsible()->collapsed(),
                Forms\Components\Section::make('Wholesales')
                    ->description(__('Set prices based on wholesale prices'))
                    ->schema([
                        Forms\Components\Repeater::make('wholesales')
                            ->relationship('wholesales', fn(Builder $query): Builder => $query->whereNull('reseller_id'))
                            ->reorderable(false)
                            ->hiddenLabel()
                            // ->collapsible()
                            ->deleteAction(
                                fn(Action $action) => $action->requiresConfirmation(),
                            )
                            ->cloneable()
                            ->defaultItems(0)
                            ->schema(self::getWholesalesSchema())
                            ->grid(['xl' => 2])
                    ])

                    ->compact()->collapsible()->collapsed(),

                Forms\Components\Section::make('FAQs')
                    ->description(__('Set general questions and answers of product'))
                    ->schema([
                        Forms\Components\Repeater::make('faqs')
                            ->relationship('faqs')
                            ->reorderable(true)
                            ->hiddenLabel()
                            ->defaultItems(0)
                            ->schema([
                                Forms\Components\TextInput::make('question')
                                    ->label(__('Question'))
                                    ->required(),
                                Forms\Components\Textarea::make('answer')
                                    ->label(__('Answer'))
                                    ->required(),
                            ])

                    ])

                    ->compact()->collapsible()->collapsed(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->square()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(size: 'lg')
                    ->extraImgAttributes(['loading' => 'lazy']),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    // ->limit('50')
                    ->extraAttributes(['class' => 'text-wrap']),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('Product Code'))
                    ->badge()
                    ->color('warning')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('afiliate_price')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('min_order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('variation')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('subvariation')
                    ->toggleable(isToggledHiddenByDefault: true),
                self::getIsFeaturedColumn(),
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
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options(self::getStatusOptions()),
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', titleAttribute: 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->native(false),
                        DatePicker::make('created_until')->native(false),
                    ])->columns(2)
                    ->indicateUsing(function (array $data): ?string {
                        $text = null;
                        if ($data['created_from']) {
                            $text = 'Created at ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                            if ($data['created_until']) {
                                $text .= ' - ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                            }
                        }

                        return $text;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
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
            // ProductVariantRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            // 'view' => '',
        ];
    }

    public static function getIsFeaturedColumn()
    {
        if (self::shouldCanUpdate()) {
            return Tables\Columns\ToggleColumn::make('is_active')
                ->label(__('Published'))
                ->afterStateUpdated(fn() => notification(__('Published status updated successfully'), 'success'));
        };

        return Tables\Columns\IconColumn::make('is_active')->boolean()->label(__('Published'));
    }

    public static function shouldCanUpdate(): bool
    {
        return auth()->user()->can('update_product');
    }

    public static function getwholesalesSchema(): array
    {
        return [
            Forms\Components\TextInput::make('min_qty')
                ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0)
                ->required()
                ->default(0)
                ->distinct(),
            Forms\Components\TextInput::make('price')
                ->label(__('Price per item'))
                ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0)
                ->required()
                ->default(0)
                ->distinct()
        ];
    }

    public static function setActionWholesales($data, $record): Notification
    {
        try {
            $wholesales = [];
            foreach ($data['wholesales'] as $key => $who) {
                $wholesales[] = [
                    'reseller_id' => $record->reseller_id,
                    'product_id' => $record->product_id,
                    'min_qty' => $who['min_qty'],
                    'price' => $who['price'],
                ];
            }

            $record->wholesales()->delete();
            $record->wholesales()->createMany($wholesales);
            return notification(__('Wholesale price submited successfully'), 'success');
        } catch (\Exception $e) {
            return notification(__('Failed to create wholesale price'), 'danger');
        }
    }

    protected static function updateProductVariants(Set $set, Get $get, string $context)
    {
        $set('product_variants', self::mapProductVariants($get, $context));
    }

    public static function mapProductVariants(Get $get, string $context): array
    {
        $variants = $get('variants') ?? [];
        $subVariants = $get('sub_variants') ?? [];
        $oldFormRepeater = $get('product_variants') ?? [];

        $productVariants = [];

        // Membuat key untuk memudahkan pencocokan antara variant dan sub_variant
        $oldVariantsKeys = [];
        foreach ($oldFormRepeater as $item) {
            $oldVariantsKeys[$item['variant'] . '_' . $item['sub_variant']] = $item;
        }

        if (empty($subVariants)) {
            foreach ($variants as $variant) {
                $key = $variant . '_';  // Sub-variant null
                if (isset($oldVariantsKeys[$key])) {
                    $old = $oldVariantsKeys[$key];
                    $productVariants[] = self::getVariantProductPayload($variant, null, $old['price'], $old['stock'], $old['image'], $old['sku'], $old['id'] ?? null);
                } else {
                    $productVariants[] = self::getVariantProductPayload($variant, null, floatval($get('price')));
                }
            }
        } else {
            foreach ($variants as $variant) {
                foreach ($subVariants as $sub) {
                    $key = $variant . '_' . $sub;
                    if (isset($oldVariantsKeys[$key])) {
                        $old = $oldVariantsKeys[$key];
                        $productVariants[] = self::getVariantProductPayload($variant, $sub, $old['price'], $old['stock'], $old['image'], $old['sku'], $old['id'] ?? null);
                    } else {
                        $productVariants[] = self::getVariantProductPayload($variant, $sub, floatval($get('price')));
                    }
                }
            }
        }

        return $productVariants;
    }

    public static function getVariantProductPayload(string $variant, ?string $subVariant = null, float $price, int $stock = 1, $image = null, ?string $sku = null, $id  = null)
    {
        return [
            'id' => $id,
            'variant' => $variant,
            'sub_variant' => $subVariant,
            'variant_name' => self::getVariantLabel(['variant' => $variant, 'sub_variant' => $subVariant]),
            'price' => $price,
            'stock' => $stock,
            'image' => $image,
            'sku' => $sku ?? uniqid()
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            Status::INACTIVE => 'Draft',
            Status::ACTIVE => 'Published',
        ];
    }

    public static function getVariantLabel(array $state): ?string
    {
        return trim(sprintf(
            '%s%s',
            !empty($state['variant']) ? "{$state['variant']}" : '',
            !empty($state['sub_variant']) ? " - {$state['sub_variant']}" : ''
        ));
    }
}
