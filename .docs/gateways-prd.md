# Product Requirements Document (PRD)

## Dynamic Payment Gateway System

**Version:** 1.0  
**Status:** Planned  
**Last Updated:** March 2026  
**Platform:** Laravel + Filament  
**Path Implement:**

- Backend: `app/Services/Gateways/`
- Settings: `app/Settings/`
- Database: `database/migrations/`, `database/settings/`

---

## 1. Executive Summary

Dynamic Payment Gateway System memungkinkan admin untuk mengelola credential payment gateway (Midtrans, Stripe, Xendit, dll) melalui settings panel. Hanya satu gateway yang dapat aktif pada satu waktu, dengan support multi-currency melalui ISO 4217 standard.

**Key Features:**

- Multiple payment gateway support (Midtrans, Stripe, Xendit)
- Encrypted credential storage
- Single active gateway enforcement
- Multi-currency support via Currency master table
- Filament admin panel integration

---

## 2. Goals & Objectives

### Primary Goals

1. **Security** - API keys dan credentials di-encrypt saat penyimpanan
2. **Flexibility** - Mudah tambah gateway baru tanpa code change
3. **Simplicity** - Admin cukup setup credentials, sistem handle sisanya
4. **Multi-currency** - Support berbagai mata uang via ISO 4217

### Success Metrics

- Admin dapat configure gateway dalam < 5 menit
- Payment processing success rate: > 99%
- Support minimal 3 gateway currencies (IDR, USD, EUR)

---

## 3. Directory Structure

```
app/Services/
├── PaymentGatewayService.php           (Main facade/orchestrator)
├── PaymentGatewaySettings.php           (Settings model - internal use)
└── Gateways/
    ├── Contracts/
    │   └── PaymentGatewayInterface.php  (Gateway contract)
    ├── MidtransGateway.php              (Midtrans implementation)
    ├── StripeGateway.php                (Stripe implementation)
    └── XenditGateway.php                (Xendit implementation)

app/Settings/
└── PaymentGatewaySettings.php           (Laravel Settings model)

app/Models/
└── Currency.php                        (Currency master model)

app/Filament/Pages/
└── ManagePaymentGateway.php             (Filament settings page)
```

---

## 4. Database Schema

### 4.1 New Table - `currencies`

**Purpose:** Master table untuk supported currencies (ISO 4217)

```php
// database/migrations/xxxx_create_currencies_table.php
Schema::create('currencies', function (Blueprint $table) {
    $table->id();
    $table->string('code', 3)->unique();     // ISO 4217: USD, EUR, IDR
    $table->string('name', 50);               // "US Dollar", "Euro"
    $table->string('symbol', 10);              // "$", "€", "Rp"
    $table->tinyInteger('decimal_places')->default(2);  // 0 for JPY, KRW
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**Seed:** ISO 4217 currency list (USD, EUR, GBP, IDR, JPY, dll)

### 4.2 PaymentGatewaySettings

**Location:** `database/settings/`

```php
// database/settings/xxxx_create_payment_gateway_settings.php

public function up(): void
{
    // Active gateway
    $this->migrator->add('payment.active_gateway', null);

    // Midtrans credentials (encrypted)
    $this->migrator->add('payment.midtrans.server_key', '');
    $this->migrator->add('payment.midtrans.client_key', '');
    $this->migrator->add('payment.midtrans.merchant_id', '');
    $this->migrator->add('payment.midtrans.mode', 'sandbox');
    $this->migrator->add('payment.midtrans.supported_currencies', ['IDR']);

    // Stripe credentials (encrypted)
    $this->migrator->add('payment.stripe.api_key', '');
    $this->migrator->add('payment.stripe.webhook_secret', '');
    $this->migrator->add('payment.stripe.mode', 'test');
    $this->migrator->add('payment.stripe.supported_currencies', ['USD', 'EUR']);

    // Xendit credentials (encrypted)
    $this->migrator->add('payment.xendit.api_key', '');
    $this->migrator->add('payment.xendit.secret_key', '');
    $this->migrator->add('payment.xendit.mode', 'test');
    $this->migrator->add('payment.xendit.supported_currencies', ['IDR', 'PHP']);

    // Global
    $this->migrator->add('payment.default_currency', 'IDR');
}
```

### 4.3 Existing Table - `payment_gateways`

**Note:** Table existing tetap ada untuk tracking gateway metadata, tapi credentials dipindah ke Settings.

| Field                | Type      | Description                                     |
| -------------------- | --------- | ----------------------------------------------- |
| `id`                 | bigint    | Primary key                                     |
| `form_id`            | bigint FK | Optional form association                       |
| `code`               | int       | Gateway code                                    |
| `name`               | string    | Display name                                    |
| `alias`              | string    | Unique identifier (midtrans, stripe)            |
| `status`             | boolean   | Global on/off switch                            |
| `gateway_parameters` | JSON      | Additional params (deprecated for credentials)  |
| `support_currencies` | JSON      | Supported currencies (deprecated, use Settings) |
| `description`        | string    | Description                                     |

### 4.4 Existing Table - `payment_gateway_currencies`

**Update:** Add FK ke currencies table

```php
// Add to existing table
$table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
```

---

## 5. Settings Model

### 5.1 PaymentGatewaySettings

```php
// app/Settings/PaymentGatewaySettings.php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;
use Illuminate\Support\Facades\Crypt;

class PaymentGatewaySettings extends Settings
{
    // Active gateway
    public ?string $active_gateway;

    // Midtrans
    public ?string $midtrans_server_key;
    public ?string $midtrans_client_key;
    public ?string $midtrans_merchant_id;
    public ?string $midtrans_mode;
    public array $midtrans_supported_currencies;

    // Stripe
    public ?string $stripe_api_key;
    public ?string $stripe_webhook_secret;
    public ?string $stripe_mode;
    public array $stripe_supported_currencies;

    // Xendit
    public ?string $xendit_api_key;
    public ?string $xendit_secret_key;
    public ?string $xendit_mode;
    public array $xendit_supported_currencies;

    // Global
    public ?string $default_currency;

    public static function group(): string
    {
        return 'payment';
    }

    protected function casts(): array
    {
        return [
            'midtrans_server_key' => 'encrypted',
            'midtrans_client_key' => 'encrypted',
            'stripe_api_key' => 'encrypted',
            'stripe_webhook_secret' => 'encrypted',
            'xendit_api_key' => 'encrypted',
            'xendit_secret_key' => 'encrypted',
        ];
    }

    // Helper methods
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
}
```

---

## 6. Gateway Interface

### 6.1 PaymentGatewayInterface

```php
// app/Services/Gateways/Contracts/PaymentGatewayInterface.php

namespace App\Services\Gateways\Contracts;

use App\Models\Order;

interface PaymentGatewayInterface
{
    public function getAlias(): string;

    public function getName(): string;

    public function isConfigured(): bool;

    public function getSupportedCurrencies(): array;

    public function createPayment(Order $order, array $params = []): PaymentResponse;

    public function getPaymentStatus(string $transactionId): PaymentStatus;

    public function handleWebhook(array $payload): WebhookResult;

    public function calculateFee(float $amount, string $currency): FeeDetail;
}
```

### 6.2 Response DTOs

```php
// app/Services/Gateways/Contracts/DTOs/

class PaymentResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $transactionId,
        public readonly ?string $paymentUrl,
        public readonly ?string $errorMessage,
        public readonly array $metadata = []
    ) {}
}

class PaymentStatus
{
    public function __construct(
        public readonly string $status, // pending, success, failed, expired
        public readonly ?string $transactionId,
        public readonly ?float $amount,
        public readonly ?string $currency,
        public readonly ?string $errorMessage
    ) {}
}

class WebhookResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $action, // process, ignore, reject
        public readonly ?string $message
    ) {}
}
```

---

## 7. Gateway Implementations

### 7.1 MidtransGateway

**Focus:** Snap API (redirect to Midtrans page)

```php
// app/Services/Gateways/MidtransGateway.php

namespace App\Services\Gateways;

use App\Services\Gateways\Contracts\PaymentGatewayInterface;
use App\Settings\PaymentGatewaySettings;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\CoreApi;

class MidtransGateway implements PaymentGatewayInterface
{
    protected array $config;

    public function __construct(protected PaymentGatewaySettings $settings)
    {
        $this->loadConfig();
    }

    protected function loadConfig(): void
    {
        $creds = $this->settings->getActiveGatewayCredentials();

        Config::$serverKey = $creds['server_key'] ?? null;
        Config::$clientKey = $creds['client_key'] ?? null;
        Config::$merchantId = $creds['merchant_id'] ?? null;
        Config::$isProduction = ($creds['mode'] ?? 'sandbox') === 'production';
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function getAlias(): string
    {
        return 'midtrans';
    }

    public function getName(): string
    {
        return 'Midtrans';
    }

    public function isConfigured(): bool
    {
        $creds = $this->settings->getActiveGatewayCredentials();
        return !empty($creds['server_key']) && !empty($creds['client_key']);
    }

    public function getSupportedCurrencies(): array
    {
        return $this->settings->midtrans_supported_currencies ?? ['IDR'];
    }

    public function createPayment(Order $order, array $params = []): PaymentResponse
    {
        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id' => $item->product_id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product->name,
            ];
        }

        $transactionDetails = [
            'order_id' => $order->order_number,
            'gross_amount' => (int) $order->total,
        ];

        $customerDetails = [
            'first_name' => $order->user->name,
            'email' => $order->user->email,
            'phone' => $order->user->phone ?? '',
        ];

        $payload = [
            'transaction_details' => $transactionDetails,
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
        ];

        try {
            $snapToken = Snap::getSnapToken($payload);

            return new PaymentResponse(
                success: true,
                transactionId: $order->order_number,
                paymentUrl: "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}",
                errorMessage: null,
                metadata: ['snap_token' => $snapToken]
            );
        } catch (\Exception $e) {
            return new PaymentResponse(
                success: false,
                transactionId: null,
                paymentUrl: null,
                errorMessage: $e->getMessage()
            );
        }
    }

    public function getPaymentStatus(string $transactionId): PaymentStatus
    {
        try {
            $result = CoreApi::status($transactionId);

            return new PaymentStatus(
                status: $this->mapStatus($result->transaction_status),
                transactionId: $result->order_id,
                amount: (float) $result->gross_amount,
                currency: $result->currency ?? 'IDR',
                errorMessage: null
            );
        } catch (\Exception $e) {
            return new PaymentStatus(
                status: 'unknown',
                transactionId: $transactionId,
                amount: null,
                currency: null,
                errorMessage: $e->getMessage()
            );
        }
    }

    public function handleWebhook(array $payload): WebhookResult
    {
        // Handle Midtrans notification
        // Update order status based on transaction_status
    }

    protected function mapStatus(string $midtransStatus): string
    {
        return match ($midtransStatus) {
            'capture', 'settlement' => 'success',
            'pending' => 'pending',
            'deny', 'reject' => 'failed',
            'expire' => 'expired',
            default => 'unknown',
        };
    }
}
```

---

## 8. Main Service

### 8.1 PaymentGatewayService

```php
// app/Services/PaymentGatewayService.php

namespace App\Services;

use App\Services\Gateways\Contracts\PaymentGatewayInterface;
use App\Services\Gateways\MidtransGateway;
use App\Settings\PaymentGatewaySettings;
use InvalidArgumentException;

class PaymentGatewayService
{
    protected ?PaymentGatewayInterface $gateway = null;

    public function __construct(
        protected PaymentGatewaySettings $settings
    ) {}

    public function getActiveGateway(): ?PaymentGatewayInterface
    {
        if (!$this->settings->active_gateway) {
            return null;
        }

        return match ($this->settings->active_gateway) {
            'midtrans' => new MidtransGateway($this->settings),
            // 'stripe' => new StripeGateway($this->settings),
            // 'xendit' => new XenditGateway($this->settings),
            default => null,
        };
    }

    public function getActiveGatewayAlias(): ?string
    {
        return $this->settings->active_gateway;
    }

    public function isCurrencySupported(string $currency): bool
    {
        $gateway = $this->getActiveGateway();

        if (!$gateway) {
            return false;
        }

        return in_array($currency, $gateway->getSupportedCurrencies());
    }

    public function getSupportedCurrencies(): array
    {
        $gateway = $this->getActiveGateway();

        return $gateway ? $gateway->getSupportedCurrencies() : [];
    }

    public function createPayment(Order $order, array $params = []): PaymentResponse
    {
        $gateway = $this->getActiveGateway();

        if (!$gateway) {
            throw new InvalidArgumentException('No active payment gateway configured');
        }

        return $gateway->createPayment($order, $params);
    }

    public function getPaymentStatus(string $transactionId): PaymentStatus
    {
        $gateway = $this->getActiveGateway();

        if (!$gateway) {
            throw new InvalidArgumentException('No active payment gateway configured');
        }

        return $gateway->getPaymentStatus($transactionId);
    }

    public function handleWebhook(Request $request): WebhookResult
    {
        $gateway = $this->getActiveGateway();

        if (!$gateway) {
            return new WebhookResult(
                success: false,
                action: 'ignore',
                message: 'No active gateway'
            );
        }

        return $gateway->handleWebhook($request->all());
    }
}
```

---

## 9. Filament Admin Page

### 9.1 ManagePaymentGateway

**Location:** `app/Filament/Pages/ManagePaymentGateway.php`

**UI Structure:**

```
┌─────────────────────────────────────────────────────────────┐
│ Payment Gateway Settings                                    │
├─────────────────────────────────────────────────────────────┤
│ [Midtrans] [Stripe] [Xendit]                                │
│                                                             │
│ ┌─ Midtrans ─────────────────────────────────────────────┐ │
│ │                                                            │ │
│ │  ☑ Set as Active Gateway                                 │ │
│ │                                                            │ │
│ │  Mode:  ○ Sandbox  ○ Production                           │ │
│ │                                                            │ │
│ │  Server Key:    [••••••••••••••••••••] 👁                │ │
│ │  Client Key:    [••••••••••••••••••••] 👁                │ │
│ │  Merchant ID:   [____________________]                    │ │
│ │                                                            │ │
│ │  Supported Currencies:                                    │ │
│ │  [IDR ✓] [USD ✓] [EUR]                                    │ │
│ │                                                            │ │
│ └──────────────────────────────────────────────────────────┘ │
│                                                             │
│ ┌─ Default Settings ──────────────────────────────────────┐ │
│ │  Default Currency: [IDR ▼]                               │ │
│ └──────────────────────────────────────────────────────────┘ │
│                                                             │
│                              [ 💾 Save Settings ]           │
└─────────────────────────────────────────────────────────────┘
```

**Key Features:**

- Tab per gateway (Midtrans, Stripe, Xendit)
- Radio button "Set as Active Gateway" - selecting one deselects others
- Password fields dengan reveal button
- Multi-select currencies dari Currency table
- Mode selector (Sandbox/Production)
- Credentials encrypted at rest

---

## 10. Implementation Files

### 10.1 Database

```
📁 database/
├── migrations/
│   └── xxxx_xx_xx_create_currencies_table.php
├── settings/
│   └── xxxx_xx_xx_create_payment_gateway_settings.php
└── seeders/
    └── CurrencySeeder.php
```

### 10.2 Models

```
📁 app/Models/
└── Currency.php (new)
```

### 10.3 Settings

```
📁 app/Settings/
└── PaymentGatewaySettings.php (new)
```

### 10.4 Services

```
📁 app/Services/
├── PaymentGatewayService.php (update)
└── Gateways/
    ├── Contracts/
    │   └── PaymentGatewayInterface.php (new)
    └── MidtransGateway.php (new)
```

### 10.5 Filament

```
📁 app/Filament/Pages/
└── ManagePaymentGateway.php (new)
```

---

## 11. Workflow

### 11.1 Admin Configure Gateway

```
1. Admin buka /setting/payment-gateway
2. Pilih tab gateway (Midtrans/Stripe/Xendit)
3. Centang "Set as Active Gateway"
4. Masukkan credentials (API keys)
5. Pilih mode (sandbox/production)
6. Pilih supported currencies
7. Klik Save
8. Sistem encrypt credentials dan save
9. Gateway siap digunakan
```

### 11.2 Payment Flow

```
1. Customer checkout dengan total X
2. PaymentGatewayService::createPayment($order)
3. Service get active gateway (MidtransGateway)
4. Gateway create Snap token
5. Return payment URL
6. Customer redirect ke Midtrans
7. Customer complete payment
8. Midtrans callback ke webhook
9. PaymentGatewayService::handleWebhook()
10. Order status updated
```

---

## 12. Security Considerations

### 12.1 Credential Encryption

- API keys di-encrypt menggunakan Laravel's `Crypt` facade
- Encryption key dari `APP_KEY` di `.env`
- Credentials tidak pernah expose di logs atau error messages

### 12.2 Webhook Security

- Verify webhook signature (Midtrans: `x-midtrans-signature`)
- IP whitelist jika memungkinkan
- Idempotent webhook handling

---

## 13. Currency Reference

### ISO 4217 Common Currencies

| Code | Name              | Symbol | Decimal Places |
| ---- | ----------------- | ------ | -------------- |
| IDR  | Indonesian Rupiah | Rp     | 2              |
| USD  | US Dollar         | $      | 2              |
| EUR  | Euro              | €      | 2              |
| GBP  | British Pound     | £      | 2              |
| JPY  | Japanese Yen      | ¥      | 0              |
| SGD  | Singapore Dollar  | S$     | 2              |
| MYR  | Malaysian Ringgit | RM     | 2              |
| AUD  | Australian Dollar | A$     | 2              |
| PHP  | Philippine Peso   | ₱      | 2              |
| THB  | Thai Baht         | ฿      | 2              |

---

## 14. Error Handling

### 14.1 Gateway Errors

| Error                    | Cause                          | Handling                       |
| ------------------------ | ------------------------------ | ------------------------------ |
| `GATEWAY_NOT_CONFIGURED` | No credentials set             | Show admin notification        |
| `GATEWAY_UNAVAILABLE`    | Gateway API down               | Retry with exponential backoff |
| `CURRENCY_NOT_SUPPORTED` | Currency not in supported list | Show error to user             |
| `INVALID_SIGNATURE`      | Webhook signature mismatch     | Log and ignore                 |
| `TRANSACTION_FAILED`     | Payment declined/failed        | Update order status            |

### 14.2 API Error Response

```json
{
    "success": false,
    "error": {
        "code": "CURRENCY_NOT_SUPPORTED",
        "message": "Currency USD is not supported by current payment gateway"
    }
}
```

---

## 15. Testing Scenarios

### 15.1 Unit Tests

- [ ] PaymentGatewaySettings encryption/decryption
- [ ] Gateway selector logic (only one active)
- [ ] Currency support validation
- [ ] MidtransGateway::mapStatus()

### 15.2 Integration Tests

- [ ] Save gateway credentials via Filament
- [ ] Create payment with Midtrans
- [ ] Handle webhook notification
- [ ] Currency switching

---

## 16. Acceptance Criteria

### 16.1 Admin Panel

- [ ] Can view Payment Gateway settings page
- [ ] Can select active gateway (only one)
- [ ] Can enter and save credentials
- [ ] Credentials are encrypted in database
- [ ] Can select supported currencies
- [ ] Can change mode (sandbox/production)

### 16.2 Payment Flow

- [ ] Active gateway is used for payments
- [ ] Payment URL generated correctly
- [ ] Webhook updates order status
- [ ] Failed payments handled gracefully

### 16.3 Currency

- [ ] Currency table seeded with ISO 4217 list
- [ ] Only supported currencies can be selected
- [ ] Unsupported currency shows error

---

## 17. Timeline & Effort

| Phase   | Tasks                                    | Estimated Time |
| ------- | ---------------------------------------- | -------------- |
| Phase 1 | Currency table + seeder + model          | 15 min         |
| Phase 2 | PaymentGatewaySettings migration + model | 20 min         |
| Phase 3 | PaymentGatewayInterface + DTOs           | 15 min         |
| Phase 4 | MidtransGateway implementation           | 45 min         |
| Phase 5 | PaymentGatewayService                    | 20 min         |
| Phase 6 | ManagePaymentGateway Filament page       | 45 min         |
| Phase 7 | Webhook handling + testing               | 30 min         |

**Total Estimated: ~3 hours**

---

## 18. Revision History

| Version | Date     | Author   | Changes              |
| ------- | -------- | -------- | -------------------- |
| 1.0     | Mar 2026 | Dev Team | Initial PRD creation |

---

**Document End**

_This PRD is a living document and subject to change based on user feedback and market conditions._
