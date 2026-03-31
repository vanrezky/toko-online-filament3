<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Jobs\SendOrderExpiryNotification;
use App\Jobs\SendOrderExpiryReminder;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredOrders extends Command
{
    protected $signature = 'orders:check-expiry';

    protected $description = 'Check for expired orders and send expiry reminders';

    public function handle(): int
    {
        $this->info('Checking for expired orders...');

        // Process orders that are about to expire (30 minutes before)
        $this->processExpiryReminders();

        // Process orders that have expired
        $this->processExpiredOrders();

        $this->info('Expired orders check completed.');

        return Command::SUCCESS;
    }

    protected function processExpiryReminders(): void
    {
        // Find unpaid orders expiring in the next 30 minutes
        // But NOT already sent reminder
        $reminderThreshold = now()->addMinutes(30);

        $transactions = Transaction::where('status', 'unpaid')
            ->whereNotNull('timelimit')
            ->where('timelimit', '<=', $reminderThreshold)
            ->where('timelimit', '>', now())
            ->whereDoesntHave('emailLogs', function ($query) {
                $query->where('template_code', 'order_expiry_reminder')
                    ->where('created_at', '>=', now()->subHours(2));
            })
            ->with('customer')
            ->get();

        $this->info("Found {$transactions->count()} orders for expiry reminder.");

        foreach ($transactions as $transaction) {
            if (! $transaction->customer) {
                continue;
            }

            try {
                SendOrderExpiryReminder::dispatch($transaction);
                $this->info("Sent expiry reminder for order: {$transaction->uuid}");
            } catch (\Exception $e) {
                Log::error("Failed to send expiry reminder for order {$transaction->uuid}: ".$e->getMessage());
                $this->error("Failed to send expiry reminder for order: {$transaction->uuid}");
            }
        }
    }

    protected function processExpiredOrders(): void
    {
        // Find unpaid orders that have passed their timelimit
        $transactions = Transaction::where('status', 'unpaid')
            ->whereNotNull('timelimit')
            ->where('timelimit', '<', now())
            ->with('customer')
            ->get();

        $this->info("Found {$transactions->count()} expired orders.");

        foreach ($transactions as $transaction) {
            if (! $transaction->customer) {
                continue;
            }

            try {
                // Update status to expired
                $transaction->update(['status' => OrderStatus::Expired->value]);

                // Send expiry notification
                SendOrderExpiryNotification::dispatch($transaction);

                $this->info("Marked order as expired: {$transaction->uuid}");
            } catch (\Exception $e) {
                Log::error("Failed to process expired order {$transaction->uuid}: ".$e->getMessage());
                $this->error("Failed to process expired order: {$transaction->uuid}");
            }
        }
    }
}
