<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateResource\Pages;
use App\Filament\Resources\TemplateResource\RelationManagers\SectionsRelationManager;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationLabel = 'Templates';
    protected static ?string $navigationGroup = 'Appearance';
    protected static ?string $slug = 'templates';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Template Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(120)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) =>
                                $set('code', \Illuminate\Support\Str::slug($state ?? '', '_'))
                            ),

                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(80)
                            ->helperText('Auto-generated from name. Must be unique.')
                            ->alphaDash(),

                        Forms\Components\Textarea::make('description')
                            ->nullable()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan(2),

                Forms\Components\Section::make('Color Scheme')
                    ->schema([
                        Forms\Components\ColorPicker::make('color_scheme.primary')
                            ->label('Primary Color')
                            ->default('#3B82F6'),

                        Forms\Components\ColorPicker::make('color_scheme.secondary')
                            ->label('Secondary Color')
                            ->default('#10B981'),

                        Forms\Components\ColorPicker::make('color_scheme.accent')
                            ->label('Accent Color')
                            ->default('#F59E0B'),

                        Forms\Components\ColorPicker::make('color_scheme.background')
                            ->label('Background Color')
                            ->default('#FFFFFF'),

                        Forms\Components\ColorPicker::make('color_scheme.text')
                            ->label('Text Color')
                            ->default('#111827'),
                    ])
                    ->columns(1)
                    ->columnSpan(1),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(false)
                            ->helperText('Only one template can be active at a time.'),

                        Forms\Components\TextInput::make('thumbnail')
                            ->label('Thumbnail URL')
                            ->url()
                            ->nullable()
                            ->placeholder('https://example.com/thumbnail.png')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('sections_count')
                    ->counts('sections')
                    ->label('Sections')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\ColorColumn::make('color_scheme.primary')
                    ->label('Primary Color')
                    ->tooltip(fn (Template $record) => $record->color_scheme['primary'] ?? null),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->afterStateUpdated(fn () => notification('Template status updated.', 'success')),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
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
            SectionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'edit'   => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }
}
