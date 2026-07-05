<?php
namespace App\Livewire\Auth;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
 public string $email = '';
 public bool $sent = false;

 public function send(): void
 {
 $this->validate(['email' => ['required', 'email']]);
 Password::sendResetLink(['email' => $this->email]);
 $this->sent = true;
 }

 public function render()
 {
 return view('livewire.auth.forgot-password')->layout('layouts.guest');
 }
}
