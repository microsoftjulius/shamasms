<section class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="page-title">Admin integrations</h1>
            <p class="page-subtitle">Store SMS gateway, payment, and email provider API credentials.</p>
        </div>
        <a class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-700 hover:bg-sky-50 hover:text-sky-800" href="{{ route('admin.dashboard') }}">
            Users & analytics
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="panel min-w-0">
            <p class="text-xs font-black uppercase tracking-widest text-slate-500">UGSMS gateway</p>
            <p class="mt-2 break-words text-lg font-black text-slate-950">{{ $activeUgsmsSetting?->label ?? 'Not configured' }}</p>
            <p class="mt-1 text-xs font-bold text-slate-500">{{ $activeUgsmsSetting?->is_sandbox ? 'Sandbox' : 'Live' }}</p>
        </div>
        <div class="panel min-w-0">
            <p class="text-xs font-black uppercase tracking-widest text-slate-500">Account balance</p>
            <p class="mt-2 text-3xl font-black text-slate-950">
                @if($ugsmsBalance && ($ugsmsBalance['ok'] ?? false))
                    {{ number_format($ugsmsBalance['balance'] ?? 0) }}
                @else
                    —
                @endif
            </p>
        </div>
        <div class="panel min-w-0">
            <p class="text-xs font-black uppercase tracking-widest text-slate-500">UGSMS price</p>
            <p class="mt-2 text-3xl font-black text-sky-700">
                @if($ugsmsBalance && ($ugsmsBalance['ok'] ?? false))
                    {{ number_format($ugsmsBalance['unit_price'] ?? 35) }}
                @else
                    —
                @endif
            </p>
        </div>
        <div class="panel min-w-0">
            <p class="text-xs font-black uppercase tracking-widest text-slate-500">Credit balance</p>
            <p class="mt-2 text-3xl font-black text-emerald-700">
                @if($ugsmsBalance && ($ugsmsBalance['ok'] ?? false))
                    {{ number_format($ugsmsBalance['credits'] ?? 0) }}
                @else
                    —
                @endif
            </p>
        </div>
    </div>

    @if($ugsmsBalance && !($ugsmsBalance['ok'] ?? false))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-black text-red-800">
            Could not load UGSMS account balance{{ isset($ugsmsBalance['message']) ? ': '.$ugsmsBalance['message'] : '.' }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[420px_1fr]">
    <form wire:submit="depositToUgsms" class="panel">
        <h2 class="text-xl font-black">Deposit to UGSMS</h2>
        <p class="page-subtitle">Initiate a mobile money payment that tops up the UGSMS account through their payments API.</p>
        <div class="mt-5 grid gap-4">
            <label class="label">Amount UGX <span class="req">*</span><input wire:model="ugsms_deposit_amount" type="number" min="5000" class="field"></label>
            <label class="label">Mobile money number <span class="req">*</span><input wire:model="ugsms_deposit_phone" class="field" placeholder="0702913454"></label>
            <div class="rounded-lg border border-sky-100 bg-sky-50 px-4 py-3 text-sm font-bold text-sky-900">
                Callback URL: <code class="break-all">{{ url('/api/ugsms/payment-callback') }}</code>
            </div>
        </div>
        @error('ugsms_deposit_amount')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
        @error('ugsms_deposit_phone')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
        @if($ugsmsDepositMessage)
            <div class="mt-4 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-black text-sky-900">{{ $ugsmsDepositMessage }}</div>
        @endif
        <button class="mt-5 rounded-lg bg-sky-500 px-6 py-3 font-black text-white hover:bg-sky-600">Deposit to UGSMS</button>
    </form>

    <div class="panel">
        <h2 class="text-xl font-black">UGSMS deposits</h2>
        <div class="mt-5 overflow-x-auto">
            <table class="table">
                <thead><tr><th>Amount</th><th>Phone</th><th>Status</th><th>Reference</th><th>Message</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse($ugsmsDeposits as $deposit)
                        <tr>
                            <td>{{ number_format($deposit->amount) }}</td>
                            <td>{{ $deposit->phone ?: '—' }}</td>
                            <td><span class="status-pill">{{ $deposit->status }}</span></td>
                            <td class="max-w-52 break-words text-xs">{{ $deposit->provider_reference ?: '—' }}</td>
                            <td class="max-w-xs text-xs leading-5">{{ data_get($deposit->metadata, 'message') ?: data_get($deposit->metadata, 'callback.message') ?: data_get($deposit->metadata, 'payload.message') ?: data_get($deposit->metadata, 'payload.error') ?: '—' }}</td>
                            <td class="whitespace-nowrap">{{ $deposit->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No UGSMS deposits yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[420px_1fr]">
    <form wire:submit="save" class="panel">
        <h2 class="text-xl font-black">{{ $editingId ? 'Edit integration' : 'Add integration' }}</h2>
        <div class="mt-6 grid gap-4">
            <label class="label">Provider <span class="req">*</span><select wire:model="provider" class="field"><option value="sms_gateway">SMS Gateway</option><option value="iotec">Payment Provider</option><option value="sendcrane">Email Provider</option></select></label>
            <label class="label">Label <span class="req">*</span><input wire:model="label" class="field"></label>
            <label class="label">Base URL<input wire:model="base_url" class="field" placeholder="{{ $provider === 'iotec' ? 'https://pay.iotec.io' : 'https://api.example.com' }}"></label>
            @if($provider === 'iotec')
                <label class="label">Iotec wallet ID <span class="req">*</span><input wire:model="wallet_id" class="field font-mono" placeholder="Wallet ID from Iotec"></label>
                <label class="label">Iotec client ID <span class="req">*</span><textarea wire:model="api_key" class="field min-h-20 font-mono text-sm"></textarea></label>
                <label class="label">Iotec API secret <span class="req">*</span><textarea wire:model="api_secret" class="field min-h-20 font-mono text-sm"></textarea></label>
            @else
                <label class="label">API key<textarea wire:model="api_key" class="field min-h-24 font-mono text-sm"></textarea></label>
                <label class="label">API secret<textarea wire:model="api_secret" class="field min-h-24"></textarea></label>
                @if($provider === 'sms_gateway')
                    <label class="label">UGSMS price per SMS <span class="req">*</span><input wire:model="ugsms_unit_price" type="number" min="1" class="field" placeholder="35"></label>
                @endif
                <label class="label">Username<input wire:model="username" class="field"></label>
                <label class="label">Password<input wire:model="password" type="password" class="field"></label>
            @endif
            @if($provider !== 'iotec')
                <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700"><input wire:model="is_sandbox" type="checkbox" class="rounded border-slate-300"> Sandbox</label>
            @else
                <div class="rounded-lg border border-sky-100 bg-sky-50 px-4 py-3 text-sm font-bold text-sky-900">
                    Payment Provider requests are live only. Sandbox payments are disabled.
                </div>
            @endif
        </div>
        <div class="mt-6 flex flex-wrap gap-2">
            <button class="rounded-lg bg-sky-500 px-6 py-3 font-black text-white hover:bg-sky-600">{{ $editingId ? 'Update integration' : 'Save integration' }}</button>
            @if($editingId)
                <button wire:click="cancelEdit" type="button" class="rounded-lg border border-slate-200 px-6 py-3 font-black text-slate-700 hover:bg-slate-50">Cancel</button>
            @endif
        </div>
    </form>
    <div class="panel">
        <h2 class="text-xl font-black">Saved integrations</h2>
        @if($testMessage)
            <div class="mt-4 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-black text-sky-900">
                {{ $testMessage }}
            </div>
        @endif
        <div class="mt-5 overflow-x-auto">
            <table class="table">
                <thead><tr><th>Provider</th><th>Label</th><th>Base URL</th><th>API key</th><th>Mode</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($settings as $setting)
                        <tr>
                            <td>{{ ['ugsms' => 'SMS Gateway', 'sms_gateway' => 'SMS Gateway', 'iotec' => 'Payment Provider', 'sendcrane' => 'Email Provider'][$setting->provider] ?? 'Provider' }}</td>
                            <td>{{ $setting->label }}</td>
                            <td><code class="block max-w-72 whitespace-normal break-all rounded bg-slate-100 px-2 py-1 text-xs text-slate-700">{{ $setting->base_url ?: 'Not set' }}</code></td>
                            <td><code class="block max-w-80 whitespace-normal break-all rounded bg-slate-100 px-2 py-1 text-xs text-slate-700">{{ $setting->api_key ?: 'Not set' }}</code></td>
                            <td>{{ $setting->is_sandbox ? 'Sandbox' : 'Live' }}</td>
                            <td><span class="status-pill">{{ $setting->is_active ? 'active' : 'off' }}</span></td>
                            <td class="space-x-2 whitespace-nowrap">
                                <button wire:click="editIntegration({{ $setting->id }})" type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 hover:bg-slate-50">Edit</button>
                                <button wire:click="testIntegration({{ $setting->id }})" wire:loading.attr="disabled" wire:target="testIntegration({{ $setting->id }})" type="button" class="rounded-lg bg-sky-500 px-3 py-2 text-xs font-black text-white hover:bg-sky-600 disabled:opacity-50">
                                    <span wire:loading.remove wire:target="testIntegration({{ $setting->id }})">Test</span>
                                    <span wire:loading wire:target="testIntegration({{ $setting->id }})">Testing...</span>
                                </button>
                                <button wire:click="deleteIntegration({{ $setting->id }})" data-swal-confirm="Delete this integration?" data-swal-title="Delete integration?" data-swal-icon="warning" data-swal-confirm-text="Delete" data-swal-confirm-color="#dc2626" type="button" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-black text-red-700 hover:bg-red-50">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                    @if($settings->isEmpty())
                        <tr><td colspan="7">No integrations saved yet.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    </div>
</section>
