<?php

namespace App\Settings;

use App\Enums\CourierCode;
use Spatie\LaravelSettings\Settings;

class CourierSettings extends Settings
{
    // RajaOngkir
    public ?string $rajaongkir_api_key = "";
    public ?string $rajaongkir_api_key_pro;
    public ?string $rajaongkir_base_url;
    public ?string $rajaongkir_base_url_pro;
    public ?string $rajaongkir_api_type = "free";

    // ApiCoId https://api.co.id/
    public ?string $apicoid_api_key;
    public ?string $apicoid_base_url = "https://api.co.id/";

    // Kurir Toko
    public ?int $kurir_toko_price = 0;

    // Default Courier
    public ?string $default_courier = CourierCode::PICKUP->value;

    public static function group(): string
    {
        return 'courier';
    }
}
