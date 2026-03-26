<?php

namespace App\Services\Gateways;

use App\Models\Transaction;
use App\Services\Gateways\Contracts\PaymentGatewayInterface;
use App\Services\Gateways\DTOs\PaymentResponse;
use App\Services\Gateways\DTOs\PaymentStatus;
use App\Services\Gateways\DTOs\WebhookResult;
use App\Settings\PaymentGatewaySettings;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditGateway implements PaymentGatewayInterface
{
    protected Client $client;
    protected ?string $apiKey;

    public function __construct(protected PaymentGatewaySettings $settings)
    {
        $creds = $this->settings->getActiveGatewayCredentials();
        $this->apiKey = $creds['api_key'] ?? null;
        $this->client = new Client([
            'base_uri' => 'https://api.xendit.co/',
            'auth' => [$this->apiKey, ''],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function getAlias(): string
    {
        return 'xendit';
    }

    public function getName(): string
    {
        return 'Xendit';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function getSupportedCurrencies(): array
    {
        return $this->settings->getSupportedCurrencies('xendit');
    }

    public function createPayment(Transaction $transaction, array $params = []): PaymentResponse
    {
        try {
            $transaction->loadMissing(['products', 'customer']);

            $subtotal = $transaction->products->sum(fn($p) => ($p->price - $p->discount) * $p->quantity);
            $totalAmount = $subtotal + $transaction->shipping_cost + $transaction->cod_fee;

            $payload = [
                'external_id' => $transaction->uuid,
                'amount' => (int) $totalAmount,
                'payer_email' => $transaction->customer?->email ?? '',
                'description' => 'Order #' . substr($transaction->uuid, 0, 8),
                'success_redirect_url' => route('frontend.orders.show', $transaction->uuid) . '?payment=success',
                'failure_redirect_url' => route('frontend.orders.show', $transaction->uuid) . '?payment=failure',
            ];

            $response = $this->client->post('v2/invoices', [
                'json' => $payload,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            return new PaymentResponse(
                success: true,
                transactionId: $result['id'],
                paymentUrl: $result['invoice_url'] ?? null,
                errorMessage: null,
                metadata: [
                    'id' => $result['id'],
                    'external_id' => $result['external_id'],
                ]
            );
        } catch (\Exception $e) {
            Log::error('Xendit payment creation failed', [
                'transaction_id' => $transaction->uuid,
                'error' => $e->getMessage(),
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
            $response = $this->client->get('v2/invoices/' . $transactionId);
            $result = json_decode($response->getBody()->getContents(), true);

            $status = $this->mapStatus($result['status'] ?? 'unknown');

            return new PaymentStatus(
                status: $status,
                transactionId: $result['external_id'] ?? $transactionId,
                amount: (float) ($result['amount'] ?? 0),
                currency: $result['currency'] ?? 'IDR',
                errorMessage: null
            );
        } catch (\Exception $e) {
            Log::error('Xendit status check failed', [
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
        // Simple implementation for now. Signature verification should use x-callback-token.
        try {
            $status = $payload['status'] ?? '';
            
            if ($status === 'PAID' || $status === 'SETTLED') {
                return new WebhookResult(
                    success: true,
                    action: WebhookResult::ACTION_PROCESS,
                    message: 'Xendit invoice paid',
                    transactionId: $payload['external_id'] ?? null,
                    status: $this->mapStatus($status)
                );
            }

            return new WebhookResult(
                success: true,
                action: WebhookResult::ACTION_IGNORE,
                message: 'Status ignored: ' . $status
            );
        } catch (\Exception $e) {
            return new WebhookResult(
                success: false,
                action: WebhookResult::ACTION_IGNORE,
                message: $e->getMessage()
            );
        }
    }

    protected function mapStatus(string $xenditStatus): string
    {
        return match ($xenditStatus) {
            'PAID', 'SETTLED' => 'success',
            'PENDING' => 'pending',
            'EXPIRED' => 'expired',
            default => 'unknown',
        };
    }
}
