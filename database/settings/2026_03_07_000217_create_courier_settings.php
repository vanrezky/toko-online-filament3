<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // RajaOngkir
        $this->migrator->add('courier.rajaongkir_api_key', '');
        $this->migrator->add('courier.rajaongkir_api_key_pro', '');
        $this->migrator->add('courier.rajaongkir_base_url', 'https://api.rajaongkir.com/starter');
        $this->migrator->add('courier.rajaongkir_base_url_pro', 'https://pro.rajaongkir.com/api');
        $this->migrator->add('courier.rajaongkir_api_type', 'free');

        // Default Courier
        $this->migrator->add('courier.default_courier', 'pickup');

        // ApiCoId
        $this->migrator->add('courier.apicoid_api_key', '');
        $this->migrator->add('courier.apicoid_base_url', 'https://use.api.co.id');
    }

    public function down()
    {
        DB::table('settings')->where('group', 'courier')->delete();
    }
};
