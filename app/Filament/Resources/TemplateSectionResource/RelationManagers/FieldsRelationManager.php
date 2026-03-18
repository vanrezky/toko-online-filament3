<?php

namespace App\Filament\Resources\TemplateSectionResource\RelationManagers;

use App\Models\TemplateSectionField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    protected static ?string $title = 'Field Definitions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->required()
                            ->alphaDash()
                            ->maxLength(80)
                            ->helperText('Unique field key, e.g. title, subtitle, image_url, button_link')
                            ->unique(
                                table: 'template_section_fields',
                                column: 'key',
                                ignoreRecord: true,
                                modifyRuleUsing: function ($rule, RelationManager $livewire) {
                                    return $rule->where('section_id', $livewire->getOwnerRecord()->id);
                                }
                            ),

                        Forms\Components\TextInput::make('label')
                            ->required()
                            ->maxLength(120),

                        Forms\Components\Select::make('type')
                            ->required()
                            ->options(TemplateSectionField::fieldTypes())
                            ->default('text')
                            ->reactive(),

                        Forms\Components\TextInput::make('placeholder')
                            ->nullable()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('default_value')
                            ->nullable()
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\KeyValue::make('options')
                            ->label('Select Options (key → label)')
                            ->nullable()
                            ->columnSpanFull()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'select'),

                        Forms\Components\Toggle::make('is_required')
                            ->label('Required')
                            ->default(false),

                        Forms\Components\TextInput::make('order_priority')
                            ->label('Order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->defaultSort('order_priority', 'asc')
            ->reorderable('order_priority')
            ->columns([
                Tables\Columns\TextColumn::make('order_priority')
                    ->label('#')
                    ->width(40)
                    ->sortable(),

                Tables\Columns\TextColumn::make('key')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('label')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'text',
                        'warning' => 'image',
                        'info'    => 'url',
                        'success' => 'toggle',
                        'danger'  => 'richtext',
                    ]),

                Tables\Columns\IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function (TemplateSectionField $record) {
                        $maxOrder = TemplateSectionField::where('section_id', $record->section_id)
                            ->where('id', '!=', $record->id)
                            ->max('order_priority') ?? 0;
                        $record->update(['order_priority' => $maxOrder + 1]);
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
}
