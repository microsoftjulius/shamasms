<?php

namespace App\Services;

use App\Models\IntegrationSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendcraneVerificationService
{
    public function send(string $email, string $name, string $verificationUrl): bool
    {
        $config = $this->config();

        if (! $config['api_key']) {
            Log::warning('Sendcrane verification email skipped: missing API key.');

            return false;
        }

        if ($config['is_sandbox']) {
            Log::info('Sendcrane verification email skipped: sandbox mode is enabled.', ['to' => $email]);

            return false;
        }

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->withToken($config['api_key'])
                ->post($config['url'], [
                    'to' => $email,
                    'to_email' => $email,
                    'to_name' => $name,
                    'template_type' => $config['verification_template_type'],
                    'template_name' => 'general_email_verification',
                    'variables' => [
                        'app_name' => config('app.name', 'ShamaSMS'),
                        'user_name' => $name,
                        'name' => $name,
                        'verification_link' => $verificationUrl,  // matches template variable
                        'verification_url' => $verificationUrl,   // kept for compatibility
                        'action_url' => $verificationUrl,
                        'expiry_time' => '60 minutes',
                        'support_email' => config('mail.from.address', 'support@shamasms.com'),
                    ],
                ]);
        } catch (\Throwable $exception) {
            Log::error('Sendcrane verification email request failed.', [
                'to' => $email,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }

        if (! $response->successful()) {
            Log::error('Sendcrane verification email was rejected.', [
                'to' => $email,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
        }

        return $response->successful();
    }

    /**
     * @return array{api_key: string, is_sandbox: bool, url: string, verification_template_type: string}
     */
    private function config(): array
    {
        $setting = IntegrationSetting::query()
            ->where('provider', 'sendcrane')
            ->where('is_active', true)
            ->latest()
            ->first();

        $baseUrl = (string) ($setting?->base_url ?: config('services.sendcrane.base_url'));
        $endpoint = (string) data_get($setting?->metadata, 'endpoint', config('services.sendcrane.endpoint'));

        return [
            'api_key' => (string) ($setting?->api_key ?: config('services.sendcrane.api_key')),
            'is_sandbox' => (bool) ($setting?->is_sandbox ?? config('services.sendcrane.sandbox')),
            'url' => rtrim($baseUrl, '/').'/'.ltrim($endpoint, '/'),
            'verification_template_type' => (string) data_get($setting?->metadata, 'verification_template_type', config('services.sendcrane.verification_template_type')),
        ];
    }
}
