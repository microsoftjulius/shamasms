<?php

namespace App\Livewire\App;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SentMessages extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $search = trim($this->search);

        return view('livewire.app.sent-messages', [
            'messages' => Auth::user()
                ->smsMessages()
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('sender_id', 'like', "%{$search}%")
                            ->orWhere('body', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhere('external_reference', 'like', "%{$search}%")
                            ->orWhereHas('recipients', function ($query) use ($search) {
                                $query->where('phone', 'like', "%{$search}%")
                                    ->orWhere('name', 'like', "%{$search}%")
                                    ->orWhere('rendered_body', 'like', "%{$search}%")
                                    ->orWhere('provider_reference', 'like', "%{$search}%");
                            });
                    });
                })
                ->latest()
                ->paginate(12),
        ])->layout('layouts.app');
    }
}
