<?php

namespace App\Notifications;

use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class CustomerResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    public string $guard;

    public function __construct(string $token, string $guard = 'customer')
    {
        $this->token = $token;
        $this->guard = $guard;
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

        if ($template) {
            $placeholders = [
                'customer_name' => $notifiable->full_name ?? trim($notifiable->first_name.' '.$notifiable->last_name),
                'reset_url' => $resetUrl,
                'expiry_minutes' => $expiryMinutes,
                'website_name' => $websiteName,
                'logo_url' => $logoUrl,
            ];

            $renderedSubject = $template->renderSubject($placeholders);
            $renderedBody = $template->renderBody($placeholders);

            return (new MailMessage)
                ->subject($renderedSubject)
                ->view('emails.raw', ['content' => $renderedBody]);
        }

        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Reset Password'), $resetUrl)
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => $expiryMinutes]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }

    protected function getPasswordBrokerKey(): string
    {
        return $this->guard === 'customer' ? 'customers' : 'users';
    }

    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token,
            'guard' => $this->guard,
        ];
    }
}
