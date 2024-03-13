<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{

    public string $site_name;
    public ?string $site_tag_line;
    public ?string $site_description;
    public ?string $site_keywords;
    public ?string $social_title;
    public ?string $social_description;
    public ?string $social_image;
    public ?string $logo;
    public ?string $favicon;
    public ?string $phone;
    public ?string $wa_phone;
    public ?string $email;
    public ?string $address;
    public ?string $instagram;
    public ?string $facebook;
    public ?string $twitter;
    public ?string $mail_from;
    public ?string $mail_host;
    public ?string $mail_port;
    public ?string $mail_encryption;
    public ?string $mail_username;
    public ?string $mail_password;
    public ?int $force_sll;
    public ?string $secure_password;
    public ?int $term_agreement;
    public ?int $registration;
    public ?string $active_template;
    public ?string $off_days;
    public ?string $currency_text;
    public ?string $currency_symbol;
    public ?float $payment_fix_charge;
    public ?float $payment_percent_charge;
    public bool $site_active;


    public static function group(): string
    {
        return 'general';
    }

    public function getLogo()
    {
        if ($this->logo) {
            return asset($this->logo);
        }
        return null;
    }

    public function getFavicon()
    {
        if ($this->favicon) {
            return asset($this->favicon);
        }

        return asset('assets/images/favicon.ico');
    }
}
