<?php

namespace App\Services;

use App\Models\IntegrationSetting;
use Illuminate\Support\Facades\Http;

class IotecPaymentService
{
    public function collect(int $amount, string $phone): array
    {
        $setting = $this->setting();

        if (! $setting || $setting->is_sandbox) {
            return [
                'ok' => true,
                'reference' => 'iotec-sandbox-'.str()->uuid(),
                'status' => 'pending',
                'sandbox' => true,
            ];
        }

        $response = Http::timeout(20)
            ->acceptJson()
            ->withToken((string) $setting->api_key)
            ->post(rtrim((string) $setting->base_url, '/').'/collections', [
                'amount' => $amount,
                'phone' => $phone,
                'currency' => data_get($setting->metadata, 'currency', 'UGX'),
            ]);

        return [
            'ok' => $response->successful(),
            'reference' => $response->json('reference') ?? $response->json('transaction_id'),
            'status' => $response->json('status') ?? 'pending',
            'payload' => $response->json(),
        ];
    }

    private function setting(): ?IntegrationSetting
    {
        return IntegrationSetting::query()
            ->where('provider', 'iotec')
            ->where('is_active', true)
            ->latest()
            ->first();
    }
}
