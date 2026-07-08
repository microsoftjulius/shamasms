<?php

namespace App\Services;

use App\Models\IntegrationSetting;
use Illuminate\Support\Facades\Http;

class IotecPaymentService
{
    public function collect(int $amount, string $phone): array
    {
        $setting = $this->setting();

        if (! $setting) {
            return [
                'ok' => false,
                'status' => 'failed',
                'message' => 'No active Iotec integration is configured.',
            ];
        }

        if ($setting->is_sandbox) {
            return [
                'ok' => false,
                'status' => 'failed',
                'message' => 'Sandbox payments are disabled. Turn off Sandbox on the Iotec integration to make live payment requests.',
            ];
        }

        $walletId = (string) data_get($setting->metadata, 'wallet_id', '');

        if ($walletId === '') {
            return [
                'ok' => false,
                'status' => 'failed',
                'message' => 'The active Iotec integration is missing the wallet ID.',
            ];
        }

        $payload = [
            'walletId' => $walletId,
            'category' => 'MobileMoney',
            'amount' => $amount,
            'payer' => $phone,
            'currency' => data_get($setting->metadata, 'currency', 'UGX'),
            'payerNote' => 'SMS credit purchase',
            'payeeNote' => 'ShamaSMS credit purchase',
            'externalId' => 'shamasms-'.str()->uuid(),
        ];

        $token = $this->accessToken($setting);

        if (! $token) {
            return [
                'ok' => false,
                'status' => 'failed',
                'message' => 'Could not get an Iotec access token. Check the client ID and API secret.',
            ];
        }

        $response = Http::timeout(20)
            ->acceptJson()
            ->withToken($token)
            ->post($this->baseUrl((string) $setting->base_url).'/api/collections/collect', $payload);

        return [
            'ok' => $response->successful(),
            'reference' => $response->json('reference') ?? $response->json('transaction_id'),
            'status' => $response->successful() ? ($response->json('status') ?? 'pending') : 'failed',
            'message' => $response->json('message')
                ?? $response->json('error')
                ?? ($response->successful() ? null : 'Iotec rejected the payment request.'),
            'http_status' => $response->status(),
            'payload' => $response->json(),
            'request' => [
                'wallet_id' => $walletId,
                'phone' => $phone,
                'amount' => $amount,
            ],
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

    private function baseUrl(string $baseUrl): string
    {
        $baseUrl = rtrim($baseUrl, '/');

        foreach (['/api/collections/collect', '/api/collections', '/collections'] as $endpoint) {
            if (str_ends_with($baseUrl, $endpoint)) {
                return substr($baseUrl, 0, -strlen($endpoint));
            }
        }

        return $baseUrl;
    }

    private function accessToken(IntegrationSetting $setting): ?string
    {
        $response = Http::timeout(20)
            ->asForm()
            ->post('https://id.iotec.io/connect/token', [
                'client_id' => $setting->api_key,
                'client_secret' => $setting->api_secret,
                'grant_type' => 'client_credentials',
            ]);

        return $response->successful() ? $response->json('access_token') : null;
    }
}
