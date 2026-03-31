<?php

namespace App\Filament\Pages;

use App\Constants\UploadPath;
use App\Settings\GeneralSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

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
                                    ->maxSize(1024)
                                    ->rules(['nullable', 'mimes:png,jpg,jpeg', 'max:1024'])
                                    ->directory(UploadPath::CONFIG_UPLOAD_PATH)
                                    ->helperText(__('Supported files: jpeg, jpg, png. Maximum file size 1MB'))
                                    ->imageEditor(),
                                FileUpload::make('favicon')
                                    ->image()
                                    ->maxSize(1024)
                                    ->rules(['nullable', 'mimes:png,jpg,jpeg,ico', 'max:1024'])
                                    ->directory(UploadPath::CONFIG_UPLOAD_PATH)
                                    ->helperText(__('Supported files: jpeg, jpg, png. Maximum file size 1MB')),
                                FileUpload::make('login_logo')
                                    ->image()
                                    ->maxSize(1024)
                                    ->rules(['nullable', 'mimes:png,jpg,jpeg', 'max:1024'])
                                    ->directory(UploadPath::CONFIG_UPLOAD_PATH)
                                    ->helperText(__('Supported files: jpeg, jpg, png. Maximum file size 1MB')),
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
                                    TextInput::make('site_keywords')->helperText(__('Separated by comma')),
                                    TextInput::make('social_title'),
                                    TextInput::make('social_description'),
                                ])->columnSpan(2),
                            ])->columns(3),
                        Tab::make('Contact & Social Media')
                            ->icon('heroicon-o-rss')
                            ->schema([
                                TextInput::make('phone')->label(__('Phone Number'))->tel(),
                                TextInput::make('wa_phone')->label(__('Whatsapp Number'))->tel(),
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
                                TextInput::make('mail_password'),

                            ])->columns(2),
                        Tab::make('System')
                            ->icon('heroicon-o-wrench')
                            ->schema([
                                Toggle::make('registration')
                                    ->label(__('Account Registration'))
                                    ->helperText('Allow users to create an account on the website. When this option is enabled, visitors will be able to register and access member features such as placing orders, viewing their history, and managing their profile.'),
                                Toggle::make('force_ssl')
                                    ->label(__('Force SSL'))
                                    ->helperText('Automatically redirect all visitors to the secure HTTPS version of the website. This helps protect user data and ensures that all communication between the browser and server is encrypted.'),
                                Toggle::make('secure_password')
                                    ->label(__('Secure Password'))
                                    ->helperText('Require users to create stronger passwords for better account security. When enabled, passwords must follow certain rules such as minimum length and the use of letters or numbers.'),
                                Toggle::make('term_agreement')
                                    ->label('Term & Agreement')
                                    ->helperText('Require users to agree to your Terms and Conditions before they can register or use certain features. This helps ensure that users understand the rules and policies of your website.'),
                                Select::make('active_template')
                                    ->options([
                                        'default' => 'Default',
                                    ])
                                    ->helperText('Select the template that will be used for the website layout and appearance. You can switch templates to change the design without affecting the website content.'),
                                Toggle::make('site_active')
                                    ->label(__('Site Active'))
                                    ->helperText('Turn the website on or off. When disabled, visitors will not be able to access the site and may see a maintenance or temporary unavailable page.'),
                            ]),
                        Tab::make('Notifications')
                            ->icon('heroicon-o-bell')
                            ->schema([
                                Textarea::make('admin_emails')
                                    ->label(__('Admin Notification Emails'))
                                    ->helperText('Enter email addresses separated by comma (,) to receive notifications for orders, payments, and other important events. Example: admin@example.com, support@example.com')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(1),
                    ])->columnSpanFull(),
            ]);
    }
}
