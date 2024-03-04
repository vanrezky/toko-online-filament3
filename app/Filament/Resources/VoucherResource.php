<?php

namespace App\Filament\Resources;

use App\Constants\Status;
use App\Enums\VoucherDiscountType;
use App\Enums\VoucherProductType;
use App\Enums\VoucherType;
use App\Filament\Resources\VoucherResource\Pages;
use App\Filament\Resources\VoucherResource\RelationManagers;
use App\Models\Voucher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-scissors';
    protected static ?string $navigationLabel = 'Voucher Promo';
    protected static ?string $navigationGroup = 'PROMO';
    protected static ?string $slug = 'promo/voucher';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Group::make([
                    Forms\Components\Section::make(__('Voucher Information'))
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label(__('Voucher Name'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Textarea::make('description')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\Group::make([
                                Forms\Components\Select::make('voucher_type')
                                    ->options([
                                        VoucherType::PRODUCT->value => __('Voucher For Product'),
                                        VoucherType::SHIPPING_COST->value => __('Voucher For Shipping Cost')
                                    ])
                                    ->default(VoucherType::PRODUCT->value)
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('product_type', $state == VoucherType::PRODUCT->value ? VoucherProductType::ALL_PRODUCT->value : VoucherProductType::PHYSICAL_PRODUCT->value);
                                    }),
                                Forms\Components\Select::make('discount_type')
                                    ->options([
                                        VoucherDiscountType::FIXED->value => __('Fixed'),
                                        VoucherDiscountType::PERCENTAGE->value => __('Percentage'),
                                    ])
                                    ->default(VoucherDiscountType::FIXED->value)
                                    ->required()
                                    ->live(),
                                Forms\Components\Select::make('product_type')
                                    ->options([
                                        VoucherProductType::ALL_PRODUCT->value => __('All Product'),
                                        VoucherProductType::PHYSICAL_PRODUCT->value => __('Physical Product'),
                                        VoucherProductType::DIGITAL_PRODUCT->value => __('Digital Product'),
                                    ])
                                    ->default(VoucherProductType::ALL_PRODUCT->value)
                                    ->disabled(fn (Get $get) => $get('voucher_type') == VoucherType::SHIPPING_COST->value)
                                    ->required(),
                            ])->columns(3),
                            Forms\Components\TextInput::make('code')
                                ->label(__('Voucher Code'))
                                ->required()
                                ->maxLength(10),
                            Forms\Components\Group::make([
                                Forms\Components\TextInput::make('discount_min')
                                    ->label(__('Minimum Order Total'))
                                    ->required()
                                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),

                                Forms\Components\TextInput::make('discount')
                                    ->label(fn (Get $get) => $get('discount_type') == VoucherDiscountType::FIXED->value ? __('Discount/Price Cut Value') : __('Discount Percentage'))
                                    ->required()
                                    ->rules('numeric')
                                    ->maxLength(fn (Get $get) => $get('discount_type') == VoucherDiscountType::PERCENTAGE->value ? 100 : null)
                                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
                                Forms\Components\TextInput::make('discount_max')
                                    ->required(fn (Get $get): bool => $get('discount_type') == VoucherDiscountType::PERCENTAGE->value)
                                    ->visible(fn (Get $get): bool => $get('discount_type') == VoucherDiscountType::PERCENTAGE->value)
                                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
                            ])->columns(3)
                        ]),

                ])->columnSpan(2),
                Forms\Components\Group::make([
                    Forms\Components\Section::make(__('Validity period'))
                        ->schema([
                            Forms\Components\DatePicker::make('start_at')
                                ->required()
                                ->minDate(
                                    function (string $operation, ?string $state) {
                                        if ($operation === 'edit') {
                                            return $state;
                                        }

                                        return now()->format('Y-m-d');
                                    }
                                )
                                ->native(false)
                                ->live(),
                            Forms\Components\DatePicker::make('end_at')
                                ->required()
                                ->native(false)
                                ->minDate(fn (Get $get): ?string => $get('start_at')),
                        ]),
                    Forms\Components\Section::make(__('Other Information'))
                        ->schema([
                            Forms\Components\FileUpload::make('image')
                                ->image()
                                ->columnSpanFull(),
                            Forms\Components\Select::make('category_id')
                                ->placeholder(__('All Category'))
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload(),
                            Forms\Components\Toggle::make('is_public')
                                ->label(__('Is for public'))
                                ->default(true)
                                ->required(),
                            Forms\Components\Toggle::make('is_active')
                                ->label('Is active')
                                ->default(true)
                                ->required(),
                            Forms\Components\TextInput::make('max_user_used')
                                ->label(__('Max usage per user'))
                                ->required()
                                ->numeric()
                                ->default(1),

                        ]),
                ])->columnSpan(1),

                Forms\Components\Hidden::make('user_id')->default(auth()->user()->id)->dehydrated(),


            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('category.name')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('user.name')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Voucher Name'))
                    ->icon(fn (Voucher $record): string => $record->is_public ? 'heroicon-o-eye' : 'heroicon-o-eye-slash')
                    ->iconColor(fn (Voucher $record): string => $record->is_public ? 'success' : 'warning')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('Voucher Code'))
                    ->weight('bold')
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('voucher_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('discount_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('product_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('discount')
                    ->money(currency: '   ')
                    ->badge()
                    ->icon(fn (Voucher $record): string => match ($record->discount_type) {
                        VoucherDiscountType::FIXED => 'heroicon-o-tag',
                        VoucherDiscountType::PERCENTAGE => 'heroicon-o-receipt-percent',
                    })
                    ->color(fn (Voucher $record): string => $record->discount_type->value == VoucherDiscountType::FIXED->value ? 'success' : 'warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('validity_period')
                    ->sortable(
                        query: fn (string $direction, $query) => $query->orderBy('start_at', $direction)
                    ),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->afterStateUpdated(function ($record, $state) {
                        return Notification::make()
                            ->title(__('Activation status updated successfully'))
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
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
}
