<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Notifications\Events\NotificationSent;

class MarkEmailLogAsSent
{
    public function handle(NotificationSent $event): void
    {
        if (! method_exists($event->notification, 'getEmailLogId')) {
            return;
        }

        $emailLogId = $event->notification->getEmailLogId();

        if ($emailLogId) {
            EmailLog::where('id', $emailLogId)
                ->where('status', 'pending')
                ->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
        }
    }
}
