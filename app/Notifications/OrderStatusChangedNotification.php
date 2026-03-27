<?php

namespace App\Notifications;

use App\Models\Transaction;
use App\Services\EmailTemplateService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrderStatusChangedNotification extends Notification
{
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

        $statusLabels = [
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Sudah Dibayar',
            'shipped' => 'Dikirim',
            'delivered' => 'Diterima',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
            'expired' => 'Kadaluarsa',
            'cancelled' => 'Dibatalkan',
        ];

        $placeholders = [
            'customer_name' => $notifiable->full_name ?? trim($notifiable->first_name.' '.$notifiable->last_name),
            'order_id' => $transaction->uuid,
            'old_status' => $statusLabels[$this->oldStatus] ?? $this->oldStatus,
            'new_status' => $statusLabels[$this->newStatus] ?? $this->newStatus,
            'tracking_number' => $this->trackingNumber ?? '',
            'courier_name' => $this->courierName ?? '',
            'website_name' => $websiteName,
        ];

        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->send('order_status_changed', $notifiable->email, $placeholders, false);

        if ($template) {
            return (new MailMessage)
                ->html($template->body)
                ->subject($template->subject);
        }

        Log::warning('Email template order_status_changed not found', ['customer_id' => $notifiable->id]);

        return (new MailMessage)
            ->subject("Update Status Order #{$transaction->uuid}")
            ->line("Halo {$placeholders['customer_name']}, status order #{$transaction->uuid} telah diperbarui dari {$placeholders['old_status']} menjadi {$placeholders['new_status']}.");
    }
}
