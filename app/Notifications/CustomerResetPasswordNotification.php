<?php

namespace App\Notifications;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class CustomerResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public string $token;

    public string $guard;

    protected ?int $emailLogId = null;

    public function __construct(string $token, string $guard = 'customer')
    {
        $this->token = $token;
        $this->guard = $guard;
    }

    public function getEmailLogId(): ?int
    {
        return $this->emailLogId;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = route('frontend.reset-password', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
            'guard' => $this->guard,
        ]);

        $expiryMinutes = config('auth.passwords.'.$this->getPasswordBrokerKey().'.expire', 60);
        $generalSettings = settings('general');
        $websiteName = $generalSettings?->site_name ?? env('APP_NAME', 'Toko Online');
        $logoUrl = $generalSettings?->getLogo() ?? asset('images/logo.png');

        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('reset_password');

        $placeholders = [
            'customer_name' => $notifiable->full_name ?? trim($notifiable->first_name.' '.$notifiable->last_name),
            'reset_url' => $resetUrl,
            'expiry_minutes' => $expiryMinutes,
            'website_name' => $websiteName,
            'logo_url' => $logoUrl,
        ];

        if ($template) {
            $renderedSubject = $template->renderSubject($placeholders);
            $renderedBody = $template->renderBody($placeholders);

            $emailLog = $this->createEmailLog($template, $notifiable, $renderedSubject, $renderedBody, $placeholders);
            $this->emailLogId = $emailLog->id;

            return (new MailMessage)
                ->subject($renderedSubject)
                ->view('emails.raw', ['content' => $renderedBody]);
        }

        $fallbackSubject = Lang::get('Reset Password Notification');
        $fallbackBody = Lang::get('You are receiving this email because we received a password reset request for your account.');

        $emailLog = $this->createEmailLog(null, $notifiable, $fallbackSubject, $fallbackBody, $placeholders);
        $this->emailLogId = $emailLog->id;

        return (new MailMessage)
            ->subject($fallbackSubject)
            ->line($fallbackBody)
            ->action(Lang::get('Reset Password'), $resetUrl)
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => $expiryMinutes]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }

    protected function getPasswordBrokerKey(): string
    {
        return $this->guard === 'customer' ? 'customers' : 'users';
    }

    protected function createEmailLog(?EmailTemplate $template, object $notifiable, string $subject, string $body, array $placeholders): EmailLog
    {
        return EmailLog::create([
            'email_template_id' => $template?->id,
            'template_code' => 'reset_password',
            'recipient_email' => $notifiable->email,
            'subject' => $subject,
            'body' => $body,
            'placeholders' => $placeholders,
            'status' => 'pending',
            'reference_type' => get_class($notifiable),
            'reference_id' => $notifiable->id,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        if ($this->emailLogId) {
            EmailLog::where('id', $this->emailLogId)->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);
        }

        Log::error('Password reset email failed', [
            'email_log_id' => $this->emailLogId,
            'error' => $exception->getMessage(),
        ]);
    }
}
