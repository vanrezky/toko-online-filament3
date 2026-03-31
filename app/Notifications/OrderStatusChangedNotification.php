<?php

namespace App\Notifications;

use App\Enums\OrderStatus;
use App\Models\Transaction;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrderStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Transaction $transaction,
        public string $oldStatus,
        public string $newStatus,
        public ?string $trackingNumber = null,
        public ?string $courierName = null
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
            'old_status' => OrderStatus::getLabel($this->oldStatus),
            'new_status' => OrderStatus::getLabel($this->newStatus),
            'tracking_number' => $this->trackingNumber ?? '',
            'courier_name' => $this->courierName ?? '',
            'website_name' => $websiteName,
        ];

        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('order_status_changed');

        if ($template) {
            $renderedSubject = $template->renderSubject($placeholders);
            $renderedBody = $template->renderBody($placeholders);

            return (new MailMessage)
                ->subject($renderedSubject)
                ->view('emails.raw', ['content' => $renderedBody]);
        }

        Log::warning('Email template order_status_changed not found', ['customer_id' => $notifiable->id]);

        return (new MailMessage)
            ->subject("Update Status Order #{$transaction->uuid}")
            ->line("Halo {$placeholders['customer_name']}, status order #{$transaction->uuid} telah diperbarui dari {$placeholders['old_status']} menjadi {$placeholders['new_status']}.");
    }
}
