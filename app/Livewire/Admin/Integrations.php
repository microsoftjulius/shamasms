<?php

namespace App\Livewire\Admin;

use App\Models\IntegrationSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Integrations extends Component
{
    public string $provider = 'sms_gateway';
    public string $label = '';
    public string $base_url = '';
    public string $api_key = '';
    public string $api_secret = '';
    public string $username = '';
    public string $password = '';
    public bool $is_sandbox = true;

    public function save(): void
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $data = $this->validate([
            'provider' => ['required', 'in:sms_gateway,iotec,sendcrane'],
            'label' => ['required', 'string', 'max:120'],
            'base_url' => ['nullable', 'url', 'max:255'],
            'api_key' => ['nullable', 'string'],
            'api_secret' => ['nullable', 'string'],
            'username' => ['nullable', 'string', 'max:160'],
            'password' => ['nullable', 'string'],
            'is_sandbox' => ['boolean'],
        ]);

        IntegrationSetting::query()->create([...$data, 'is_active' => true]);

        $this->provider = 'sms_gateway';
        $this->reset(['label', 'base_url', 'api_key', 'api_secret', 'username', 'password']);
        $this->is_sandbox = true;
        session()->flash('status', 'Integration saved.');
    }

    public function render()
    {
        abort_unless(Auth::user()?->is_admin, 403);

        return view('livewire.admin.integrations', [
            'settings' => IntegrationSetting::query()->latest()->get(),
        ])->layout('layouts.app');
    }
}
