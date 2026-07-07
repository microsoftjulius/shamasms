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
            <thead><tr><th>Sender</th><th>Message</th><th>Recipients</th><th>Status</th><th>Created</th><th>Details</th></tr></thead>
            <tbody>
                @forelse($messages as $message)
                    <tr>
                        <td>{{ $message->sender_id ?: '—' }}</td>
                        <td class="max-w-xl truncate">{{ $message->body }}</td>
                        <td>{{ $message->recipient_count }}</td>
                        <td><span class="status-pill">{{ $message->status }}</span></td>
                        <td>{{ $message->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <button type="button" wire:click="viewMessage({{ $message->id }})" class="rounded-lg border border-sky-200 px-3 py-1.5 text-xs font-black text-sky-800 hover:bg-sky-50">
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">{{ $search ? 'No messages match your search.' : 'No messages yet.' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $messages->links() }}</div>

    @if($selectedMessage)
        @php
            $repeatCount = max(1, (int) ($selectedMessage->schedule_repeat_count ?? 1));
            $estimatedCredits = $selectedMessage->segments * $selectedMessage->recipient_count * $repeatCount;
            $sentRecipients = $selectedMessage->recipients->where('status', 'sent')->count();
            $sentCredits = $selectedMessage->segments * $sentRecipients;
        @endphp
        <section class="mt-6 rounded-lg border border-sky-100 bg-sky-50/60 p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-black text-slate-950">Message details</h2>
                    <p class="mt-1 text-sm font-semibold text-slate-600">Sent {{ $selectedMessage->created_at->format('d M Y H:i') }}</p>
                </div>
                <button type="button" wire:click="closeMessage" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-50">Close</button>
            </div>

            <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-white p-3"><dt class="font-black text-slate-500">Sender ID</dt><dd class="mt-1 font-bold text-slate-900">{{ $selectedMessage->sender_id ?: '—' }}</dd></div>
                <div class="rounded-lg bg-white p-3"><dt class="font-black text-slate-500">Group</dt><dd class="mt-1 font-bold text-slate-900">{{ $selectedMessage->contactGroup?->name ?: '—' }}</dd></div>
                <div class="rounded-lg bg-white p-3"><dt class="font-black text-slate-500">Recipients</dt><dd class="mt-1 font-bold text-slate-900">{{ number_format($selectedMessage->recipient_count) }}</dd></div>
                <div class="rounded-lg bg-white p-3"><dt class="font-black text-slate-500">Cost implication</dt><dd class="mt-1 font-bold text-slate-900">{{ number_format($estimatedCredits) }} credits estimated@if($sentRecipients > 0), {{ number_format($sentCredits) }} sent@endif</dd></div>
            </dl>

            <div class="mt-4 rounded-lg bg-white p-4">
                <h3 class="text-sm font-black text-slate-500">Message</h3>
                <p class="mt-2 whitespace-pre-wrap text-sm leading-6 text-slate-800">{{ $selectedMessage->body }}</p>
            </div>

            <div class="mt-4 overflow-x-auto rounded-lg bg-white">
                <table class="table">
                    <thead><tr><th>Contact</th><th>Status</th><th>Message received</th><th>Reference</th></tr></thead>
                    <tbody>
                        @foreach($selectedMessage->recipients as $recipient)
                            <tr>
                                <td>
                                    <div class="font-black text-slate-900">{{ $recipient->phone }}</div>
                                    @if($recipient->name)
                                        <div class="text-xs text-slate-500">{{ $recipient->name }}</div>
                                    @endif
                                </td>
                                <td><span class="status-pill">{{ $recipient->status }}</span></td>
                                <td class="max-w-xl whitespace-pre-wrap">{{ $recipient->rendered_body ?: $selectedMessage->body }}</td>
                                <td>{{ $recipient->provider_reference ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</section>
