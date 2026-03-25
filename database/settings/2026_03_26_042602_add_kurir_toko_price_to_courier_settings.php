<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddKurirTokoPriceToCourierSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('courier.kurir_toko_price', 0);
    }
}
