<section class="grid gap-6 lg:grid-cols-[360px_1fr]">
    <div class="panel" data-tour="phonebook-groups">
        <h1 class="page-title">Phonebook</h1>
        <p class="page-subtitle">Create recipient groups, import numbers, then select the group in Compose SMS.</p>
        <form wire:submit="createGroup" class="mt-5 grid gap-4" data-tour="phonebook-create">
            <label class="label">Group name <span class="req">*</span><input wire:model="group_name" class="field"></label>
            <label class="label">Sender ID<input wire:model="sender_id" class="field"></label>
            <button class="rounded-lg bg-sky-500 px-5 py-3 font-black text-white hover:bg-sky-600">Create group</button>
        </form>
        <div class="mt-6 space-y-2" data-tour="phonebook-list">
            @foreach($groups as $group)
                <button wire:click="$set('active_group_id', {{ $group->id }})" @class(['group-button', 'active' => $active_group_id === $group->id])>{{ $group->name }} <span>{{ $group->contacts_count }}</span></button>
            @endforeach
        </div>
    </div>
    <div class="panel" data-tour="phonebook-import">
        <h2 class="text-xl font-black">{{ $activeGroup?->name ?? 'No group selected' }}</h2>
        @if($activeGroup)
            <form wire:submit="importContacts" class="mt-5 grid gap-4">
                <div class="rounded-lg border border-sky-100 bg-sky-50 p-4" data-tour="phonebook-template">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-black text-sky-950">Import template</h3>
                            <p class="mt-1 text-sm leading-6 text-slate-700">Use one phone number per row. You may include a <span class="font-mono text-xs font-bold">phone</span> header.</p>
                        </div>
                        <a
                            class="rounded-lg border border-sky-200 bg-white px-3 py-2 text-sm font-black text-sky-800 hover:bg-sky-100"
                            download="shamasms-phonebook-template.csv"
                            href="data:text/csv;charset=utf-8,phone%0A0700000000%0A256750000000%0A%2B256770000000"
                        >Download CSV</a>
                    </div>
                    <pre class="mt-3 overflow-x-auto rounded-lg bg-white p-3 font-mono text-xs leading-6 text-slate-800">phone
0700000000
256750000000
+256770000000</pre>
                </div>
                <label class="label">Paste numbers or upload file <span class="req">*</span>
                    <textarea wire:model="numbers" class="field min-h-36" placeholder="0700000000&#10;256700000000&#10;+256750000000&#10;0770000000"></textarea>
                </label>
                <label class="label">Import file
                    <input wire:model="numbers_file" type="file" class="field bg-white">
                </label>
                <p class="text-sm leading-6 text-slate-600">Use Ugandan mobile numbers only. Accepted formats include 070..., 25670..., and +25670.... Names and personalization variables are entered during Compose SMS, not saved in phonebook groups.</p>
                @if($invalid_numbers)
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                        Invalid numbers ignored: {{ implode(', ', array_slice($invalid_numbers, 0, 8)) }}
                        @if(count($invalid_numbers) > 8)
                            , and {{ count($invalid_numbers) - 8 }} more
                        @endif
                    </div>
                @endif
                @if($providerCounts->isNotEmpty())
                    <div class="grid gap-2 sm:grid-cols-3">
                        @foreach($providerCounts as $provider => $count)
                            <div class="rounded-lg border border-sky-100 bg-sky-50 px-4 py-3">
                                <p class="text-xs font-black uppercase text-sky-700">{{ $provider }}</p>
                                <p class="mt-1 text-2xl font-black text-sky-950">{{ number_format($count) }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
                <button class="w-full rounded-lg bg-sky-500 px-5 py-3 font-black text-white hover:bg-sky-600 sm:w-auto">Import numbers</button>
            </form>
            <div class="mt-6 overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Phone</th><th>Provider</th><th>Added</th></tr></thead>
                    <tbody>
                        @forelse($activeGroup->contacts as $contact)
                            <tr><td>+{{ $contact->phone }}</td><td>{{ app(\App\Services\UgandaPhoneNumber::class)->providerFor($contact->phone) }}</td><td>{{ $contact->created_at->format('d M Y') }}</td></tr>
                        @empty
                            <tr><td colspan="3">No numbers imported yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
