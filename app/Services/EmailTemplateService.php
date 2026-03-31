<?php

namespace App\Services;

use App\Jobs\SendEmailJob;
use App\Models\Customer;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailTemplateService
{
    public function send(string $code, string $email, array $placeholders = [], bool $queue = true, string $queuePriority = 'default'): ?EmailLog
    {
        $template = EmailTemplate::getByCode($code);

        if (! $template) {
            Log::warning("Email template not found: {$code}");

            return null;
        }

        $renderedSubject = $template->renderSubject($placeholders);
        $renderedBody = $template->renderBody($placeholders);

        $emailLog = EmailLog::create([
            'email_template_id' => $template->id,
            'template_code' => $code,
            'recipient_email' => $email,
            'subject' => $renderedSubject,
            'body' => $renderedBody,
            'placeholders' => $placeholders,
            'status' => 'pending',
        ]);

        if ($queue) {
            SendEmailJob::dispatch($emailLog)->onQueue($queuePriority);
        } else {
            $this->sendNow($emailLog);
        }

        // Send to admin emails if enabled
        if ($template->send_to_admin) {
            $this->sendToAdmins($template, $placeholders, $queue, $queuePriority);
        }

        return $emailLog;
    }

    public function sendToCustomer(string $code, Customer $customer, array $placeholders = [], bool $queue = true, string $queuePriority = 'default'): ?EmailLog
    {
        $customerPlaceholders = array_merge($placeholders, [
            'customer_name' => $customer->full_name ?? trim($customer->first_name.' '.$customer->last_name),
            'email' => $customer->email,
        ]);

        return $this->send($code, $customer->email, $customerPlaceholders, $queue, $queuePriority);
    }

    public function getTemplate(string $code): ?EmailTemplate
    {
        return EmailTemplate::getByCode($code);
    }

    protected function sendToAdmins(EmailTemplate $template, array $placeholders, bool $queue = true, string $queuePriority = 'default'): void
    {
        $adminEmails = settings('general')->admin_emails;

        if (! $adminEmails) {
            return;
        }

        $emails = array_map('trim', explode(',', $adminEmails));
        $emails = array_filter($emails, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        if (empty($emails)) {
            return;
        }

        // Add admin indicator to placeholders
        $adminPlaceholders = array_merge($placeholders, [
            'is_admin_copy' => true,
        ]);

        foreach ($emails as $adminEmail) {
            $renderedSubject = '[Admin Copy] '.$template->renderSubject($placeholders);
            $renderedBody = $template->renderBody($adminPlaceholders);

            $emailLog = EmailLog::create([
                'email_template_id' => $template->id,
                'template_code' => $template->code,
                'recipient_email' => $adminEmail,
                'subject' => $renderedSubject,
                'body' => $renderedBody,
                'placeholders' => $adminPlaceholders,
                'status' => 'pending',
            ]);

            if ($queue) {
                SendEmailJob::dispatch($emailLog)->onQueue('low');
            } else {
                $this->sendNow($emailLog);
            }
        }
    }

    public function sendNow(EmailLog $emailLog): bool
    {
        try {
            Mail::html($emailLog->body, function ($message) use ($emailLog) {
                $message->to($emailLog->recipient_email)
                    ->subject($emailLog->subject);
            });

            $emailLog->markAsSent();

            return true;
        } catch (\Exception $e) {
            $emailLog->markAsFailed($e->getMessage());

            return false;
        }
    }

    public function preview(EmailTemplate $template, array $placeholders = []): array
    {
        return [
            'subject' => $template->renderSubject($placeholders),
            'body' => $template->renderBody($placeholders),
        ];
    }
}
