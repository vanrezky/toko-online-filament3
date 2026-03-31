<?php

namespace App\Observers;

use App\Jobs\SendOrderStatusChangedNotification;
use App\Models\Transaction;

class TransactionObserver
{
    public function updated(Transaction $transaction): void
    {
        if (! $transaction->isDirty('status')) {
            return;
        }

        $oldStatus = $transaction->getOriginal('status');
        $newStatus = $transaction->status;

        // Skip notification for these status transitions
        $skipTransitions = [
            ['unpaid', 'paid'], // Handled by PaymentWebhookController
            ['unpaid', 'expired'], // Handled by CheckExpiredOrders command
        ];

        foreach ($skipTransitions as [$from, $to]) {
            if ($oldStatus === $from && $newStatus === $to) {
                return;
            }
        }

        // Send status change notification for other transitions
        $notifiableStatuses = ['shipped', 'delivered', 'completed', 'cancelled'];

        if (in_array($newStatus, $notifiableStatuses)) {
            SendOrderStatusChangedNotification::dispatch(
                $transaction,
                $oldStatus,
                $newStatus
            );
        }
    }
}
