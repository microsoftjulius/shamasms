<section class="panel" data-tour="sent-panel">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="page-title">Sent</h1>
            <p class="page-subtitle">Recent standard, personalized, sandbox, and scheduled SMS batches.</p>
        </div>
        <label class="label w-full sm:max-w-sm" data-tour="sent-search">Search SMS
            <input wire:model.live.debounce.300ms="search" class="field" placeholder="Message, sender, phone, status">
        </label>
    </div>
    <div class="mt-6 overflow-x-auto" data-tour="sent-table">
        <table class="table">
            <thead><tr><th>Sender</th><th>Message</th><th>Recipients</th><th>Status</th><th>Created</th></tr></thead>
            <tbody>
                @forelse($messages as $message)
                    <tr>
                        <td>{{ $message->sender_id ?: '—' }}</td>
                        <td class="max-w-xl truncate">{{ $message->body }}</td>
                        <td>{{ $message->recipient_count }}</td>
                        <td><span class="status-pill">{{ $message->status }}</span></td>
                        <td>{{ $message->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">{{ $search ? 'No messages match your search.' : 'No messages yet.' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $messages->links() }}</div>
</section>
