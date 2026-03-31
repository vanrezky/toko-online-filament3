<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\View;


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
                            ->disabled(fn ($record) => $record !== null)
                            ->helperText('Unique identifier for this template (e.g., reset_password, payment_success)')
                            ->columnSpan(1),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(1),
                        Toggle::make('send_to_admin')
                            ->label('Send Copy to Admin')
                            ->default(false)
                            ->inline(false)
                            ->helperText('Send a copy of this email to admin emails configured in Settings > Notifications')
                            ->columnSpan(1),
                    ])->columns(2),

                Section::make('Header Settings')
                    ->schema([
                        TextInput::make('header_title')
                            ->label('Header Title')
                            ->maxLength(255)
                            ->helperText('Title shown in email header (e.g., "Pembayaran Berhasil")'),
                        ColorPicker::make('header_gradient')
                            ->label('Header Gradient Start')
                            ->helperText('Gradient color for email header. Leave empty for default.')
                            ->columnSpan(1),
                    ])->columns(2),

                Section::make('Email Content')
                    ->schema([
                        TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Email subject. Use {{placeholder}} for dynamic values.')
                            ->columnSpanFull()
                            ->maxLength(255),
                        View::make('filament.forms.placeholders'),

                        RichEditor::make('body')
                            ->required()
                            ->maxLength(65535)
                            ->helperText('Email body content. Use {{placeholder}} for dynamic values. Styles are applied automatically.')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'alignLeft',
                                'alignCenter',
                                'alignRight',
                                'orderedList',
                                'bulletList',
                                'link',
                            ])
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->badge(),
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
                Tables\Columns\IconColumn::make('send_to_admin')
                    ->label('Admin Copy')
                    ->boolean()
                    ->trueIcon('heroicon-o-bell')
                    ->falseIcon('heroicon-o-bell-slash'),
                TextColumn::make('updated_at')
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
        return [];
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
