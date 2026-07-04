<?php

namespace App\Services;

use App\Models\IntegrationSetting;
use Illuminate\Support\Facades\Http;

class SendcraneVerificationService
{
    public function send(string $email, string $name, string $verificationUrl): bool
    {
        $setting = IntegrationSetting::query()
            ->where('provider', 'sendcrane')
            ->where('is_active', true)
            ->latest()
            ->first();

        if (! $setting || $setting->is_sandbox) {
            return true;
        }

        $response = Http::timeout(20)
            ->acceptJson()
            ->withToken((string) $setting->api_key)
            ->post(rtrim((string) $setting->base_url, '/').'/email/send', [
                'to' => $email,
                'name' => $name,
                'template' => data_get($setting->metadata, 'verification_template', 'email-verification'),
                'data' => ['verification_url' => $verificationUrl],
            ]);

        return $response->successful();
    }
}
