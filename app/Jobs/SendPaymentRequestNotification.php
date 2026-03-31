<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPaymentRequestNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public Transaction $transaction,
        public string $paymentUrl
    ) {}

    public function handle(EmailTemplateService $emailService): void
    {
        $transaction = $this->transaction;
        $customer = $transaction->customer;

        if (! $customer) {
            return;
        }

        $generalSettings = settings('general');
        $websiteName = $generalSettings?->site_name ?? config('app.name');
        $logoUrl = $generalSettings?->getLogo() ?? asset('images/logo.png');

        $placeholders = [
            'customer_name' => $customer->full_name ?? trim($customer->first_name.' '.$customer->last_name),
            'order_id' => $transaction->uuid,
            'order_total' => number_format($transaction->total_amount, 0, ',', '.'),
            'payment_url' => $this->paymentUrl,
            'expiry_time' => $transaction->timelimit?->format('d M Y, H:i') ?? 'N/A',
            'website_name' => $websiteName,
            'logo_url' => $logoUrl,
        ];

        $emailService->send('payment_request', $customer->email, $placeholders, true, 'default');
    }
}
