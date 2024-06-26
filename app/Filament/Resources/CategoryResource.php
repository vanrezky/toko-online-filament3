<?php

namespace App\Filament\Resources;

use App\Constants\UploadPath;
use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Filament\Resources\Schema\MetaSchema;
use App\Filament\Resources\Schema\TitleSchema;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
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
                                TitleSchema::title('name')
                                    ->required()
                                    ->minLength(3)
                                    ->columnSpanFull(),
                                Group::make([
                                    Forms\Components\FileUpload::make('image')
                                        ->label(__('Category Image'))
                                        ->rules(['required', 'mimes:png,jpg,jpeg,webp,gif', 'max:1024'])
                                        ->image()
                                        ->directory(UploadPath::CATEGORY_UPLOAD_PATH)
                                        ->imageEditorAspectRatios([null, '1:1'])
                                        ->hint(__('Ratio Is 1:1. Maximum size is 1MB')),
                                    Forms\Components\Toggle::make('is_active'),
                                    Forms\Components\Toggle::make('is_featured')
                                ])->columnSpanFull(),
                            ]),
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

                Tables\Columns\ImageColumn::make('image')
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
                Tables\Columns\ToggleColumn::make('is_active')
                    ->afterStateUpdated(function ($record, $state) {
                        return Notification::make()
                            ->title('Activation status updated successfully')
                            ->success()
                            ->send();
                    })->disabled(!auth()->user()->can('update_category')),
                Tables\Columns\ToggleColumn::make('is_featured')
                    ->afterStateUpdated(function ($record, $state) {
                        return Notification::make()
                            ->title('Featured status updated successfully')
                            ->success()
                            ->send();
                    })->disabled(!auth()->user()->can('update_category')),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->action(function ($record) {
                        if ($record->products()->count()) {
                            return Notification::make()
                                ->title('Category cannot be deleted')
                                ->warning()
                                ->send();
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
                        return Notification::make()
                            ->title('There are categories that cannot be deleted')
                            ->warning()
                            ->send();
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
}
