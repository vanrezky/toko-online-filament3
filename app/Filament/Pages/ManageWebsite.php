<?php

namespace App\Filament\Pages;

use App\Constants\UploadPath;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\HtmlString;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageWebsite extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'setting/settings';

    protected static string $settings = GeneralSettings::class;

    public static function canAccess(): bool
    {
        return isSuperUser();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()
                    ->schema([
                        Tab::make('General')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('site_name')->required()->maxLength(255),
                                        TextInput::make('site_tag_line')->maxLength(255),
                                        TextInput::make('address')->maxLength(255),
                                        Group::make([
                                            TextInput::make('currency_text')->required()->maxLength(5),
                                            TextInput::make('currency_symbol')->required()->maxLength(5),
                                        ])->columns(2),

                                    ])->columnSpanFull(),
                            ])->columns(2),
                        Tab::make('Logo')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                FileUpload::make('logo')
                                    ->image()
                                    ->rules(['nullable', 'mimes:png,jpg,jpeg', 'max:1024'])
                                    ->directory(UploadPath::IMAGE_CONFIG_PATH)
                                    ->helperText('Supported files: jpeg, jpg, png. Maximum file size 1MB')
                                    ->imageEditor(),
                                FileUpload::make('favicon')
                                    ->image()
                                    ->rules(['nullable', 'mimes:png,jpg,jpeg', 'max:1024'])
                                    ->directory(UploadPath::IMAGE_CONFIG_PATH)
                                    ->helperText('Supported files: jpeg, jpg, png. Maximum file size 1MB'),
                                FileUpload::make('login_logo')
                                    ->image()
                                    ->rules(['nullable', 'mimes:png,jpg,jpeg', 'max:1024'])
                                    ->directory(UploadPath::IMAGE_CONFIG_PATH)
                                    ->helperText('Supported files: jpeg, jpg, png. Maximum file size 1MB'),
                            ])->columns(2),
                        Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                FileUpload::make('social_image')
                                    ->helperText(__('Supported files: jpeg, jpg, png. Images will be resized to 1180x600pixel. maximum size 1MB.'))
                                    ->rules(['nullable', 'mimes:png,jpg,jpeg', 'max:1024'])
                                    ->directory(UploadPath::IMAGES_UPLOAD_PATH)
                                    ->image()
                                    ->imageResizeMode('cover')
                                    ->imageResizeTargetWidth('1180')
                                    ->imageResizeTargetHeight('600'),
                                Group::make([
                                    Textarea::make('site_description')->maxLength(300),
                                    TextInput::make('site_keywords')->helperText('Separated by comma'),
                                    TextInput::make('social_title'),
                                    TextInput::make('social_description'),
                                ])->columnSpan(2)
                            ])->columns(3),
                        Tab::make('Contact & Social Media')
                            ->icon('heroicon-o-rss')
                            ->schema([
                                TextInput::make('phone')->label('Phone Number')->tel(),
                                TextInput::make('wa_phone')->label('Whatsapp Number')->tel(),
                                TextInput::make('instagram')->url()->maxLength(255),
                                TextInput::make('facebook')->url()->maxLength(255),
                                TextInput::make('twitter')->url()->maxLength(255),

                            ]),

                        Tab::make('Mail Config')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                TextInput::make('mail_from'),
                                TextInput::make('mail_host'),
                                TextInput::make('mail_port')->numeric()->maxLength(5),
                                Select::make('mail_encryption')
                                    ->rules(['required', 'in:tls,ssl'])
                                    ->options([
                                        'ssl' => 'SSL',
                                        'tls' => 'TLS',
                                    ]),
                                TextInput::make('mail_username')->email(),
                                TextInput::make('mail_password')

                            ])->columns(2),
                        Tab::make('System')
                            ->icon('heroicon-o-wrench')
                            ->schema([
                                Toggle::make('registration')
                                    ->label(__('Account Registration'))
                                    ->helperText('Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, dignissimos nobis itaque aspernatur saepe nisi eos minus quasi animi dolore non at hic commodi ipsum assumenda nostrum nesciunt tenetur magni.'),
                                Toggle::make('force_ssl')
                                    ->label(__('Force SSL'))
                                    ->helperText('Lorem ipsum dolor sit, amet consectetur adipisicing elit. Adipisci, voluptas laborum? Suscipit sint molestiae magni ab? Tempore ea, eligendi similique modi animi, et quaerat perspiciatis enim excepturi, mollitia nobis quidem?'),
                                Toggle::make('secure_password')
                                    ->label(__('Secure Password'))
                                    ->helperText('Lorem ipsum dolor sit amet consectetur adipisicing elit. In facere odio ab quibusdam sequi nemo officia reprehenderit neque! Odit corporis nemo quia, rerum atque voluptatem rem error voluptates maiores porro.'),
                                Toggle::make('term_agreement')
                                    ->label('Term & Agreement')
                                    ->helperText('Lorem ipsum, dolor sit amet consectetur adipisicing elit. Voluptatibus sint dignissimos repudiandae dolorum. Necessitatibus optio magnam non quibusdam earum! Soluta, maxime? Deleniti, ipsum totam ab asperiores eligendi atque aut quas!'),
                                Select::make('active_template')
                                    ->options([
                                        'default' => 'Default',
                                    ])
                                    ->helperText('Lorem ipsum dolor sit amet consectetur adipisicing elit. Recusandae voluptatum provident rem ad commodi aliquam illum impedit quia, minus consectetur. Nam dicta rerum omnis repellat asperiores dignissimos culpa itaque et.'),
                                Toggle::make('site_active')
                                    ->label(__('Site Active'))
                                    ->helperText('Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur voluptatem voluptatum commodi libero est ducimus at, laboriosam ipsa atque. Dolore architecto ipsa aut corrupti quam quasi. Ullam quisquam cum delectus!')
                            ])
                    ])->columnSpanFull()
            ]);
    }
}
