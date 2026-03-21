<?php

namespace App\Services\Gateways\DTOs;

class PaymentStatus
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $transactionId,
        public readonly ?float $amount,
        public readonly ?string $currency,
        public readonly ?string $errorMessage
    ) {}

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'expired']);
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'transaction_id' => $this->transactionId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'error_message' => $this->errorMessage,
        ];
    }
}
