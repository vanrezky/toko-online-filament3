<?php

namespace App\Services;

use App\Models\Transaction;
use App\Services\Gateways\Contracts\PaymentGatewayInterface;
use App\Services\Gateways\DTOs\PaymentResponse;
use App\Services\Gateways\DTOs\PaymentStatus;
use App\Services\Gateways\DTOs\WebhookResult;
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
            default => null,
        };
    }

    public function getActiveGatewayAlias(): ?string
    {
        return $this->settings->active_gateway;
    }

    public function getActiveGatewayName(): ?string
    {
        $gateway = $this->getActiveGateway();

        return $gateway?->getName();
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

    public function createPayment(Transaction $transaction, array $params = []): PaymentResponse
    {
        $gateway = $this->getActiveGateway();

        if (!$gateway) {
            return new PaymentResponse(
                success: false,
                transactionId: null,
                paymentUrl: null,
                errorMessage: 'No active payment gateway configured'
            );
        }

        if (!$gateway->isConfigured()) {
            return new PaymentResponse(
                success: false,
                transactionId: null,
                paymentUrl: null,
                errorMessage: 'Active payment gateway is not configured properly'
            );
        }

        return $gateway->createPayment($transaction, $params);
    }

    public function getPaymentStatus(string $transactionId): PaymentStatus
    {
        $gateway = $this->getActiveGateway();

        if (!$gateway) {
            return new PaymentStatus(
                status: 'unknown',
                transactionId: $transactionId,
                amount: null,
                currency: null,
                errorMessage: 'No active payment gateway configured'
            );
        }

        return $gateway->getPaymentStatus($transactionId);
    }

    public function handleWebhook(array $payload): WebhookResult
    {
        $gateway = $this->getActiveGateway();

        if (!$gateway) {
            return new WebhookResult(
                success: false,
                action: WebhookResult::ACTION_IGNORE,
                message: 'No active gateway'
            );
        }

        return $gateway->handleWebhook($payload);
    }

    public function isGatewayActive(string $alias): bool
    {
        return $this->settings->isGatewayActive($alias);
    }

    public function isGatewayConfigured(string $alias): bool
    {
        return $this->settings->isConfigured($alias);
    }

    public function getSettings(): PaymentGatewaySettings
    {
        return $this->settings;
    }
}
