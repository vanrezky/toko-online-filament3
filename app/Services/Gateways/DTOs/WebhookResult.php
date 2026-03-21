<?php

namespace App\Services\Gateways\DTOs;

class WebhookResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $action,
        public readonly ?string $message
    ) {}

    public const ACTION_PROCESS = 'process';
    public const ACTION_IGNORE = 'ignore';
    public const ACTION_REJECT = 'reject';

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'action' => $this->action,
            'message' => $this->message,
        ];
    }
}
