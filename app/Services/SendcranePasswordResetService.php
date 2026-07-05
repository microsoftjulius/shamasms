<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendcranePasswordResetService
{
    public function send(string $email, string $name, string $resetUrl): bool
    {
        $apiKey = config('services.sendcrane.api_key');

        if (! $apiKey) {
            Log::warning('Sendcrane password reset email skipped: missing API key.');
            return false;
        }

        $baseUrl  = rtrim(config('services.sendcrane.base_url', 'https://sendcrane.com/api/v1'), '/');
        $endpoint = ltrim(config('services.sendcrane.endpoint', '/email/send'), '/');
        $url      = $baseUrl . '/' . $endpoint;

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->withToken($apiKey)
                ->post($url, [
                    'to'            => $email,
                    'to_email'      => $email,
                    'to_name'       => $name,
                    'template_name' => 'general_password_reset',
                    'template_type' => 'password_reset',
                    'variables'     => [
                        'app_name'      => config('app.name', 'ShamaSMS'),
                        'user_name'     => $name,
                        'reset_link'    => $resetUrl,
                        'expiry_time'   => config('auth.passwords.users.expire', 60) . ' minutes',
                        'app_logo_url'  => '',
                        'support_email' => config('mail.from.address', 'support@shamasms.com'),
                    ],
                ]);
        } catch (\Throwable $e) {
            Log::error('Sendcrane password reset email request failed.', [
                'to'    => $email,
                'error' => $e->getMessage(),
            ]);
            return false;
        }

        if (! $response->successful()) {
            Log::error('Sendcrane password reset email was rejected.', [
                'to'       => $email,
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);
        }

        return $response->successful();
    }
}
