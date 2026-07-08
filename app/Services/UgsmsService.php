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
            ->post($this->baseUrl($setting['base_url']).'/sms/send', array_filter([
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
        $unitPrice = $this->unitPrice($setting);

        if (! $setting['api_key']) {
            return [
                'ok' => false,
                'message' => 'No active UGSMS API key is configured.',
            ];
        }

        if ($setting['is_sandbox']) {
            return [
                'ok' => true,
                'balance' => 10000,
                'credits' => intdiv(10000, $unitPrice),
                'unit_price' => $unitPrice,
                'sandbox' => true,
            ];
        }

        $response = Http::timeout(20)
            ->acceptJson()
            ->withHeaders(['X-API-Key' => (string) $setting['api_key']])
            ->get($this->baseUrl($setting['base_url']).'/account/balance');

        $balance = (int) (
            $response->json('account_balance')
            ?? $response->json('accountBalance')
            ?? $response->json('amount')
            ?? $response->json('credits')
            ?? $response->json('balance')
            ?? $response->json('data.account_balance')
            ?? $response->json('data.accountBalance')
            ?? $response->json('data.amount')
            ?? $response->json('data.balance')
            ?? 0
        );

        return [
            'ok' => $response->successful(),
            'balance' => $balance,
            'credits' => intdiv($balance, $unitPrice),
            'unit_price' => $unitPrice,
            'payload' => $response->json(),
        ];
    }

    public function requestDeposit(int $amount, string $phone, string $callbackUrl): array
    {
        $setting = $this->setting();

        if (! $setting['api_key']) {
            return [
                'ok' => false,
                'status' => 'failed',
                'message' => 'No active UGSMS API key is configured.',
            ];
        }

        if ($setting['is_sandbox']) {
            return [
                'ok' => false,
                'status' => 'failed',
                'message' => 'UGSMS deposits require the SMS gateway integration to be live, not sandbox.',
            ];
        }

        $payload = [
            'amount' => $amount,
            'phone_number' => $phone,
            'callback_url' => $callbackUrl,
        ];

        $response = Http::timeout(20)
            ->acceptJson()
            ->withHeaders(['X-API-Key' => (string) $setting['api_key']])
            ->post($this->baseUrl($setting['base_url']).'/payments', $payload);

        return [
            'ok' => $response->successful(),
            'reference' => $response->json('reference')
                ?? $response->json('payment_reference')
                ?? $response->json('payment_id')
                ?? $response->json('id')
                ?? $response->json('data.reference')
                ?? $response->json('data.payment_reference')
                ?? $response->json('data.payment_id')
                ?? $response->json('data.id'),
            'status' => $response->successful()
                ? ($response->json('status') ?? $response->json('data.status') ?? 'pending')
                : 'failed',
            'message' => $response->json('message')
                ?? $response->json('error')
                ?? $response->json('data.message')
                ?? ($response->successful() ? null : 'UGSMS rejected the payment request.'),
            'payload' => $response->json(),
            'request' => $payload,
            'http_status' => $response->status(),
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
                'unit_price' => (int) data_get($setting->metadata, 'unit_price', config('services.ugsms.unit_price', 35)),
            ];
        }

        return [
            'base_url' => config('services.ugsms.base_url'),
            'api_key' => config('services.ugsms.api_key'),
            'is_sandbox' => (bool) config('services.ugsms.sandbox'),
            'unit_price' => (int) config('services.ugsms.unit_price', 35),
        ];
    }

    private function unitPrice(array $setting): int
    {
        return max(1, (int) ($setting['unit_price'] ?? 35));
    }

    private function baseUrl(string $baseUrl): string
    {
        $baseUrl = rtrim($baseUrl, '/');

        foreach (['/sms/send', '/account/balance', '/payments'] as $endpoint) {
            if (str_ends_with($baseUrl, $endpoint)) {
                return substr($baseUrl, 0, -strlen($endpoint));
            }
        }

        return $baseUrl;
    }
}
