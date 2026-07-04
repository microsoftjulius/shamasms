<?php

namespace App\Livewire\App;

use App\Models\Me2UTransfer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Me2U extends Component
{
    public string $username = '';
    public int $credits = 1;
    public string $note = '';

    public function transfer(): void
    {
        $data = $this->validate([
            'username' => ['required', 'exists:users,username'],
            'credits' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:160'],
        ]);

        $sender = Auth::user();
        if ($sender->sms_balance < $data['credits']) {
            $this->addError('credits', 'Your SMS balance is too low.');
            return;
        }

        $recipient = User::query()->where('username', $data['username'])->firstOrFail();

        DB::transaction(function () use ($sender, $recipient, $data): void {
            $sender->decrement('sms_balance', $data['credits']);
            $recipient->increment('sms_balance', $data['credits']);
            Me2UTransfer::query()->create([
                'from_user_id' => $sender->id,
                'to_user_id' => $recipient->id,
                'credits' => $data['credits'],
                'note' => $data['note'],
            ]);
        });

        $this->reset(['username', 'credits', 'note']);
        $this->credits = 1;
        session()->flash('status', 'SMS credits shared successfully.');
    }

    public function render()
    {
        return view('livewire.app.me2u', [
            'transfers' => Me2UTransfer::query()
                ->where('from_user_id', Auth::id())
                ->orWhere('to_user_id', Auth::id())
                ->latest()
                ->limit(10)
                ->get(),
        ])->layout('layouts.app');
    }
}
