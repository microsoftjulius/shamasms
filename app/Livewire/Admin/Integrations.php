<?php

namespace App\Livewire\Admin;

use App\Models\IntegrationSetting;
use App\Models\SmsCreditTransaction;
use App\Services\UgsmsService;
use App\Services\UgandaPhoneNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Integrations extends Component
{
    public ?int $editingId = null;
    public string $provider = 'sms_gateway';
    public string $label = '';
    public string $base_url = '';
    public string $api_key = '';
    public string $api_secret = '';
    public string $wallet_id = '';
    public string $username = '';
    public string $password = '';
    public int $ugsms_unit_price = 35;
    public int $ugsms_deposit_amount = 5000;
    public string $ugsms_deposit_phone = '';
    public bool $is_sandbox = true;
    public ?string $testMessage = null;
    public ?string $ugsmsDepositMessage = null;
    public ?int $testingId = null;

    public function save(): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $data = $this->validate([
            'provider' => ['required', 'in:sms_gateway,iotec,sendcrane'],
            'label' => ['required', 'string', 'max:120'],
            'base_url' => ['nullable', 'url', 'max:255'],
            'api_key' => ['nullable', 'string'],
            'api_secret' => ['nullable', 'string'],
            'wallet_id' => ['nullable', 'string', 'max:160'],
            'username' => ['nullable', 'string', 'max:160'],
            'password' => ['nullable', 'string'],
            'ugsms_unit_price' => ['required', 'integer', 'min:1', 'max:10000'],
            'is_sandbox' => ['boolean'],
        ]);

        if ($data['provider'] === 'iotec') {
            $data['is_sandbox'] = false;
            $data['metadata'] = [
                'wallet_id' => $data['wallet_id'] ?? null,
            ];
        } elseif (in_array($data['provider'], ['sms_gateway', 'ugsms'], true)) {
            $data['metadata'] = [
                ...(IntegrationSetting::query()->find($this->editingId)?->metadata ?? []),
                'unit_price' => $data['ugsms_unit_price'],
            ];
        } elseif ($this->editingId) {
            $data['metadata'] = IntegrationSetting::query()->find($this->editingId)?->metadata;
        }

        unset($data['wallet_id'], $data['ugsms_unit_price']);

        if ($this->editingId) {
            IntegrationSetting::query()->findOrFail($this->editingId)->update($data);
            session()->flash('status', 'Integration updated.');
        } else {
            IntegrationSetting::query()->create([...$data, 'is_active' => true]);
            session()->flash('status', 'Integration saved.');
        }

        $this->resetForm();
    }

    public function editIntegration(int $settingId): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $setting = IntegrationSetting::query()->findOrFail($settingId);

        $this->editingId = $setting->id;
        $this->provider = $setting->provider;
        $this->label = $setting->label;
        $this->base_url = (string) $setting->base_url;
        $this->api_key = (string) $setting->api_key;
        $this->api_secret = (string) $setting->api_secret;
        $this->wallet_id = (string) data_get($setting->metadata, 'wallet_id', '');
        $this->ugsms_unit_price = max(1, (int) data_get($setting->metadata, 'unit_price', 35));
        $this->username = (string) $setting->username;
        $this->password = (string) $setting->password;
        $this->is_sandbox = $setting->is_sandbox;
        $this->testMessage = "Editing {$setting->label}.";
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    public function testIntegration(int $settingId): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $setting = IntegrationSetting::query()->findOrFail($settingId);
        $this->testingId = $setting->id;

        if ($setting->provider === 'iotec' && $setting->is_sandbox) {
            $this->testMessage = "{$setting->label} is marked Sandbox, but sandbox payments are disabled. Save a live Iotec integration with Sandbox off.";
            return;
        }

        if ($setting->provider === 'iotec' && blank(data_get($setting->metadata, 'wallet_id'))) {
            $this->testMessage = "{$setting->label} is missing the Iotec wallet ID. Edit it and save it with Wallet ID, Client ID, and API Secret.";
            return;
        }

        if ($setting->is_sandbox) {
            $this->testMessage = "{$setting->label} is in sandbox mode. Configuration is readable and sandbox tests do not call the provider.";
            return;
        }

        if (! $setting->base_url) {
            $this->testMessage = "{$setting->label} could not be tested because the Base URL is missing.";
            return;
        }

        try {
            $response = $this->providerTestRequest($setting);
        } catch (\Throwable $exception) {
            $this->testMessage = "{$setting->label} test failed: {$exception->getMessage()}";
            return;
        }

        $status = $response->status();
        $this->testMessage = $response->successful()
            ? "{$setting->label} test passed. Provider responded with HTTP {$status}."
            : "{$setting->label} test failed. Provider responded with HTTP {$status}: ".str($response->body())->limit(180);
    }

    public function deleteIntegration(int $settingId): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $setting = IntegrationSetting::query()->findOrFail($settingId);
        $label = $setting->label;

        $setting->delete();

        if ($this->testingId === $settingId) {
            $this->testingId = null;
        }

        $this->testMessage = "{$label} has been deleted.";
    }

    public function depositToUgsms(UgsmsService $ugsms, UgandaPhoneNumber $phoneNumber): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $data = $this->validate([
            'ugsms_deposit_amount' => ['required', 'integer', 'min:5000'],
            'ugsms_deposit_phone' => ['required', 'string', 'max:30'],
        ]);

        $normalizedPhone = $phoneNumber->normalize($data['ugsms_deposit_phone']);

        if (! $normalizedPhone) {
            $this->addError('ugsms_deposit_phone', 'Enter a valid Ugandan mobile money number, for example 0700000000.');
            return;
        }

        $phone = '0'.substr($normalizedPhone['phone'], 3);
        $unitPrice = max(1, (int) data_get(IntegrationSetting::query()
            ->whereIn('provider', ['sms_gateway', 'ugsms'])
            ->where('is_active', true)
            ->latest()
            ->first()?->metadata, 'unit_price', 35));
        $result = $ugsms->requestDeposit(
            $data['ugsms_deposit_amount'],
            $phone,
            url('/api/ugsms/payment-callback'),
        );

        SmsCreditTransaction::query()->create([
            'user_id' => Auth::id(),
            'type' => 'ugsms_deposit',
            'amount' => $data['ugsms_deposit_amount'],
            'credits' => intdiv($data['ugsms_deposit_amount'], $unitPrice),
            'phone' => $phone,
            'provider' => 'ugsms',
            'provider_reference' => $result['reference'] ?? null,
            'status' => $result['ok'] ? ($result['status'] ?? 'pending') : 'failed',
            'metadata' => [
                ...$result,
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()?->name,
                'input_phone' => $data['ugsms_deposit_phone'],
                'callback_url' => url('/api/ugsms/payment-callback'),
            ],
        ]);

        $this->ugsmsDepositMessage = ($result['ok'] ?? false)
            ? 'UGSMS deposit request sent. Check the phone for the mobile money prompt.'
            : 'UGSMS deposit request failed: '.($result['message'] ?? 'Provider rejected the request.');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->provider = 'sms_gateway';
        $this->reset(['label', 'base_url', 'api_key', 'api_secret', 'wallet_id', 'username', 'password']);
        $this->ugsms_unit_price = 35;
        $this->is_sandbox = true;
    }

    private function providerTestRequest(IntegrationSetting $setting)
    {
        $baseUrl = rtrim((string) $setting->base_url, '/');

        return match ($setting->provider) {
            'sms_gateway', 'ugsms' => Http::timeout(15)
                ->acceptJson()
                ->withHeaders(['X-API-Key' => (string) $setting->api_key])
                ->get($this->smsGatewayBaseUrl($baseUrl).'/account/balance'),
            'iotec' => $this->iotecTestRequest($setting, $baseUrl),
            'sendcrane' => Http::timeout(15)
                ->acceptJson()
                ->withToken((string) $setting->api_key)
                ->get($baseUrl),
            default => Http::timeout(15)->acceptJson()->get($baseUrl),
        };
    }

    private function smsGatewayBaseUrl(string $baseUrl): string
    {
        $baseUrl = rtrim($baseUrl, '/');

        foreach (['/sms/send', '/account/balance'] as $endpoint) {
            if (str_ends_with($baseUrl, $endpoint)) {
                return substr($baseUrl, 0, -strlen($endpoint));
            }
        }

        return $baseUrl;
    }

    private function iotecBaseUrl(string $baseUrl): string
    {
        $baseUrl = rtrim($baseUrl, '/');

        foreach (['/api/collections/collect', '/api/collections', '/collections'] as $endpoint) {
            if (str_ends_with($baseUrl, $endpoint)) {
                return substr($baseUrl, 0, -strlen($endpoint));
            }
        }

        return $baseUrl;
    }

    private function iotecTestRequest(IntegrationSetting $setting, string $baseUrl)
    {
        $tokenResponse = Http::timeout(15)
            ->asForm()
            ->post('https://id.iotec.io/connect/token', [
                'client_id' => $setting->api_key,
                'client_secret' => $setting->api_secret,
                'grant_type' => 'client_credentials',
            ]);

        if (! $tokenResponse->successful()) {
            return $tokenResponse;
        }

        return Http::timeout(15)
            ->acceptJson()
            ->withToken((string) $tokenResponse->json('access_token'))
            ->get($this->iotecBaseUrl($baseUrl).'/api/wallet-balance/'.data_get($setting->metadata, 'wallet_id'));
    }

    public function render()
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $ugsmsBalance = null;
        try {
            $ugsmsBalance = app(UgsmsService::class)->balance();
        } catch (\Throwable $exception) {
            $ugsmsBalance = [
                'ok' => false,
                'message' => $exception->getMessage(),
            ];
        }

        return view('livewire.admin.integrations', [
            'settings' => IntegrationSetting::query()->latest()->get(),
            'ugsmsBalance' => $ugsmsBalance,
            'activeUgsmsSetting' => IntegrationSetting::query()
                ->whereIn('provider', ['sms_gateway', 'ugsms'])
                ->where('is_active', true)
                ->latest()
                ->first(),
            'ugsmsDeposits' => SmsCreditTransaction::query()
                ->where('type', 'ugsms_deposit')
                ->where('provider', 'ugsms')
                ->latest()
                ->limit(10)
                ->get(),
        ])->layout('layouts.app');
    }
}
