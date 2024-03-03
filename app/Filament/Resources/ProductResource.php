<?php

namespace App\Filament\Resources;

use App\Constants\Status;
use App\Constants\UploadPath;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\RelationManagers\ProductVariantRelationManager;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Tabs\Tab;
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
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Product List';
    protected static ?string $navigationGroup = 'PRODUCT';
    protected static ?string $slug = 'product/product';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([



                Group::make([
                    Forms\Components\Section::make('Name & Description Product')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label(__('Product Name'))
                                ->hiddenLabel()
                                ->placeholder('Product Name')
                                ->helperText(__('Product name of at least 15 letters/characters.'))
                                ->minLength(5)
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull()
                                ->id('product-name')
                                ->live(onBlur: true)
                                ->extraInputAttributes(['class' => 'font-bold py-10'], true)
                                ->afterStateUpdated(function (Set $set, Get $get, ?string $state, string $operation) {

                                    if ($operation == 'edit') {
                                        return;
                                    }
                                    if (!$get('is_slug_changed_manually') && filled($state)) {
                                        $set('slug', Str::slug($state));
                                    }

                                    $set('slug', Str::slug($state));
                                }),
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
                    Forms\Components\Section::make(__('Code & Product Category'))
                        ->schema(
                            [
                                Forms\Components\TextInput::make('code')
                                    ->label(__('Product Code'))
                                    ->helperText(__('Enter the unique code for the product and make sure it is not the same as another product.'))
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('category_id')
                                    ->label(__('Product Category'))
                                    ->helperText(__('Select the category that corresponds to the product.'))
                                    ->relationship('category', 'name', fn (Builder $query): Builder => $query->active())
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
                                    ->visible(fn (Get $get): bool => $get('digital'))
                                    ->required(fn (Get $get): bool => $get('digital'))
                                    ->url()
                                    ->columnSpanFull()
                            ]
                        )->inlineLabel()->columns(2),
                    Forms\Components\Section::make('Price & Stock')
                        ->schema([
                            Forms\Components\TextInput::make('weight')
                                ->rules('nullable|numeric')
                                ->label(__('Product Weight (Gram)'))
                                ->helperText(__('Only enter numbers. e.g: 1000'))
                                ->visible(fn (Get $get): bool => !$get('digital'))
                                ->required(fn (Get $get): bool => !$get('digital'))
                                ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
                            Forms\Components\Select::make('warehouse_id')
                                ->label(__('Shipping Warehouse'))
                                ->helperText(str('Select the warehouse where the product will be shipped from, to change the warehouse can be seen in the **<a href="javascript:;">Warehouse Location</a>** menu.')->inlineMarkdown()->toHtmlString())
                                ->relationship('warehouse', 'name', fn (Builder $query): Builder => $query->active())
                                ->searchable()
                                ->preload()
                                ->visible(fn (Get $get): bool => !$get('digital'))
                                ->required(fn (Get $get): bool => !$get('digital'))
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('stock')
                                ->rules('nullable|numeric')
                                ->helperText(__('Only enter numbers. e.g: 50'))
                                ->required()
                                ->default(1)
                                ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),

                            Forms\Components\TextInput::make('min_order')
                                ->rules('nullable|numeric')
                                ->helperText(__('Only enter numbers. e.g: 50'))
                                ->required()
                                ->default(1)
                                ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
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
                ])->columnSpan(2),

                Group::make([
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
                                ->imagePreviewHeight('150')
                                ->directory(UploadPath::IMAGES_UPLOAD_PATH)
                        ])->collapsible(),
                    Forms\Components\Section::make('Other Settings Product')
                        ->schema([
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->rules(['alpha_dash'])
                                ->unique(ignoreRecord: true)
                                ->afterStateUpdated(function (Set $set) {
                                    $set('is_slug_changed_manually', true);
                                })
                                ->required()->columnSpanFull(),
                            Forms\Components\Hidden::make('is_slug_changed_manually')
                                ->default(false)
                                ->dehydrated(false),
                            Forms\Components\TagsInput::make('tags')
                                ->label('Product Tags')
                                ->placeholder('e.g: electronics, phone, laptop')
                                ->separator(','),
                            Forms\Components\Select::make('is_active')
                                ->label('Product Status')
                                ->helperText(__('Publish the product or save it as a draft.'))
                                ->options([
                                    Status::INACTIVE => __('Draft'),
                                    Status::ACTIVE => __('Published'),
                                ])
                                ->default(Status::ACTIVE)
                                ->native(false)
                                // ->canSelectPlaceholder(false)
                                ->required(),
                        ]),

                ])->columnSpan(1),


                Forms\Components\Section::make('Variant Product')
                    ->schema([
                        Forms\Components\TextInput::make('variant')
                            ->placeholder(__('e.g: Colors'))
                            ->nullable(),
                        Forms\Components\TextInput::make('sub_variant')
                            ->placeholder(__('e.g: Sizes'))
                            ->nullable(),

                        Forms\Components\Group::make([
                            Forms\Components\TagsInput::make('variants')
                                ->placeholder(__('e.g: Blue, Black, Red, Green, etc.'))
                                ->live()
                                ->afterStateUpdated(function (Set $set, ?array $state) {
                                })
                                ->nullable(),
                            Forms\Components\TagsInput::make('sub_variants')
                                ->placeholder(__('e.g: S, M, L, XL, etc.'))
                                ->nullable(),
                        ])->visible(fn (string $operation): bool => $operation == 'create')
                            ->columnSpanFull()
                    ])
                    ->columnSpanFull()->columns(2),


                // hidden value
                Forms\Components\Hidden::make('user_id')->dehydrated()

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
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
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
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Published')
                    ->afterStateUpdated(function ($record, $state) {
                        return Notification::make()
                            ->title(__('Published status updated successfully'))
                            ->success()
                            ->send();
                    }),
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
                    ->options([
                        Status::INACTIVE => 'Draft',
                        Status::ACTIVE => 'Published',
                    ]),
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
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })

            ], layout: FiltersLayout::AboveContent)
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
            ProductVariantRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
