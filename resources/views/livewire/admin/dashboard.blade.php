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
                                <button wire:click="deleteAdvert({{ $advert->id }})" data-swal-confirm="Delete this advert?" data-swal-title="Delete advert?" data-swal-icon="warning" data-swal-confirm-text="Delete" data-swal-confirm-color="#dc2626" type="button" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-black text-red-700 hover:bg-red-50">Delete</button>
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

    <div class="panel min-w-0">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-black">Payments</h2>
                <p class="page-subtitle">Review credit purchases and manually resolve payments that did not callback.</p>
            </div>
            @if($paymentMessage)
                <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-black text-emerald-800">{{ $paymentMessage }}</p>
            @endif
        </div>
        <div class="mt-5 overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Credits</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Reference</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td class="whitespace-nowrap">{{ $payment->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <div class="font-black text-slate-900">{{ $payment->user?->name ?? 'Unknown user' }}</div>
                                <div class="text-xs text-slate-500">{{ $payment->user?->username ?? '—' }}</div>
                            </td>
                            <td>{{ number_format($payment->amount) }}</td>
                            <td>{{ number_format($payment->credits) }}</td>
                            <td class="whitespace-nowrap">{{ $payment->phone ?: '—' }}</td>
                            <td><span class="status-pill">{{ $payment->status }}</span></td>
                            <td class="max-w-52 break-words text-xs">{{ $payment->provider_reference ?: data_get($payment->metadata, 'external_id', '—') }}</td>
                            <td class="max-w-xs text-xs leading-5">{{ data_get($payment->metadata, 'message') ?: data_get($payment->metadata, 'callback.statusMessage') ?: data_get($payment->metadata, 'payload.message') ?: data_get($payment->metadata, 'payload.error') ?: '—' }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="markPaymentSuccessful({{ $payment->id }})" data-swal-confirm="Mark this payment successful and add credits if needed?" data-swal-title="Approve payment?" data-swal-icon="success" data-swal-confirm-text="Mark successful" data-swal-confirm-color="#10b981" type="button" class="rounded-lg bg-emerald-500 px-3 py-2 text-xs font-black text-white hover:bg-emerald-600">Success</button>
                                    <button wire:click="markPaymentFailed({{ $payment->id }})" data-swal-confirm="Mark this payment failed and reverse credits if it was already credited?" data-swal-title="Fail payment?" data-swal-icon="warning" data-swal-confirm-text="Mark failed" data-swal-confirm-color="#dc2626" type="button" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-black text-red-700 hover:bg-red-50">Failed</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9">No payments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">
            {{ $payments->links() }}
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_420px]">
        <div class="panel min-w-0">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-xl font-black">All users</h2>
                    <p class="page-subtitle">Search by name, email, or username, then verify accounts, credit balances, or change user SMS pricing.</p>
                </div>
                <label class="label w-full sm:max-w-xs">Search user
                    <input wire:model.live.debounce.300ms="search" class="field" placeholder="Name, email, username, phone">
                </label>
            </div>
            @if($userActionMessage)
                <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-black text-emerald-900">
                    {{ $userActionMessage }}
                </div>
            @endif

            <div class="mt-5 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Credits</th>
                            <th>Sent</th>
                            <th>Price per SMS</th>
                            <th>Credit amount</th>
                            <th>Password</th>
                            <th>Verification</th>
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
                                <td>
                                    <form wire:submit="creditUser({{ $user->id }})" class="flex min-w-44 items-center gap-2">
                                        <input wire:model="creditInputs.{{ $user->id }}" type="number" min="1" class="field mt-0 w-28" placeholder="UGX">
                                        <button class="rounded-lg bg-emerald-500 px-3 py-2 text-xs font-black text-white hover:bg-emerald-600">Credit</button>
                                    </form>
                                    @error('creditInputs.'.$user->id)
                                        <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td>
                                    <button wire:click="openPasswordModal({{ $user->id }})" type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 hover:bg-slate-50">Password</button>
                                </td>
                                <td>
                                    @if($user->hasVerifiedEmail())
                                        <span class="status-pill">verified</span>
                                    @else
                                        <button wire:click="verifyUser({{ $user->id }})" data-swal-confirm="Verify this user account now?" data-swal-title="Verify user?" data-swal-icon="question" data-swal-confirm-text="Verify" type="button" class="rounded-lg bg-sky-500 px-3 py-2 text-xs font-black text-white hover:bg-sky-600">Verify</button>
                                    @endif
                                </td>
                                <td><span class="status-pill">{{ $user->is_admin ? 'admin' : 'user' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="9">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $users->links() }}
            </div>
        </div>

        @if($passwordModalUserId)
            @php($passwordUser = $users->firstWhere('id', $passwordModalUserId))
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4 py-6">
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-2xl shadow-slate-950/30" style="width:min(420px, calc(100vw - 32px));">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Change password</h2>
                            <p class="mt-1 text-sm text-slate-600">{{ $passwordUser?->name ?? 'Selected user' }} · {{ $passwordUser?->username ?? '' }}</p>
                        </div>
                        <button wire:click="closePasswordModal" type="button" class="rounded-lg px-3 py-2 text-sm font-black text-slate-500 hover:bg-slate-100">Close</button>
                    </div>

                    <form wire:submit="changeUserPassword" class="mt-4">
                        <label class="label">New password <span class="req">*</span>
                            <span class="password-wrap mt-1">
                                <input wire:model="passwordModalValue" type="{{ $showPasswordModalValue ? 'text' : 'password' }}" class="field !mt-0 pr-12" placeholder="At least 8 characters" autocomplete="new-password" autofocus>
                                <button wire:click="togglePasswordModalVisibility" type="button" class="password-eye" aria-label="{{ $showPasswordModalValue ? 'Hide password' : 'Show password' }}" title="{{ $showPasswordModalValue ? 'Hide password' : 'Show password' }}">
                                    @if($showPasswordModalValue)
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3l18 18"/><path d="M10.6 10.6A2 2 0 0 0 13.4 13.4"/><path d="M9.9 5.1A9.8 9.8 0 0 1 12 5c5 0 9 4.5 10 7-0.5 1.2-1.6 2.8-3.1 4.1"/><path d="M6.6 6.6C4.3 8 2.8 10.4 2 12c1 2.5 5 7 10 7 1.3 0 2.5-.3 3.6-.8"/></svg>
                                    @else
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    @endif
                                </button>
                            </span>
                        </label>
                        @error('passwordModalValue')
                            <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="mt-4 flex flex-wrap justify-end gap-2">
                            <button wire:click="closePasswordModal" type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-black text-slate-700 hover:bg-slate-50">Cancel</button>
                            <button class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-black text-white hover:bg-sky-600">Save password</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

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
