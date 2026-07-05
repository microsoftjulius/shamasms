<?php

namespace App\Notifications;

use App\Services\SendcranePasswordResetService;
use Illuminate\Auth\Notifications\ResetPassword as LaravelResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SendcraneResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Override the notification dispatch entirely.
     * Try SendCrane first; fall back to Laravel's built-in reset email.
     */
    public function send(object $notifiable): void
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $sent = app(SendcranePasswordResetService::class)->send(
            $notifiable->getEmailForPasswordReset(),
            $notifiable->name ?? $notifiable->email,
            $resetUrl,
        );

        if (! $sent) {
            // Fallback to Laravel's default mailer-based reset email
            $notifiable->notify(new LaravelResetPassword($this->token));
        }
    }

    public function toMail(object $notifiable): void
    {
        // Not used — send() handles delivery directly
    }
}
