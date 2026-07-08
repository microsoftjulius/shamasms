<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
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

        if (! Auth::attempt([
            'username' => $data['username'],
            'password' => $data['password'],
        ], $this->remember)) {
            $this->addError('username', 'These details do not match a ShamaSMS account.');
            return;
        }

        request()->session()->regenerate();
        $this->redirectRoute('compose', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}
