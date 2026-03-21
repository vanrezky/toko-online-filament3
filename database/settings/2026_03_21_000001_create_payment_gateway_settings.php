<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.active_gateway', null);

        $this->migrator->add('payment.midtrans_server_key', '');
        $this->migrator->add('payment.midtrans_client_key', '');
        $this->migrator->add('payment.midtrans_merchant_id', '');
        $this->migrator->add('payment.midtrans_mode', 'sandbox');
        $this->migrator->add('payment.midtrans_supported_currencies', ['IDR']);

        $this->migrator->add('payment.stripe_api_key', '');
        $this->migrator->add('payment.stripe_webhook_secret', '');
        $this->migrator->add('payment.stripe_mode', 'test');
        $this->migrator->add('payment.stripe_supported_currencies', ['USD', 'EUR']);

        $this->migrator->add('payment.xendit_api_key', '');
        $this->migrator->add('payment.xendit_secret_key', '');
        $this->migrator->add('payment.xendit_mode', 'test');
        $this->migrator->add('payment.xendit_supported_currencies', ['IDR', 'PHP']);

        $this->migrator->add('payment.default_currency', 'IDR');
    }

    public function down(): void
    {
        DB::table('settings')->where('group', 'payment')->delete();
    }
};
