<?php

namespace App\Settings;

use Illuminate\Support\Facades\Crypt;
use Spatie\LaravelSettings\Settings;

class PaymentGatewaySettings extends Settings
{
    public ?string $active_gateway;

    public ?string $midtrans_server_key;
    public ?string $midtrans_client_key;
    public ?string $midtrans_merchant_id;
    public ?string $midtrans_mode;
    public array $midtrans_supported_currencies = [];

    public ?string $stripe_api_key;
    public ?string $stripe_webhook_secret;
    public ?string $stripe_mode;
    public array $stripe_supported_currencies = [];

    public ?string $xendit_api_key;
    public ?string $xendit_secret_key;
    public ?string $xendit_mode;
    public array $xendit_supported_currencies = [];

    public ?string $default_currency;

    public static function group(): string
    {
        return 'payment';
    }

    public function getMidtransServerKeyAttribute(?string $value): ?string
    {
        return $value ? Crypt::decrypt($value) : null;
    }

    public function setMidtransServerKeyAttribute(?string $value): void
    {
        $this->attributes['midtrans_server_key'] = $value ? Crypt::encrypt($value) : null;
    }

    public function getMidtransClientKeyAttribute(?string $value): ?string
    {
        return $value ? Crypt::decrypt($value) : null;
    }

    public function setMidtransClientKeyAttribute(?string $value): void
    {
        $this->attributes['midtrans_client_key'] = $value ? Crypt::encrypt($value) : null;
    }

    public function getStripeApiKeyAttribute(?string $value): ?string
    {
        return $value ? Crypt::decrypt($value) : null;
    }

    public function setStripeApiKeyAttribute(?string $value): void
    {
        $this->attributes['stripe_api_key'] = $value ? Crypt::encrypt($value) : null;
    }

    public function getStripeWebhookSecretAttribute(?string $value): ?string
    {
        return $value ? Crypt::decrypt($value) : null;
    }

    public function setStripeWebhookSecretAttribute(?string $value): void
    {
        $this->attributes['stripe_webhook_secret'] = $value ? Crypt::encrypt($value) : null;
    }

    public function getXenditApiKeyAttribute(?string $value): ?string
    {
        return $value ? Crypt::decrypt($value) : null;
    }

    public function setXenditApiKeyAttribute(?string $value): void
    {
        $this->attributes['xendit_api_key'] = $value ? Crypt::encrypt($value) : null;
    }

    public function getXenditSecretKeyAttribute(?string $value): ?string
    {
        return $value ? Crypt::decrypt($value) : null;
    }

    public function setXenditSecretKeyAttribute(?string $value): void
    {
        $this->attributes['xendit_secret_key'] = $value ? Crypt::encrypt($value) : null;
    }

    public function isGatewayActive(string $alias): bool
    {
        return $this->active_gateway === $alias;
    }

    public function getActiveGatewayCredentials(): ?array
    {
        if (!$this->active_gateway) {
            return null;
        }

        return match ($this->active_gateway) {
            'midtrans' => [
                'server_key' => $this->midtrans_server_key,
                'client_key' => $this->midtrans_client_key,
                'merchant_id' => $this->midtrans_merchant_id,
                'mode' => $this->midtrans_mode,
                'supported_currencies' => $this->midtrans_supported_currencies,
            ],
            'stripe' => [
                'api_key' => $this->stripe_api_key,
                'webhook_secret' => $this->stripe_webhook_secret,
                'mode' => $this->stripe_mode,
                'supported_currencies' => $this->stripe_supported_currencies,
            ],
            'xendit' => [
                'api_key' => $this->xendit_api_key,
                'secret_key' => $this->xendit_secret_key,
                'mode' => $this->xendit_mode,
                'supported_currencies' => $this->xendit_supported_currencies,
            ],
            default => null,
        };
    }

    public function getSupportedCurrencies(?string $alias = null): array
    {
        $alias = $alias ?? $this->active_gateway;

        return match ($alias) {
            'midtrans' => $this->midtrans_supported_currencies,
            'stripe' => $this->stripe_supported_currencies,
            'xendit' => $this->xendit_supported_currencies,
            default => [],
        };
    }

    public function isConfigured(string $alias): bool
    {
        return match ($alias) {
            'midtrans' => !empty($this->midtrans_server_key) && !empty($this->midtrans_client_key),
            'stripe' => !empty($this->stripe_api_key),
            'xendit' => !empty($this->xendit_api_key),
            default => false,
        };
    }
}
