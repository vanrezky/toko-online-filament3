<?php

namespace App\Filament\Resources;

use App\Constants\UploadPath;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\Pages\Profile;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\BalancesRelationManager;
use App\Filament\Resources\CustomerResource\RelationManagers\CustomerAddressRelationManager;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?string $slug = 'shop/customers';
    protected static ?int $navigationSort = 4;

    public Customer $record;



    // protected static ?string $navigationLabel = 'Customer';
    // protected static ?string $recordTitleAttribute = 'first_name';
    // protected static int $globalSearchResultsLimit = 10;

    // public static  function getGlobalSearchResultTitle(Model $record): string
    // {
    //     return $record->first_name . ' ' .  $record->last_name;
    // }

    // public static function getGloballySearchableAttributes(): array
    // {
    //     return ['first_name', 'last_name', 'departement.name'];
    // }

    // public static function getGlobalSearchResultDetails(Model $record): array
    // {
    //     return [
    //         'Country' => $record->country->name,
    //         'Departement' => $record->departement->name
    //     ];
    // }

    // public static function getGlobalSearchEloquentQuery(): Builder
    // {
    //     return parent::getGlobalSearchEloquentQuery()->with(['departement', 'country']);
    // }

    // public static function getGlobalSearchResultUrl(Model $record): string
    // {
    //     return EmployeeResource::getUrl('view', ['record' => $record]);
    // }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'info' : 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Profile Image')
                    ->image()
                    ->avatar()
                    ->directory(UploadPath::PROFILE_UPLOAD_PATH)
                    ->imageCropAspectRatio('1:1')
                    ->imageEditorAspectRatios([
                        '1:1',
                    ])
                    ->rules(['nullable', 'mimes:png,jpg,jpeg', 'max:1024'])
                    ->columnSpanFull()
                    ->alignCenter()
                    ->helperText(__('Ratio Is 1:1. Maximum size is 1MB')),

                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('first_name')
                        ->placeholder(__('e.g: ') . 'John')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('last_name')
                        ->placeholder(__('e.g: ') . 'Smith')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('email')
                        ->placeholder(__('e.g: ') . 'Johnsmith@example.com')
                        ->email()
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('username')
                        ->placeholder(__('e.g: ') . 'johnsmith')
                        ->minLength(6)
                        ->maxLength(15)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('phone')
                        ->placeholder(__('e.g: ') . '+6281234567890')
                        ->tel()
                        ->maxLength(20),
                    Forms\Components\TextInput::make('balance')
                        ->required()
                        ->default(0.00)
                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->rules([secure_password()])
                        ->required()
                        ->same('confirm_password')
                        ->minLength(8)
                        ->maxLength(20)
                        ->visible(fn (string $operation): bool  => $operation === 'create'),
                    Forms\Components\TextInput::make('confirm_password')
                        ->password()
                        ->required()
                        ->maxLength(255)
                        ->visible(fn (string $operation): bool  => $operation === 'create'),
                ])->columnSpanFull(),

                Forms\Components\Hidden::make('email_verified_at')
                    ->default(now())->dehydrated(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo_url')
                    ->label('Photo')
                    ->circular()
                    ->extraImgAttributes([
                        'class' => 'border border-gray-200',
                        'lazy' => 'loading'
                    ]),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->icon(fn (Customer $record): string => $record->email_verified_at ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->iconColor(fn (Customer $record): string => $record->email_verified_at ? 'success' : 'warning')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('balance')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('distributorLevel.name')
                    ->label(__('Level'))
                    ->default('None')
                    ->badge()
                    ->color('info'),
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
                TernaryFilter::make('email_verified_at')
                    ->label('Email verification')
                    ->nullable()
                    ->placeholder(__('All'))
                    ->trueLabel(__('Verified'))
                    ->falseLabel(__('Not verified'))
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Action::make('verifed_email')
                        ->label(__('Verification Email'))
                        ->icon('heroicon-o-at-symbol')
                        ->requiresConfirmation()
                        ->action(function (Customer $record) {
                            $record->update(['email_verified_at' => now()]);
                            return Notification::make()
                                ->title(__('Email has been verified  successfully'))
                                ->success()
                                ->send();
                        })
                        ->color('danger')
                        ->visible(fn (Customer $record): bool => $record->email_verified_at === null),
                    Tables\Actions\ViewAction::make()->label('Profile')
                        ->color('info'),
                    Tables\Actions\EditAction::make(),
                ])->tooltip(__('Actions'))

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
            CustomerAddressRelationManager::class,
            BalancesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            // 'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\Profile::route('/{record}/profile'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
