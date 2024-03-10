<?php

namespace App\Filament\Resources;

use App\Constants\UploadPath;
use App\Enums\StatusType;
use App\Filament\Resources\BlogPostResource\Pages;
use App\Filament\Resources\BlogPostResource\RelationManagers;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Posts';
    protected static ?string $navigationGroup = 'Blog';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make('Post Information')
                        ->schema([
                            TextInput::make('title')
                                ->label(__('Post title'))
                                ->hiddenLabel()
                                ->placeholder(__('Post title'))
                                ->minLength(5)
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull()
                                ->id('post-title')
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
                                ->placeholder('Post Content')
                                ->required()
                                ->string()
                                ->columnSpanFull(),
                            FileUpload::make('image')
                                ->image()
                                ->imageEditor()
                                ->required()
                                ->imagePreviewHeight('150')
                                ->directory(UploadPath::IMAGES_UPLOAD_PATH),
                        ]),
                ])->columnSpan(2),

                Group::make([
                    Section::make('Other Information')
                        ->schema([
                            TextInput::make('slug')
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

                            Select::make('blog_category_id')
                                ->relationship('category', titleAttribute: 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TagsInput::make('tags')
                                ->label(__('Tags'))
                                ->placeholder('e.g: electronics, phone, laptop')
                                ->separator(','),
                            Select::make('is_status')
                                ->label(__('Post Status'))
                                ->helperText(__('Publish the post or save it as a draft.'))
                                ->options([
                                    StatusType::ACTIVE->value => 'Draft',
                                    StatusType::INACTIVE->value => 'Published',
                                ])
                                ->default(StatusType::INACTIVE->value)
                                ->native(false)
                                // ->canSelectPlaceholder(false)
                                ->required()
                                ->live(),

                            DatePicker::make('published_at')
                                ->helperText(__('If published, the post will be visible on this date.'))
                                ->default(now()),
                            Hidden::make('user_id')
                                ->default(auth()->id())
                                ->dehydrated(fn (string $operation) => $operation !== 'edit'),

                        ]),

                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Post title'))
                    ->searchable()
                    ->sortable()
                    ->words(5),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label(__('Author'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->label(__('Published')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Last Updated'))
                    ->date(),

            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'view' => Pages\ViewBlogPost::route('/{record}'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
