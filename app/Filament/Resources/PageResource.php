<?php

namespace App\Filament\Resources;

use App\Constants\UploadPath;
use App\Enums\BlogPostStatus;
use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Blog';
    protected static ?int $navigationSort = 3;

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
                        Tab::make('Title & Content')
                            ->schema([
                                TextInput::make('title')
                                    ->autofocus()
                                    ->label(__('Page title'))
                                    ->hiddenLabel()
                                    ->placeholder(__('Page title'))
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->id('page-title')
                                    ->live(onBlur: true)
                                    ->extraInputAttributes(['class' => 'column-title'], true)
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, string $operation) {

                                        if ($operation == 'edit') {
                                            return;
                                        }
                                        if (!$get('is_slug_changed_manually') && filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }

                                        $set('slug', Str::slug($state));
                                    }),
                                RichEditor::make('content')
                                    ->hiddenLabel()
                                    ->placeholder('Page Content')
                                    ->required()
                                    ->string()
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('SEO')
                            ->schema([
                                Textarea::make('description')
                                    ->label(__('Description'))
                                    ->hint(__('Write an excerpt for your page')),
                                TextInput::make('slug')
                                    ->label(__('Page Slug'))
                                    ->required()
                                    ->maxLength(255)
                                    ->rules(['alpha_dash'])
                                    ->unique(ignoreRecord: true)
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('is_slug_changed_manually', true);
                                    })
                                    ->required()->columnSpanFull(),
                                Hidden::make('is_slug_changed_manually')
                                    ->default(false)
                                    ->dehydrated(false),
                                Select::make('parent_id')
                                    ->label(__('Parent Page'))
                                    ->options(function (?string $operation, ?string $state) {
                                        if ($operation === 'create') {
                                            return Page::all()->pluck('title', 'id');
                                        }

                                        $page = Page::where('id', '!=', $state)->get()->pluck('title', 'id');
                                    })
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('order')
                                    ->required()
                                    ->default(1)
                                    ->numeric(),
                            ]),
                        Tab::make('Visibility')
                            ->schema([
                                Select::make('is_status')
                                    ->label(__('Page Status'))
                                    ->helperText(__('Publish the page or save it as a draft.'))
                                    ->options([
                                        BlogPostStatus::DRAFT->value => 'Draft',
                                        BlogPostStatus::PUBLISHED->value => 'Published',
                                    ])
                                    ->default(BlogPostStatus::PUBLISHED->value)
                                    ->native(false)
                                    ->required(),
                                DateTimePicker::make('published_at')
                                    ->helperText(__('If published, the page will be visible on this date.'))
                                    ->default(now())
                            ]),
                        Tab::make('Image')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Featured Image')
                                    ->image()
                                    ->imageEditor()
                                    ->directory(UploadPath::IMAGES_UPLOAD_PATH),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('is_status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
