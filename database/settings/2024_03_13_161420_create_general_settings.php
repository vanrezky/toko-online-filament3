<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {

        $this->migrator->add('general.site_name', config('app.name'));
        $this->migrator->add('general.site_tag_line', 'Menyala abangku 🔥');
        $this->migrator->add('general.site_description', 'toko online website belanja online');
        $this->migrator->add('general.site_keywords', 'Toko online, ecommerce belanja, toko belanja online');
        $this->migrator->add('general.social_image');
        $this->migrator->add('general.social_title', config('app.name'));
        $this->migrator->add('general.social_description', 'toko online website belanja online');
        $this->migrator->add('general.logo');
        $this->migrator->add('general.login_logo');
        $this->migrator->add('general.favicon');
        $this->migrator->add('general.phone', fake()->e164PhoneNumber());
        $this->migrator->add('general.wa_phone', fake()->phoneNumber());
        $this->migrator->add('general.email', fake()->safeEmail());
        $this->migrator->add('general.address', fake()->address());
        $this->migrator->add('general.instagram', 'https://instagram.com');
        $this->migrator->add('general.facebook', 'https://facebook.com');
        $this->migrator->add('general.twitter', 'https://twitter.com');
        $this->migrator->add('general.mail_from', 'Admin Toko Online');
        $this->migrator->add('general.mail_host', 'smtp.gmail.com');
        $this->migrator->add('general.mail_port', '567');
        $this->migrator->add('general.mail_encryption', 'tls');
        $this->migrator->add('general.mail_username', 'admin@example.com');
        $this->migrator->add('general.mail_password', 'password');
        $this->migrator->add('general.force_sll', 0);
        $this->migrator->add('general.secure_password', 0);
        $this->migrator->add('general.term_agreement', 0);
        $this->migrator->add('general.registration', 0);
        $this->migrator->add('general.active_template', 'default');
        $this->migrator->add('general.off_days');
        $this->migrator->add('general.currency_text', 'IDR');
        $this->migrator->add('general.currency_symbol', 'Rp');
        $this->migrator->add('general.payment_fix_charge', 0);
        $this->migrator->add('general.payment_percent_charge', 0);
        $this->migrator->add('general.site_active', true);
    }

    public function down()
    {
        DB::table('settings')->where('group', 'general')->delete();
    }
};
