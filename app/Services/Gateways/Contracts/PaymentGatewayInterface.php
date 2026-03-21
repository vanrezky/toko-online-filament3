<?php

namespace App\Services\Gateways\Contracts;

use App\Models\Transaction;
use App\Services\Gateways\DTOs\PaymentResponse;
use App\Services\Gateways\DTOs\PaymentStatus;
use App\Services\Gateways\DTOs\WebhookResult;

interface PaymentGatewayInterface
{
    public function getAlias(): string;

    public function getName(): string;

    public function isConfigured(): bool;

    public function getSupportedCurrencies(): array;

    public function createPayment(Transaction $order, array $params = []): PaymentResponse;

    public function getPaymentStatus(string $transactionId): PaymentStatus;

    public function handleWebhook(array $payload): WebhookResult;
}
