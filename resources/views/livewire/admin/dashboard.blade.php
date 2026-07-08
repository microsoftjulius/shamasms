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
            <p class="text-xs font-black uppercase tracking-widest text-slate-500">Admins</p>
            <p class="mt-2 text-3xl font-black text-slate-950">{{ number_format($adminCount) }}</p>
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

    <div class="grid gap-6 xl:grid-cols-3">
        <form wire:submit="promoteAdmin" class="panel min-w-0">
            <h2 class="text-xl font-black">Create admin</h2>
            <p class="page-subtitle">Enter an existing username to give that user admin access.</p>
            <label class="label mt-5 block">Username <span class="req">*</span>
                <input wire:model="adminUsername" class="field" placeholder="e.g. martin">
            </label>
            @error('adminUsername')
                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
            @enderror
            @if($adminMessage)
                <p class="mt-2 text-sm font-black text-emerald-700">{{ $adminMessage }}</p>
            @endif
            <button class="mt-5 rounded-lg bg-sky-500 px-5 py-3 font-black text-white hover:bg-sky-600">Make admin</button>
        </form>

        <form wire:submit="createTier" class="panel min-w-0">
            <h2 class="text-xl font-black">Add price tier</h2>
            <p class="page-subtitle">Set the SMS price for a message quantity range, for example 1 to 1000 or 1001 to 2000.</p>
            <div class="mt-5 grid gap-3">
                <label class="label">Tier name <span class="req">*</span><input wire:model="tierName" class="field" placeholder="e.g. Starter"></label>
                <label class="label">From messages <span class="req">*</span><input wire:model="tierMinMessages" type="number" min="1" class="field" placeholder="1"></label>
                <label class="label">To messages <input wire:model="tierMaxMessages" type="number" min="1" class="field" placeholder="1000"></label>
                <label class="label">Price per SMS <span class="req">*</span><input wire:model="tierUnitPrice" type="number" min="1" class="field"></label>
            </div>
            @error('tierName')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            @error('tierMinMessages')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            @error('tierMaxMessages')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            @error('tierUnitPrice')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            @if($tierMessage)
                <p class="mt-2 text-sm font-black text-emerald-700">{{ $tierMessage }}</p>
            @endif
            <button class="mt-5 rounded-lg bg-sky-500 px-5 py-3 font-black text-white hover:bg-sky-600">Add tier</button>
        </form>

        <form wire:submit="saveAdvert" class="panel min-w-0">
            <h2 class="text-xl font-black">Advert</h2>
            <p class="page-subtitle">Show a non-blocking notice to logged-in users until you stop it.</p>
            <div class="mt-5 grid gap-3">
                <label class="label">Title <span class="req">*</span><input wire:model="advertTitle" class="field" placeholder="e.g. Weekend offer"></label>
                <label class="label">Message <span class="req">*</span><textarea wire:model="advertBody" class="field min-h-24" placeholder="Advert details"></textarea></label>
                <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700"><input wire:model="advertActive" type="checkbox" class="rounded border-slate-300"> Active</label>
            </div>
            @error('advertTitle')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            @error('advertBody')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            @if($advertMessage)
                <p class="mt-2 text-sm font-black text-emerald-700">{{ $advertMessage }}</p>
            @endif
            <button class="mt-5 rounded-lg bg-sky-500 px-5 py-3 font-black text-white hover:bg-sky-600">Save advert</button>
        </form>
    </div>

    <div class="panel min-w-0">
        <h2 class="text-xl font-black">Saved adverts</h2>
        <p class="page-subtitle">Review saved adverts and choose which one should be shown to logged-in users.</p>
        <div class="mt-5 overflow-x-auto">
            <table class="table">
                <thead><tr><th>Advert</th><th>Status</th><th>Created</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($adverts as $advert)
                        <tr>
                            <td>
                                <div class="font-black text-slate-900">{{ $advert->title }}</div>
                                <div class="mt-1 max-w-2xl whitespace-pre-wrap text-xs leading-5 text-slate-500">{{ $advert->body }}</div>
                            </td>
                            <td><span class="status-pill">{{ $advert->is_active ? 'active' : 'inactive' }}</span></td>
                            <td>{{ $advert->created_at->format('d M Y H:i') }}</td>
                            <td class="space-x-2 whitespace-nowrap">
                                @if($advert->is_active)
                                    <button wire:click="stopAdvert({{ $advert->id }})" type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 hover:bg-slate-50">Stop</button>
                                @else
                                    <button wire:click="activateAdvert({{ $advert->id }})" type="button" class="rounded-lg bg-sky-500 px-3 py-2 text-xs font-black text-white hover:bg-sky-600">Activate</button>
                                @endif
                                <button wire:click="deleteAdvert({{ $advert->id }})" wire:confirm="Delete this advert?" type="button" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-black text-red-700 hover:bg-red-50">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No adverts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel min-w-0">
        <h2 class="text-xl font-black">Price tiers</h2>
        <p class="page-subtitle">The tier whose message range matches the purchased credits will set the user's SMS price.</p>
        <div class="mt-5 overflow-x-auto">
            <table class="table">
                <thead><tr><th>Name</th><th>From messages</th><th>To messages</th><th>Price per SMS</th><th>Active</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($tiers as $tier)
                        <tr>
                            <td><input wire:model="tierInputs.{{ $tier->id }}.name" class="field mt-0 min-w-40"></td>
                            <td><input wire:model="tierInputs.{{ $tier->id }}.min_messages" type="number" min="1" class="field mt-0 w-32"></td>
                            <td><input wire:model="tierInputs.{{ $tier->id }}.max_messages" type="number" min="1" class="field mt-0 w-32" placeholder="No limit"></td>
                            <td><input wire:model="tierInputs.{{ $tier->id }}.sms_unit_price" type="number" min="1" class="field mt-0 w-32"></td>
                            <td><input wire:model="tierInputs.{{ $tier->id }}.is_active" type="checkbox" class="rounded border-slate-300"></td>
                            <td><button wire:click="updateTier({{ $tier->id }})" type="button" class="rounded-lg bg-sky-500 px-3 py-2 text-xs font-black text-white hover:bg-sky-600">Save</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No price tiers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

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
