<?php

namespace App\Services\Gateways\DTOs;

class PaymentResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $transactionId,
        public readonly ?string $paymentUrl,
        public readonly ?string $errorMessage,
        public readonly array $metadata = []
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'transaction_id' => $this->transactionId,
            'payment_url' => $this->paymentUrl,
            'error_message' => $this->errorMessage,
            'metadata' => $this->metadata,
        ];
    }
}
