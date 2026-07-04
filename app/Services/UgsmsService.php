<?php

namespace App\Services;

use App\Models\IntegrationSetting;
use Illuminate\Support\Facades\Http;

class UgsmsService
{
    public function send(?string $senderId, string $phone, string $message): array
    {
        $setting = $this->setting();

        if (! $setting['api_key'] || $setting['is_sandbox']) {
            return [
                'ok' => true,
                'reference' => 'sandbox-'.str()->uuid(),
                'sandbox' => true,
            ];
        }

        $response = Http::timeout(20)
            ->acceptJson()
            ->withHeaders(['X-API-Key' => (string) $setting['api_key']])
            ->post(rtrim((string) $setting['base_url'], '/').'/sms/send', array_filter([
                'sender_id' => $senderId,
                'numbers' => $phone,
                'message_body' => $message,
            ], fn ($value) => filled($value)));

        return [
            'ok' => $response->successful(),
            'reference' => $response->json('reference')
                ?? $response->json('message_id')
                ?? $response->json('data.reference')
                ?? $response->json('data.message_id'),
            'payload' => $response->json(),
        ];
    }

    public function balance(): array
    {
        $setting = $this->setting();

        if (! $setting['api_key'] || $setting['is_sandbox']) {
            return ['ok' => true, 'credits' => 10000, 'sandbox' => true];
        }

        $response = Http::timeout(20)
            ->acceptJson()
            ->withHeaders(['X-API-Key' => (string) $setting['api_key']])
            ->get(rtrim((string) $setting['base_url'], '/').'/account/balance');

        return [
            'ok' => $response->successful(),
            'credits' => (int) (
                $response->json('credits')
                ?? $response->json('balance')
                ?? $response->json('data.balance')
                ?? 0
            ),
            'payload' => $response->json(),
        ];
    }

    private function setting(): array
    {
        $setting = IntegrationSetting::query()
            ->whereIn('provider', ['sms_gateway', 'ugsms'])
            ->where('is_active', true)
            ->latest()
            ->first();

        if ($setting) {
            return [
                'base_url' => $setting->base_url ?: config('services.ugsms.base_url'),
                'api_key' => $setting->api_key,
                'is_sandbox' => $setting->is_sandbox,
            ];
        }

        return [
            'base_url' => config('services.ugsms.base_url'),
            'api_key' => config('services.ugsms.api_key'),
            'is_sandbox' => (bool) config('services.ugsms.sandbox'),
        ];
    }
}
