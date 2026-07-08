<?php

namespace App\Livewire\Admin;

use App\Models\IntegrationSetting;
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
    public bool $is_sandbox = true;
    public ?string $testMessage = null;
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
            'is_sandbox' => ['boolean'],
        ]);

        if ($data['provider'] === 'iotec') {
            $data['is_sandbox'] = false;
            $data['metadata'] = [
                'wallet_id' => $data['wallet_id'] ?? null,
            ];
        } elseif ($this->editingId) {
            $data['metadata'] = IntegrationSetting::query()->find($this->editingId)?->metadata;
        }

        unset($data['wallet_id']);

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
            $this->testMessage = "{$setting->label} is missing the Iotec wallet ID. Delete it and save it again with Wallet ID, Client ID, and API Secret.";
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

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->provider = 'sms_gateway';
        $this->reset(['label', 'base_url', 'api_key', 'api_secret', 'wallet_id', 'username', 'password']);
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

        return view('livewire.admin.integrations', [
            'settings' => IntegrationSetting::query()->latest()->get(),
        ])->layout('layouts.app');
    }
}
