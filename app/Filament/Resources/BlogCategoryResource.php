<?php

namespace App\Filament\Resources;

use App\Enums\StatusType;
use App\Filament\Resources\BlogCategoryResource\Pages;
use App\Filament\Resources\BlogCategoryResource\RelationManagers;
use App\Filament\Resources\Schema\MetaSchema;
use App\Filament\Resources\Schema\TitleSchema;
use App\Models\BlogCategory;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BlogCategoryResource extends Resource
{
    protected static ?string $model = BlogCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Categories';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'blog/categories';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

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
                                    ->autofocus()
                                    ->minLength(3)
                                    ->maxLength(255),
                                RichEditor::make('description')->columnSpanFull(),
                                Toggle::make('is_visible')
                                    ->label('Visible to customers')
                                    ->default(true)

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
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->searchable()->sortable(),
                ToggleColumn::make('is_visible')->sortable()
                    ->label(__('Visibility'))
                    ->afterStateUpdated(function ($record, $state) {
                        return Notification::make()
                            ->title('Visibility status updated successfully')
                            ->success()
                            ->send();
                    })->disabled(!auth()->user()->can('update_blog::category')),
                TextColumn::make('updated_at')
                    ->label(__('Last Updated'))
                    ->dateTime()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            $delete = true;

                            foreach ($records as $record) {
                                if ($record->posts_count > 0) {
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

                            $records->each->delete();
                        }),
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
            'index' => Pages\ListBlogCategories::route('/'),
            'create' => Pages\CreateBlogCategory::route('/create'),
            'edit' => Pages\EditBlogCategory::route('/{record}/edit'),
        ];
    }
}
