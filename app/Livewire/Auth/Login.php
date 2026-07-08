<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Login extends Component
{
    public string $username = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): void
    {
        $data = $this->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = trim($data['username']);
        $user = User::query()
            ->whereRaw('LOWER(username) = ?', [mb_strtolower($username)])
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            $this->addError('username', 'These details do not match a ShamaSMS account.');
            return;
        }

        Auth::login($user, $this->remember);
        request()->session()->regenerate();
        $this->redirectRoute('compose', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}
