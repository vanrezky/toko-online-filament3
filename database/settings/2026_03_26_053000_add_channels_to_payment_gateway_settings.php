<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.midtrans_channels', []);
        $this->migrator->add('payment.stripe_channels', []);
        $this->migrator->add('payment.xendit_channels', []);
    }

    public function down(): void
    {
        $this->migrator->delete('payment.midtrans_channels');
        $this->migrator->delete('payment.stripe_channels');
        $this->migrator->delete('payment.xendit_channels');
    }
};
