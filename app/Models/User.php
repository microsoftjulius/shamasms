<?php

namespace App\Models;

use App\Services\SendcraneVerificationService;
use Database\Factories\UserFactory;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;

#[Fillable(['name', 'username', 'email', 'phone', 'company', 'password', 'sms_balance', 'sms_unit_price', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'sms_balance' => 'integer',
            'sms_unit_price' => 'integer',
            'is_admin' => 'boolean',
        ];
    }

    public function contactGroups(): HasMany
    {
        return $this->hasMany(ContactGroup::class);
    }

    public function smsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function sendEmailVerificationNotification(): void
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes((int) config('auth.verification.expire', 60)),
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->getEmailForVerification()),
            ],
        );

        $sent = app(SendcraneVerificationService::class)->send(
            $this->email,
            $this->name,
            $url,
        );

        if (! $sent) {
            $this->notify(new VerifyEmail);
        }
    }

    public function sendPasswordResetNotification($token): void
    {
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        $sent = app(\App\Services\SendcranePasswordResetService::class)->send(
            $this->getEmailForPasswordReset(),
            $this->name ?? $this->email,
            $resetUrl,
        );

        if (! $sent) {
            // Fallback to Laravel's default reset email via the log mailer
            $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
        }
    }
}
