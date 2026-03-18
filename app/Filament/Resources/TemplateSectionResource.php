<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateSectionResource\Pages;
use App\Filament\Resources\TemplateSectionResource\RelationManagers\FieldsRelationManager;
use App\Filament\Resources\TemplateSectionResource\RelationManagers\ContentsRelationManager;
use App\Models\Template;
use App\Models\TemplateSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;

class TemplateSectionResource extends Resource
{
    protected static ?string $model = TemplateSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Template Sections';
    protected static ?string $navigationGroup = 'Appearance';
    protected static ?string $slug = 'template-sections';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Section Details')
                    ->schema([
                        Forms\Components\Select::make('template_id')
                            ->label('Template')
                            ->relationship('template', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('type')
                            ->required()
                            ->options(TemplateSection::types())
                            ->searchable(),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(120),

                        Forms\Components\TextInput::make('icon')
                            ->nullable()
                            ->placeholder('heroicon-o-photo')
                            ->helperText('Heroicon name for display in navigation'),

                        Forms\Components\Textarea::make('description')
                            ->nullable()
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan(2),

                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Forms\Components\TextInput::make('order_priority')
                            ->label('Order / Priority')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Lower values appear first. Use Sortable to reorder visually.'),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('template.name')
                    ->label('Template')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_priority')
                    ->label('#')
                    ->sortable()
                    ->width(40),

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
                    ])
                    ->formatStateUsing(fn (string $state) => TemplateSection::types()[$state] ?? $state),

                Tables\Columns\TextColumn::make('fields_count')
                    ->counts('fields')
                    ->label('Fields')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order_priority', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('template_id')
                    ->label('Template')
                    ->relationship('template', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            FieldsRelationManager::class,
            ContentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTemplateSections::route('/'),
            'create' => Pages\CreateTemplateSection::route('/create'),
            'edit'   => Pages\EditTemplateSection::route('/{record}/edit'),
            'sort'   => Pages\SortTemplateSections::route('/{template}/sort'),
        ];
    }
}
