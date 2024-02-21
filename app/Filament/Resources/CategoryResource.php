<?php

namespace App\Filament\Resources;

use App\Constants\UploadPath;
use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Management';
    protected static ?string $slug = 'master-data/categories';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Section::make([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->minLength(3)
                            ->live()
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required(),
                    ]),
                    Section::make([
                        Forms\Components\Toggle::make('is_active')
                            ->default(fn (): bool => true),
                        Forms\Components\Toggle::make('is_featured')
                            ->default(fn (): bool => true),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory(UploadPath::CATEGORY_UPLOAD_PATH)
                            ->imageEditorAspectRatios([null, '1:1'])
                    ])
                ])->from('md')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Category Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->square(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->afterStateUpdated(function ($record, $state) {
                        $record->is_active = $state;
                        $record->save();
                    }),
                Tables\Columns\ToggleColumn::make('is_featured')
                    ->afterStateUpdated(function ($record, $state) {
                        $record->is_featured = $state;
                        $record->save();
                    }),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
