<?php

namespace App\Services;

use App\Jobs\SendEmailJob;
use App\Models\Customer;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class EmailTemplateService
{
    public function send(string $code, string $email, array $placeholders = [], bool $queue = true): ?EmailLog
    {
        $template = EmailTemplate::getByCode($code);

        if (! $template) {
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
            SendEmailJob::dispatch($emailLog);
        } else {
            $this->sendNow($emailLog);
        }

        return $emailLog;
    }

    public function sendToCustomer(string $code, Customer $customer, array $placeholders = [], bool $queue = true): ?EmailLog
    {
        $customerPlaceholders = array_merge($placeholders, [
            'customer_name' => $customer->full_name ?? trim($customer->first_name.' '.$customer->last_name),
            'email' => $customer->email,
        ]);

        return $this->send($code, $customer->email, $customerPlaceholders, $queue);
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
