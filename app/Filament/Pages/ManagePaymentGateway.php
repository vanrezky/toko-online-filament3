<?php

namespace App\Filament\Pages;

use App\Models\Currency;
use App\Settings\PaymentGatewaySettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class ManagePaymentGateway extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'setting/payment-gateway-settings';

    protected static string $view = 'filament.pages.manage-payment-gateway';

    public ?array $data = [];

    public function mount(PaymentGatewaySettings $settings): void
    {
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        $currencies = Currency::active()->pluck('name', 'code')->toArray();

        return $form
            ->schema([
                Tabs::make('Payment Gateways')
                    ->tabs([
                        Tab::make('midtrans')
                            ->label('Midtrans')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Toggle::make('midtrans_is_active')
                                    ->label('Set as Active Gateway')
                                    ->helperText('Only one gateway can be active at a time'),

                                Section::make('Credentials')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('midtrans_server_key')
                                                    ->label('Server Key')
                                                    ->password()
                                                    ->revealable(filament()->arePasswordsRevealable())
                                                    ->helperText(new HtmlString('Get your Server Key from <a href="https://dashboard.midtrans.com/" target="_blank" class="text-primary-600 underline">Midtrans Dashboard</a>')),

                                                TextInput::make('midtrans_client_key')
                                                    ->label('Client Key')
                                                    ->password()
                                                    ->revealable(filament()->arePasswordsRevealable()),
                                            ]),

                                        TextInput::make('midtrans_merchant_id')
                                            ->label('Merchant ID')
                                            ->placeholder('MCH-XXXXXX'),
                                    ]),

                                Section::make('Configuration')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('midtrans_mode')
                                                    ->label('Mode')
                                                    ->options([
                                                        'sandbox' => 'Sandbox (Testing)',
                                                        'production' => 'Production (Live)',
                                                    ])
                                                    ->default('sandbox'),

                                                Select::make('midtrans_supported_currencies')
                                                    ->label('Supported Currencies')
                                                    ->options($currencies)
                                                    ->multiple()
                                                    ->preload(),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('stripe')
                            ->label('Stripe')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Toggle::make('stripe_is_active')
                                    ->label('Set as Active Gateway')
                                    ->helperText('Only one gateway can be active at a time'),

                                Section::make('Credentials')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('stripe_api_key')
                                                    ->label('API Key')
                                                    ->password()
                                                    ->revealable(filament()->arePasswordsRevealable())
                                                    ->helperText(new HtmlString('Get your API Key from <a href="https://dashboard.stripe.com/apikeys" target="_blank" class="text-primary-600 underline">Stripe Dashboard</a>')),

                                                TextInput::make('stripe_webhook_secret')
                                                    ->label('Webhook Secret')
                                                    ->password()
                                                    ->revealable(filament()->arePasswordsRevealable())
                                                    ->helperText('Used for verifying webhook signatures'),
                                            ]),
                                    ]),

                                Section::make('Configuration')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('stripe_mode')
                                                    ->label('Mode')
                                                    ->options([
                                                        'test' => 'Test Mode',
                                                        'live' => 'Live Mode',
                                                    ])
                                                    ->default('test'),

                                                Select::make('stripe_supported_currencies')
                                                    ->label('Supported Currencies')
                                                    ->options($currencies)
                                                    ->multiple()
                                                    ->preload(),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('xendit')
                            ->label('Xendit')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Toggle::make('xendit_is_active')
                                    ->label('Set as Active Gateway')
                                    ->helperText('Only one gateway can be active at a time'),

                                Section::make('Credentials')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('xendit_api_key')
                                                    ->label('API Key')
                                                    ->password()
                                                    ->revealable(filament()->arePasswordsRevealable())
                                                    ->helperText(new HtmlString('Get your API Key from <a href="https://dashboard.xendit.co/settings/developers" target="_blank" class="text-primary-600 underline">Xendit Dashboard</a>')),

                                                TextInput::make('xendit_secret_key')
                                                    ->label('Secret Key')
                                                    ->password()
                                                    ->revealable(filament()->arePasswordsRevealable()),
                                            ]),
                                    ]),

                                Section::make('Configuration')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('xendit_mode')
                                                    ->label('Mode')
                                                    ->options([
                                                        'test' => 'Test Mode',
                                                        'live' => 'Live Mode',
                                                    ])
                                                    ->default('test'),

                                                Select::make('xendit_supported_currencies')
                                                    ->label('Supported Currencies')
                                                    ->options($currencies)
                                                    ->multiple()
                                                    ->preload(),
                                            ]),
                                    ]),
                            ]),
                    ])->columnSpanFull(),

                Section::make('Default Settings')
                    ->schema([
                        Select::make('default_currency')
                            ->label('Default Currency')
                            ->options($currencies)
                            ->searchable()
                            ->preload(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(PaymentGatewaySettings $settings): void
    {
        $data = $this->form->getState();

        $activeGateway = null;
        $gatewayAliases = ['midtrans', 'stripe', 'xendit'];

        foreach ($gatewayAliases as $alias) {
            $isActiveKey = "{$alias}_is_active";
            if (isset($data[$isActiveKey]) && $data[$isActiveKey]) {
                $activeGateway = $alias;
                break;
            }
        }

        $settings->active_gateway = $activeGateway;

        $gatewayFields = [
            'midtrans' => ['server_key', 'client_key', 'merchant_id', 'mode', 'supported_currencies'],
            'stripe' => ['api_key', 'webhook_secret', 'mode', 'supported_currencies'],
            'xendit' => ['api_key', 'secret_key', 'mode', 'supported_currencies'],
        ];

        foreach ($gatewayFields as $gateway => $fields) {
            foreach ($fields as $field) {
                $key = "{$gateway}_{$field}";
                if (array_key_exists($key, $data)) {
                    $settings->{$key} = $data[$key];
                }
            }
        }

        if (isset($data['default_currency'])) {
            $settings->default_currency = $data['default_currency'];
        }

        $settings->save();

        Notification::make()
            ->title('Payment gateway settings saved')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return isSuperUser();
    }
}
