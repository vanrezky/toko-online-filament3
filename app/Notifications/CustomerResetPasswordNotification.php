<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class CustomerResetPasswordNotification extends Notification
{
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

        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Reset Password'), $resetUrl)
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.' . $this->getPasswordBrokerKey() . '.expire', 60)]))
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