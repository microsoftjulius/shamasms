<?php

namespace App\Livewire\App;

use App\Models\SmsMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SentMessages extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $selectedMessageId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function viewMessage(int $messageId): void
    {
        $this->selectedMessageId = Auth::user()
            ->smsMessages()
            ->whereKey($messageId)
            ->value('id');
    }

    public function closeMessage(): void
    {
        $this->selectedMessageId = null;
    }

    public function render()
    {
        $search = trim($this->search);
        $selectedMessage = $this->selectedMessageId
            ? SmsMessage::query()
                ->whereBelongsTo(Auth::user())
                ->with(['contactGroup', 'recipients' => fn ($query) => $query->orderBy('id')])
                ->find($this->selectedMessageId)
            : null;

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
            'selectedMessage' => $selectedMessage,
        ])->layout('layouts.app');
    }
}
