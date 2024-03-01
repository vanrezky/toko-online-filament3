<?php

namespace App\Filament\Resources;

use App\Constants\Status;
use App\Constants\UploadPath;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
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
                    ]),


                Forms\Components\Section::make(__('Name & Product Category'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Product Name'))
                            ->helperText(__('Product name of at least 15 letters/characters.'))
                            ->minLength(15)
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label(__('Slug')),
                        Forms\Components\TextInput::make('code')
                            ->label(__('Product Code'))
                            ->helperText(__('Enter the unique code for the product and make sure it is not the same as another product.'))
                            ->required()
                            ->maxLength(255),
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
                                Status::DIGITAL_PRODUCT => __('Physical Product'),
                                Status::PHYSICAL_PRODUCT => __('Digital Product'),
                            ])
                            ->native(false)
                            ->default(false)
                            ->live(onBlur: true),
                        Forms\Components\TextInput::make('digital_url')
                            ->placeholder('https://urlweb.com/path/to/file/download.zip')
                            ->helperText(__('Enter the URL address for product access for buyers, such as the file download URL if the product is in the form of a file and the like.'))
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => $get('digital'))
                            ->required(fn (Get $get): bool => filled($get('digital')))

                    ])->inlineLabel(),
                Forms\Components\Section::make('Price & Stock')
                    ->schema([
                        Forms\Components\Select::make('warehouse_id')
                            ->label(__('Shipping Warehouse'))
                            ->helperText(str('Select the warehouse where the product will be shipped from, to change the warehouse can be seen in the **<a href="javascript:;">Warehouse Location</a>** menu.')->inlineMarkdown()->toHtmlString())
                            ->relationship('warehouse', 'name', fn (Builder $query): Builder => $query->active())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('stock')
                            ->helperText(__('Only enter numbers. e.g: 50'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('min_order')
                            ->helperText(__('Only enter numbers. e.g: 50'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('sale_price')
                            ->label(__('Price Before Discount'))
                            ->helperText(__('Normal price before discount. just enter the number only. e.g: 100000'))
                            ->numeric(),
                        Forms\Components\TextInput::make('price')
                            ->label(__('Normal Price'))
                            ->helperText(__('Normal After discount. just enter  the number only. e.g: 100000'))
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('afiliate_price')
                            ->label('Sales Commission Fee')
                            ->helperText(__('Sales commission given to the affiliator. just enter a number. e.g: 10000'))
                            ->numeric(),

                    ])->inlineLabel(),

                Forms\Components\Section::make('Description Product')
                    ->schema([
                        Forms\Components\TextInput::make('weight')
                            ->label(__('Product Weight (Gram)'))
                            ->helperText(__('Only enter numbers. e.g: 1000'))
                            ->required()
                            ->numeric(),
                        Forms\Components\RichEditor::make('description')
                            ->helperText(__('Add product descriptions to make it easier for buyers to understand the products being sold.'))
                            ->required()
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->columnSpanFull(),
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
                    ])->inlineLabel(),

                Forms\Components\Section::make('Product Variants')
                    ->schema([
                        Forms\Components\Split::make([
                            Forms\Components\TextInput::make('varian')
                                ->label('Variant')
                                ->placeholder('e.g: Colors')
                                // ->helperText('Add product variant options as needed, maximum 10 variants per product.')
                                ->maxLength(255)
                                ->live()
                                ->inlineLabel(),
                            Forms\Components\TextInput::make('subvarian')
                                ->label('Sub Variant')
                                ->placeholder('e.g: Sizes')
                                // ->helperText('Add product variant options as needed, maximum 10 variants per product.')
                                ->maxLength(255)
                                ->visible(fn (Get $get): bool => !empty($get('varian')))
                                ->inlineLabel(),
                        ])->from('lg'),
                        Forms\Components\TagsInput::make('varian_choices')
                            ->label('Variant Choices')
                            ->placeholder('Add new variant')
                            ->helperText('Add product variant options as needed, maximum 10 variants per product.')
                            ->separator(',')
                            ->reorderable()
                            ->visible(fn (Get $get): bool => !empty($get('varian'))),
                        Forms\Components\TagsInput::make('subvarian_choices')
                            ->label('Sub Variant Choices')
                            ->placeholder('Add sub new variant')
                            ->helperText('Add product sub variant options as needed, maximum 10 sub variants per product.')
                            ->separator(',')
                            ->reorderable()
                            ->visible(fn (Get $get): bool =>  !empty($get('varian')))
                    ])
            ]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
