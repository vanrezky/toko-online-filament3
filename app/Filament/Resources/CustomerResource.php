<?php

namespace App\Filament\Resources;

use App\Constants\UploadPath;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'USER';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory(UploadPath::PROFILE_UPLOAD_PATH)
                    ->imageEditorAspectRatios([
                        '1:1',
                    ])->columnSpanFull(),

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
                        ->required()
                        ->same('confirm_password')
                        ->maxLength(255)
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
                Tables\Columns\ImageColumn::make('image')
                    ->square(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->icon(fn (Customer $record): string => $record->email_verified_at ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->iconColor(fn (Customer $record): string => $record->email_verified_at ? 'success' : 'warning')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('email_verified_at')
                //     ->dateTime()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('username')
                //     ->default('-')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('balance')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCustomers::route('/'),
            // 'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
