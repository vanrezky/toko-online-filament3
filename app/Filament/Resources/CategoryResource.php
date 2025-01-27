<?php

namespace App\Filament\Resources;

use App\Constants\UploadPath;
use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Filament\Resources\Schema\MetaSchema;
use App\Filament\Resources\Schema\TitleSchema;
use App\Models\Category;
use App\Tables\Components\GeneralToogleInfoColumn;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use GeneralToogleTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationLabel = 'Categories';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?string $slug = 'shop/categories';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()
                    ->schema([
                        Tabs\Tab::make('Main')
                            ->schema([
                                Group::make([
                                    TitleSchema::title('name')
                                        ->required()
                                        ->minLength(3),
                                    Forms\Components\Toggle::make('is_active'),
                                    Forms\Components\Toggle::make('is_featured')
                                ]),

                                SpatieMediaLibraryFileUpload::make('image')
                                    ->label(__('Image'))
                                    ->maxSize(1024)
                                    ->rules(['required', 'mimes:png,jpg,jpeg,webp,gif', 'max:1024'])
                                    ->image()
                                    ->directory(UploadPath::CATEGORY_UPLOAD_PATH)
                                    ->imageCropAspectRatio('1:1')
                                    ->imagePreviewHeight(250)
                                    ->helperText(__('Ratio Is 1:1. Maximum size is 1MB'))
                                    ->disk(getActiveDisk())
                            ])->columns(2),
                        Tabs\Tab::make('SEO')
                            ->schema([
                                TitleSchema::slug(),
                                TitleSchema::hidden(),
                                MetaSchema::get(),
                            ])
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->conversion('thumb')
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Category Name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('products_count')->counts('products')
                    ->prefix('Products: ')
                    ->badge()
                    ->icon('heroicon-o-squares-2x2')
                    ->label(__('Total Product'))
                    ->color('danger')
                    ->sortable(),
                self::getIsActiveColumn(),
                self::getIsFeaturedColumn(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->action(function ($record) {
                        if ($record->products()->count()) {
                            notification(__('Category cannot be deleted'), 'warning');
                        }

                        return $record->delete();
                    }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->action(function ($records) {
                    $delete = true;
                    foreach ($records as $key => $record) {
                        if ($record->products()->count()) {
                            $delete = false;
                            break;
                        }
                    }

                    if (!$delete) {
                        notification(__('There are categories that cannot be deleted'), 'warning');
                    }
                }),
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
            // 'create' => Pages\CreateCategory::route('/create'),
            // 'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getIsActiveColumn()
    {
        if (self::shouldCanUpdate()) {
            return Tables\Columns\ToggleColumn::make('is_active')
                ->afterStateUpdated(fn() => notification(__('Activation status updated successfully')))->label(__('Active'));
        }
        return Tables\Columns\IconColumn::make('is_active')->boolean()->label(__('Active'));
    }

    public static function getIsFeaturedColumn()
    {
        if (self::shouldCanUpdate()) {
            return Tables\Columns\ToggleColumn::make('is_featured')
                ->afterStateUpdated(fn() => notification(__('Featured status updated successfully')))->label(__('Featured'));
        }
        return Tables\Columns\IconColumn::make('is_featured')->boolean()->label(__('Featured'));
    }

    public static function shouldCanUpdate(): bool
    {
        return auth()->user()->can('update_category');
    }
}
