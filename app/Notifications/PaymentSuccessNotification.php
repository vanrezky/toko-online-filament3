<?php

namespace App\Notifications;

use App\Models\Transaction;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PaymentSuccessNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Transaction $transaction,
        public string $paymentMethod = 'Payment Gateway'
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
            'order_total' => number_format($transaction->total_amount, 0, ',', '.'),
            'payment_method' => $this->paymentMethod,
            'transaction_date' => now()->format('d M Y, H:i'),
            'website_name' => $websiteName,
        ];

        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('payment_success');

        if ($template) {
            $renderedSubject = $template->renderSubject($placeholders);
            $renderedBody = $template->renderBody($placeholders);

            return (new MailMessage)
                ->subject($renderedSubject)
                ->view('emails.raw', ['content' => $renderedBody]);
        }

        Log::warning('Email template payment_success not found', ['customer_id' => $notifiable->id]);

        return (new MailMessage)
            ->subject("Pembayaran Berhasil untuk Order #{$transaction->uuid}")
            ->line("Halo {$placeholders['customer_name']}, pembayaran untuk order #{$transaction->uuid} telah kami terima.");
    }
}
