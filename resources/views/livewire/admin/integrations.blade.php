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
                                <button wire:click="deleteIntegration({{ $setting->id }})" wire:confirm="Delete this integration?" type="button" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-black text-red-700 hover:bg-red-50">Delete</button>
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
