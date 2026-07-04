<?php

namespace App\Livewire\App;

use App\Models\ApiKey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class Settings extends Component
{
    public string $name = '';
    public string $phone = '';
    public string $company = '';
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $api_key_name = 'Default integration';
    public string $api_key_mode = 'sandbox';

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->phone = (string) $user->phone;
        $this->company = (string) $user->company;
    }

    public function saveProfile(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:160'],
        ]);

        Auth::user()->update($data);
        session()->flash('status', 'Profile updated.');
    }

    public function changePassword(): void
    {
        $data = $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update(['password' => $data['password']]);
        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('status', 'Password changed.');
    }

    public function createApiKey(): void
    {
        $data = $this->validate([
            'api_key_name' => ['required', 'string', 'max:120'],
            'api_key_mode' => ['required', 'in:sandbox,live'],
        ]);

        $plain = 'shama_'.$data['api_key_mode'].'_'.Str::random(40);

        Auth::user()->apiKeys()->create([
            'name' => $data['api_key_name'],
            'mode' => $data['api_key_mode'],
            'key_hash' => Hash::make($plain),
            'plain_text_key' => $plain,
        ]);

        session()->flash('status', 'API key created. Copy it now; production should hide it after creation.');
    }

    public function render()
    {
        return view('livewire.app.settings', [
            'keys' => Auth::user()->apiKeys()->latest()->get(),
        ])->layout('layouts.app');
    }
}
