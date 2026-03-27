<?php

namespace App\Notifications;

use App\Models\Transaction;
use App\Services\EmailTemplateService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrderExpiryNotification extends Notification
{
    public function __construct(
        public Transaction $transaction,
        public string $paymentUrl
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $transaction = $this->transaction;
        $websiteName = settings('general')->site_name ?? env('APP_NAME', 'Toko Online');

        $placeholders = [
            'customer_name' => $notifiable->full_name ?? trim($notifiable->first_name.' '.$notifiable->last_name),
            'order_id' => $transaction->uuid,
            'expiry_time' => $transaction->timelimit ? $transaction->timelimit->format('d M Y, H:i') : 'N/A',
            'payment_url' => $this->paymentUrl,
            'website_name' => $websiteName,
        ];

        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->send('order_expiry', $notifiable->email, $placeholders, false);

        if ($template) {
            return (new MailMessage)
                ->html($template->body)
                ->subject($template->subject);
        }

        Log::warning('Email template order_expiry not found', ['customer_id' => $notifiable->id]);

        return (new MailMessage)
            ->subject("Order #{$transaction->uuid} Kadaluarsa")
            ->line("Halo {$placeholders['customer_name']}, order #{$transaction->uuid} telah kadaluarsa.");
    }
}
