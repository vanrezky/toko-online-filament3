<?php

namespace App\Filament\Pages;

use App\Models\Courier;
use App\Settings\CourierSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class ManageCourier extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?int $navigationSort = 6;
    protected static ?string $slug = 'setting/courier';

    protected static string $view = 'filament.pages.manage-courier';

    public ?array $data = [];

    public function mount(CourierSettings $settings): void
    {
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('')
                    ->tabs([
                        Tab::make('rajaongkir')
                            ->label('RajaOngkir')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('rajaongkir_api_key')
                                            ->label('API Key (Free/Basic/Starter)')
                                            ->password()
                                            ->revealable()
                                            ->helperText(new HtmlString('Get your API key at <a href="https://rajaongkir.com/" target="_blank" class="text-primary-600 underline">rajaongkir.com</a>')),
                                        Select::make('rajaongkir_api_type')
                                            ->label('API Type')
                                            ->options([
                                                'free' => 'Free',
                                                'starter' => 'Starter',
                                                'basic' => 'Basic',
                                                'pro' => 'Pro',
                                            ])
                                            ->required(),
                                        TextInput::make('rajaongkir_base_url')
                                            ->label('Base URL')
                                            ->placeholder('https://api.rajaongkir.com/starter'),
                                        TextInput::make('rajaongkir_api_key_pro')
                                            ->label('API Key (Pro)')
                                            ->password()
                                            ->revealable()
                                            ->helperText(new HtmlString('Get your Pro API key at <a href="https://rajaongkir.com/" target="_blank" class="text-primary-600 underline">rajaongkir.com</a>')),
                                        TextInput::make('rajaongkir_base_url_pro')
                                            ->label('Base URL (Pro)')
                                            ->placeholder('https://pro.rajaongkir.com/api'),
                                    ]),
                            ]),
                        Tab::make('apicoid')
                            ->label('ApiCoId')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('apicoid_api_key')
                                            ->label('API Key')
                                            ->password()
                                            ->revealable()
                                            ->helperText(new HtmlString('Get your API key at <a href="https://api.co.id/" target="_blank" class="text-primary-600 underline">api.co.id</a>')),
                                        TextInput::make('apicoid_base_url')
                                            ->label('Base URL')
                                            ->placeholder('https://api.co.id/'),
                                    ]),

                            ]),
                        Tab::make('kurir_toko')
                            ->label('Kurir Toko')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('kurir_toko_price')
                                            ->label('Tarif Pengiriman Kurir Toko')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->required(),
                                    ]),
                            ]),
                    ]),

                Section::make('General')
                    ->schema([
                        Select::make('default_courier')
                            ->options(Courier::pluck('name', 'code'))
                            ->searchable()
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(CourierSettings $settings): void
    {
        $settings->fill($this->form->getState());
        $settings->save();

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    public function getCouriers(): Collection
    {
        return Courier::orderBy('name')->get();
    }

    public function toggleStatus(int $id): void
    {
        $courier = Courier::find($id);
        $courier->is_active = !$courier->is_active;
        $courier->save();

        Notification::make()
            ->title('Status updated')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return isSuperUser();
    }
}
