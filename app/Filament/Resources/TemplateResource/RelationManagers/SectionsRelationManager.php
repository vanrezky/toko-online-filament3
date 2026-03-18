<?php

namespace App\Filament\Resources\TemplateResource\RelationManagers;

use App\Models\TemplateSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Sections';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Section Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(120),

                        Forms\Components\Select::make('type')
                            ->required()
                            ->options(TemplateSection::types())
                            ->searchable(),

                        Forms\Components\Textarea::make('description')
                            ->nullable()
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('icon')
                            ->nullable()
                            ->placeholder('heroicon-o-photo')
                            ->helperText('Heroicon name, e.g. heroicon-o-photo'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('order_priority', 'asc')
            ->reorderable('order_priority')
            ->columns([
                Tables\Columns\TextColumn::make('order_priority')
                    ->label('#')
                    ->width(40)
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary'   => 'hero',
                        'success'   => 'stories',
                        'warning'   => 'banner',
                        'info'      => 'gallery',
                        'danger'    => 'cta',
                        'secondary' => fn ($state) => !in_array($state, ['hero', 'stories', 'banner', 'gallery', 'cta']),
                    ])
                    ->formatStateUsing(fn (string $state) => TemplateSection::types()[$state] ?? $state),

                Tables\Columns\TextColumn::make('fields_count')
                    ->counts('fields')
                    ->label('Fields')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\Action::make('sort_sections')
                    ->label('Sort Sections')
                    ->icon('heroicon-o-arrows-up-down')
                    ->color('gray')
                    ->url(fn () => \App\Filament\Resources\TemplateSectionResource::getUrl(
                        'sort',
                        ['template' => $this->getOwnerRecord()->uuid]
                    )),

                Tables\Actions\CreateAction::make()
                    ->after(function (TemplateSection $record) {
                        // Set order_priority to last position
                        $maxOrder = TemplateSection::where('template_id', $record->template_id)
                            ->where('id', '!=', $record->id)
                            ->max('order_priority') ?? 0;
                        $record->update(['order_priority' => $maxOrder + 1]);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('manage_fields')
                    ->label('Fields')
                    ->icon('heroicon-o-list-bullet')
                    ->url(fn (TemplateSection $record) => \App\Filament\Resources\TemplateSectionResource::getUrl('edit', ['record' => $record->id]))
                    ->openUrlInNewTab(false),

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
