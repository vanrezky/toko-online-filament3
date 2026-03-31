<?php

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Models\Transaction;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderStatusChangedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public Transaction $transaction,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function handle(EmailTemplateService $emailService): void
    {
        $transaction = $this->transaction;
        $customer = $transaction->customer;

        if (! $customer) {
            return;
        }

        $shippingDetail = $transaction->shippingDetails()->latest()->first();

        $generalSettings = settings('general');
        $websiteName = $generalSettings?->site_name ?? config('app.name');
        $logoUrl = $generalSettings?->getLogo() ?? asset('images/logo.png');

        $placeholders = [
            'customer_name' => $customer->full_name ?? trim($customer->first_name.' '.$customer->last_name),
            'order_id' => $transaction->uuid,
            'old_status' => OrderStatus::getLabel($this->oldStatus),
            'new_status' => OrderStatus::getLabel($this->newStatus),
            'tracking_number' => $shippingDetail?->tracking_number ?? '',
            'courier_name' => $shippingDetail?->courier_name ?? '',
            'website_name' => $websiteName,
            'logo_url' => $logoUrl,
        ];

        $emailService->send('order_status_changed', $customer->email, $placeholders, true, 'default');
    }
}
