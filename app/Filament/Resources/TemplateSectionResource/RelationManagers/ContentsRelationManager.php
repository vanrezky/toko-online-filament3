<?php

namespace App\Filament\Resources\TemplateSectionResource\RelationManagers;

use App\Models\TemplateSectionField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ContentsRelationManager extends RelationManager
{
    protected static string $relationship = 'contents';

    protected static ?string $title = 'Content Values';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('field_id')
                            ->label('Field')
                            ->options(function (RelationManager $livewire) {
                                return TemplateSectionField::where('section_id', $livewire->getOwnerRecord()->id)
                                    ->orderBy('order_priority')
                                    ->pluck('label', 'id');
                            })
                            ->required()
                            ->unique(
                                table: 'template_section_contents',
                                column: 'field_id',
                                ignoreRecord: true,
                                modifyRuleUsing: function ($rule, RelationManager $livewire) {
                                    return $rule->where('section_id', $livewire->getOwnerRecord()->id);
                                }
                            )
                            ->reactive()
                            ->searchable(),

                        Forms\Components\Textarea::make('value')
                            ->label('Value')
                            ->nullable()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\KeyValue::make('meta')
                            ->label('Meta / Extra Data')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('field_id')
            ->columns([
                Tables\Columns\TextColumn::make('field.key')
                    ->label('Field Key')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('field.label')
                    ->label('Field Label'),

                Tables\Columns\TextColumn::make('field.type')
                    ->label('Type')
                    ->badge(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->value),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
