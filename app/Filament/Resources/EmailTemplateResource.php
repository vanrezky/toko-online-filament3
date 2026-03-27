<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Master';

    protected static ?string $navigationLabel = 'Email Templates';

    protected static ?string $modelLabel = 'Email Template';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'email-templates';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Template Information')
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignorable: fn ($record) => $record)
                            ->disabled(fn ($record) => $record !== null),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Select::make('placeholders')
                            ->multiple()
                            ->options([
                                'customer_name' => 'Customer Name',
                                'email' => 'Email',
                                'order_id' => 'Order ID',
                                'order_total' => 'Order Total',
                                'payment_url' => 'Payment URL',
                                'payment_method' => 'Payment Method',
                                'transaction_date' => 'Transaction Date',
                                'expiry_time' => 'Expiry Time',
                                'reset_url' => 'Reset URL',
                                'expiry_minutes' => 'Expiry Minutes',
                                'old_status' => 'Old Status',
                                'new_status' => 'New Status',
                                'tracking_number' => 'Tracking Number',
                                'courier_name' => 'Courier Name',
                                'reseller_name' => 'Reseller Name',
                                'product_list' => 'Product List',
                                'shipping_address' => 'Shipping Address',
                                'order_date' => 'Order Date',
                                'website_name' => 'Website Name',
                            ])
                            ->afterStateUpdated(function (Set $set, array $state) {
                                $placeholders = collect($state)
                                    ->map(fn ($val) => '{{'.$val.'}}')
                                    ->implode(', ');
                                $set('placeholders_display', $placeholders);
                            }),
                        TextInput::make('placeholders_display')
                            ->label('Placeholders Reference')
                            ->readOnly()
                            ->helperText('Copy these placeholders to use in subject or body'),
                    ])->columns(2),

                Section::make('Email Content')
                    ->schema([
                        TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Use placeholders like {{customer_name}}, {{order_id}}, etc.'),
                        RichEditor::make('body')
                            ->required()
                            ->maxLength(65535)
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'alignLeft', 'alignCenter', 'alignRight', 'alignJustify',
                                'orderedList', 'bulletList',
                                'link', 'blockquote', 'codeBlock',
                                'clean',
                            ])
                            ->helperText('HTML is supported. Use placeholders like {{customer_name}}, {{order_id}}, etc.'),
                    ]),

                Section::make('Settings')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->limit(50),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->query(fn ($query) => $query->where('is_active', true))
                    ->toggle(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
