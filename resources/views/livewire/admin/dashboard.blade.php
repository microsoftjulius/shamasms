<section class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="page-title">Admin dashboard</h1>
            <p class="page-subtitle">View all users, set custom SMS pricing per account, and monitor the strongest senders.</p>
        </div>
        <a class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-700 hover:bg-sky-50 hover:text-sky-800" href="{{ route('admin.integrations') }}">
            Integrations
        </a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="panel min-w-0">
            <p class="text-xs font-black uppercase tracking-widest text-slate-500">Users</p>
            <p class="mt-2 text-3xl font-black text-slate-950">{{ number_format($totalUsers) }}</p>
        </div>
        <div class="panel min-w-0">
            <p class="text-xs font-black uppercase tracking-widest text-slate-500">Total user credits</p>
            <p class="mt-2 text-3xl font-black text-sky-700">{{ number_format($totalBalance) }}</p>
        </div>
        <div class="panel min-w-0">
            <p class="text-xs font-black uppercase tracking-widest text-slate-500">Default public rate</p>
            <p class="mt-2 text-3xl font-black text-slate-950">UGX 35</p>
        </div>
        <div class="panel min-w-0">
            <p class="text-xs font-black uppercase tracking-widest text-slate-500">Selected period</p>
            <p class="mt-2 break-words text-lg font-black text-slate-950">{{ $startsAt->format('d M Y') }} - {{ $endsAt->format('d M Y') }}</p>
        </div>
    </div>

    @if($priceUpdateMessage)
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-black text-emerald-900">
            {{ $priceUpdateMessage }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1fr_420px]">
        <div class="panel min-w-0">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-xl font-black">All users</h2>
                    <p class="page-subtitle">Change a user rate to control how many SMS credits they receive when buying.</p>
                </div>
                <label class="label w-full sm:max-w-xs">Search
                    <input wire:model.live.debounce.300ms="search" class="field" placeholder="Name, email, username, phone">
                </label>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Credits</th>
                            <th>Sent</th>
                            <th>Price per SMS</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="font-black text-slate-900">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $user->email }} · {{ $user->username }}</div>
                                </td>
                                <td>{{ $user->phone ?: '—' }}</td>
                                <td>{{ number_format($user->sms_balance) }}</td>
                                <td>
                                    <div class="font-black text-slate-900">{{ number_format($user->sent_recipients_total ?? 0) }}</div>
                                    <div class="text-xs text-slate-500">{{ number_format($user->sent_batches_count) }} batches</div>
                                </td>
                                <td>
                                    <form wire:submit="updatePrice({{ $user->id }})" class="flex min-w-44 items-center gap-2">
                                        <input wire:model="prices.{{ $user->id }}" type="number" min="1" class="field mt-0 w-28">
                                        <button class="rounded-lg bg-sky-500 px-3 py-2 text-xs font-black text-white hover:bg-sky-600">Save</button>
                                    </form>
                                    @error('prices.'.$user->id)
                                        <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td><span class="status-pill">{{ $user->is_admin ? 'admin' : 'user' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $users->links() }}
            </div>
        </div>

        <aside class="panel h-fit min-w-0">
            <div class="flex flex-col gap-3">
                <div>
                    <h2 class="text-xl font-black">Top senders</h2>
                    <p class="page-subtitle">Ranked by delivered recipient count.</p>
                </div>
                <div class="segmented grid-cols-2 sm:grid-cols-none">
                    <label><input type="radio" wire:model.live="period" value="day"><span>Day</span></label>
                    <label><input type="radio" wire:model.live="period" value="week"><span>Week</span></label>
                    <label><input type="radio" wire:model.live="period" value="month"><span>Month</span></label>
                    <label><input type="radio" wire:model.live="period" value="year"><span>Year</span></label>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="table">
                    <thead><tr><th>User</th><th>Recipients</th><th>Credits</th></tr></thead>
                    <tbody>
                        @forelse($topSenders as $sender)
                            <tr>
                                <td>
                                    <div class="font-black text-slate-900">{{ $sender->user?->name ?? 'Unknown user' }}</div>
                                    <div class="text-xs text-slate-500">{{ $sender->batches_count }} batches</div>
                                </td>
                                <td>{{ number_format($sender->recipients_count) }}</td>
                                <td>{{ number_format($sender->credits_count) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No sent SMS in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $topSenders->links() }}
            </div>
        </aside>
    </div>
</section>
