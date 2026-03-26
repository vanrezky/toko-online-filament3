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

class StripeGateway implements PaymentGatewayInterface
{
    protected Client $client;
    protected ?string $apiKey;

    public function __construct(protected PaymentGatewaySettings $settings)
    {
        $creds = $this->settings->getActiveGatewayCredentials();
        $this->apiKey = $creds['api_key'] ?? null;
        $this->client = new Client([
            'base_uri' => 'https://api.stripe.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);
    }

    public function getAlias(): string
    {
        return 'stripe';
    }

    public function getName(): string
    {
        return 'Stripe';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function getSupportedCurrencies(): array
    {
        return $this->settings->getSupportedCurrencies('stripe');
    }

    public function createPayment(Transaction $transaction, array $params = []): PaymentResponse
    {
        try {
            $transaction->loadMissing(['products', 'customer']);

            $subtotal = $transaction->products->sum(fn($p) => ($p->price - $p->discount) * $p->quantity);
            $totalAmount = $subtotal + $transaction->shipping_cost + $transaction->cod_fee;

            $response = $this->client->post('checkout/sessions', [
                'form_params' => [
                    'success_url' => route('frontend.orders.show', $transaction->uuid) . '?payment=success',
                    'cancel_url' => route('frontend.orders.show', $transaction->uuid) . '?payment=cancel',
                    'line_items' => [
                        [
                            'price_data' => [
                                'currency' => 'idr',
                                'product_data' => [
                                    'name' => 'Order #' . substr($transaction->uuid, 0, 8),
                                ],
                                'unit_amount' => (int) ($totalAmount * 1), // IDR smallest unit is the currency itself if no cents
                            ],
                            'quantity' => 1,
                        ],
                    ],
                    'mode' => 'payment',
                    'client_reference_id' => $transaction->uuid,
                    'customer_email' => $transaction->customer?->email,
                    'metadata' => [
                        'transaction_uuid' => $transaction->uuid,
                    ],
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            return new PaymentResponse(
                success: true,
                transactionId: $result['id'],
                paymentUrl: $result['url'],
                errorMessage: null,
                metadata: [
                    'session_id' => $result['id'],
                ]
            );
        } catch (\Exception $e) {
            Log::error('Stripe payment creation failed', [
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
            $response = $this->client->get('checkout/sessions/' . $transactionId);
            $result = json_decode($response->getBody()->getContents(), true);

            $status = 'unknown';
            if ($result['payment_status'] === 'paid') {
                $status = 'success';
            } elseif ($result['status'] === 'expired') {
                $status = 'expired';
            }

            return new PaymentStatus(
                status: $status,
                transactionId: $result['client_reference_id'] ?? $transactionId,
                amount: (float) ($result['amount_total'] / 1),
                currency: strtoupper($result['currency']),
                errorMessage: null
            );
        } catch (\Exception $e) {
            Log::error('Stripe status check failed', [
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
        // Simple implementation for now. In production, signature verification is required.
        // Stripe sends the whole event object in payload.
        try {
            $type = $payload['type'] ?? '';
            
            if ($type === 'checkout.session.completed') {
                $session = $payload['data']['object'];
                return new WebhookResult(
                    success: true,
                    action: WebhookResult::ACTION_PROCESS,
                    message: 'Stripe checkout completed',
                    transactionId: $session['client_reference_id'] ?? ($session['metadata']['transaction_uuid'] ?? null),
                    status: 'success'
                );
            }

            return new WebhookResult(
                success: true,
                action: WebhookResult::ACTION_IGNORE,
                message: 'Event ignored: ' . $type
            );
        } catch (\Exception $e) {
            return new WebhookResult(
                success: false,
                action: WebhookResult::ACTION_IGNORE,
                message: $e->getMessage()
            );
        }
    }
}
