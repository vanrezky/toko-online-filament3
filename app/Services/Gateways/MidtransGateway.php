<?php

namespace App\Services\Gateways;

use App\Models\Transaction;
use App\Services\Gateways\Contracts\PaymentGatewayInterface;
use App\Services\Gateways\DTOs\PaymentResponse;
use App\Services\Gateways\DTOs\PaymentStatus;
use App\Services\Gateways\DTOs\WebhookResult;
use App\Settings\PaymentGatewaySettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\CoreApi;

class MidtransGateway implements PaymentGatewayInterface
{
    protected array $config = [];

    public function __construct(protected PaymentGatewaySettings $settings)
    {
        $this->loadConfig();
    }

    protected function loadConfig(): void
    {
        $creds = $this->settings->getActiveGatewayCredentials();

        if (!$creds) {
            Log::warning('Midtrans: No active gateway credentials found');
            return;
        }

        $serverKey = $creds['server_key'] ?? null;
        $clientKey = $creds['client_key'] ?? null;

        if (!$serverKey || !$clientKey) {
            Log::warning('Midtrans: Server key or client key is empty', [
                'has_server_key' => !empty($serverKey),
                'has_client_key' => !empty($clientKey),
                'mode' => $creds['mode'] ?? 'sandbox',
            ]);
        }

        Config::$serverKey = $serverKey;
        Config::$clientKey = $clientKey;
        Config::$isProduction = ($creds['mode'] ?? 'sandbox') === 'production';
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $this->config = $creds;
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

        if (!$creds) {
            return false;
        }

        return !empty($creds['server_key']) && !empty($creds['client_key']);
    }

    public function getSupportedCurrencies(): array
    {
        return $this->settings->getSupportedCurrencies('midtrans');
    }

    public function createPayment(Transaction $transaction, array $params = []): PaymentResponse
    {
        if (!$this->isConfigured()) {
            return new PaymentResponse(
                success: false,
                transactionId: null,
                paymentUrl: null,
                errorMessage: 'Midtrans is not configured properly'
            );
        }

        try {
            // Ensure products and their related product info are loaded
            $transaction->loadMissing(['products.product', 'customer']);

            $itemDetails = [];
            $totalAmount = 0;

            foreach ($transaction->products as $item) {
                $price = (int) $item->price;
                $discount = (int) $item->discount;
                $netPrice = $price - $discount;
                $quantity = (int) $item->quantity;
                $itemDetails[] = [
                    'id' => $item->product?->uuid ?? $item->id,
                    'price' => $netPrice,
                    'quantity' => $quantity,
                    'name' => $item->product?->name ?? ('Product #' . $item->product_id),
                ];
                $totalAmount += $netPrice * $quantity;
            }

            if ($transaction->shipping_cost > 0) {
                $itemDetails[] = [
                    'id' => 'shipping',
                    'price' => (int) $transaction->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Shipping Cost',
                ];
                $totalAmount += $transaction->shipping_cost;
            }

            if ($transaction->cod_fee > 0) {
                $itemDetails[] = [
                    'id' => 'cod_fee',
                    'price' => (int) $transaction->cod_fee,
                    'quantity' => 1,
                    'name' => 'COD Fee',
                ];
                $totalAmount += $transaction->cod_fee;
            }

            $transactionDetails = [
                'order_id' => $transaction->uuid,
                'gross_amount' => (int) $totalAmount,
            ];

            $customerDetails = [];
            $customer = $transaction->customer;
            if ($customer) {
                $customerDetails = [
                    'first_name' => $customer->name ?? 'Customer',
                    'email' => $customer->email ?? '',
                    'phone' => $customer->phone ?? '',
                ];
            }

            $payload = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
            ];

            if (isset($params['callback_url'])) {
                $payload['gopay'] = [
                    'callback_url' => $params['callback_url'],
                ];
            }

            Log::info('Midtrans: Creating Snap token', [
                'order_id' => $transaction->uuid,
                'gross_amount' => $totalAmount,
                'mode' => Config::$isProduction ? 'production' : 'sandbox',
                'has_server_key' => !empty(Config::$serverKey),
            ]);

            $snapToken = Snap::getSnapToken($payload);

            $paymentUrl = Config::$isProduction
                ? "https://app.midtrans.com/snap/v2/vtweb/{$snapToken}"
                : "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}";

            return new PaymentResponse(
                success: true,
                transactionId: $transaction->uuid,
                paymentUrl: $paymentUrl,
                errorMessage: null,
                metadata: [
                    'snap_token' => $snapToken,
                    'client_key' => Config::$clientKey,
                    'mode' => Config::$isProduction ? 'production' : 'sandbox',
                ]
            );
        } catch (\Exception $e) {
            Log::error('Midtrans payment creation failed', [
                'transaction_id' => $transaction->uuid,
                'error' => $e->getMessage(),
                'mode' => Config::$isProduction ? 'production' : 'sandbox',
                'has_server_key' => !empty(Config::$serverKey),
                'server_key_prefix' => Config::$serverKey ? substr(Config::$serverKey, 0, 8) . '...' : 'EMPTY',
            ]);

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
            $result = \Midtrans\Transaction::status($transactionId);

            return new PaymentStatus(
                status: $this->mapStatus($result->transaction_status ?? 'unknown'),
                transactionId: $result->order_id ?? $transactionId,
                amount: (float) ($result->gross_amount ?? 0),
                currency: $result->currency ?? 'IDR',
                errorMessage: null
            );
        } catch (\Exception $e) {
            Log::error('Midtrans status check failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

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
        try {
            $transactionId = $payload['order_id'] ?? null;
            $status = $payload['transaction_status'] ?? null;
            $statusCode = $payload['status_code'] ?? null;
            $grossAmount = $payload['gross_amount'] ?? null;
            $signatureKey = $payload['signature_key'] ?? null;

            if (!$transactionId || !$status) {
                return new WebhookResult(
                    success: false,
                    action: WebhookResult::ACTION_IGNORE,
                    message: 'Invalid webhook payload'
                );
            }

            $creds = $this->settings->getActiveGatewayCredentials();
            $serverKey = $creds['server_key'] ?? '';

            // Midtrans signature: SHA512(order_id + status_code + gross_amount + server_key)
            $signature = hash('sha512', $transactionId . $statusCode . $grossAmount . $serverKey);

            if ($signature !== $signatureKey) {
                Log::warning('Midtrans webhook signature mismatch', [
                    'transaction_id' => $transactionId,
                ]);

                return new WebhookResult(
                    success: false,
                    action: WebhookResult::ACTION_REJECT,
                    message: 'Invalid signature'
                );
            }

            return new WebhookResult(
                success: true,
                action: WebhookResult::ACTION_PROCESS,
                message: 'Webhook processed successfully',
                transactionId: $transactionId,
                status: $this->mapStatus($status)
            );
        } catch (\Exception $e) {
            Log::error('Midtrans webhook handling failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return new WebhookResult(
                success: false,
                action: WebhookResult::ACTION_IGNORE,
                message: $e->getMessage()
            );
        }
    }

    protected function mapStatus(string $midtransStatus): string
    {
        return match ($midtransStatus) {
            'capture', 'settlement' => 'success',
            'pending' => 'pending',
            'deny', 'reject' => 'failed',
            'expire' => 'expired',
            'cancel' => 'cancelled',
            default => 'unknown',
        };
    }
}
