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
        <div class="mt-6 grid gap-4">
            <label class="label">Provider <span class="req">*</span><select wire:model="provider" class="field"><option value="sms_gateway">SMS Gateway</option><option value="iotec">Payment Provider</option><option value="sendcrane">Email Provider</option></select></label>
            <label class="label">Label <span class="req">*</span><input wire:model="label" class="field"></label>
            <label class="label">Base URL<input wire:model="base_url" class="field" placeholder="https://api.example.com"></label>
            <label class="label">API key<textarea wire:model="api_key" class="field min-h-24 font-mono text-sm"></textarea></label>
            <label class="label">API secret<textarea wire:model="api_secret" class="field min-h-24"></textarea></label>
            <label class="label">Username<input wire:model="username" class="field"></label>
            <label class="label">Password<input wire:model="password" type="password" class="field"></label>
            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700"><input wire:model="is_sandbox" type="checkbox" class="rounded border-slate-300"> Sandbox</label>
        </div>
        <button class="mt-6 rounded-lg bg-sky-500 px-6 py-3 font-black text-white hover:bg-sky-600">Save integration</button>
    </form>
    <div class="panel">
        <h2 class="text-xl font-black">Saved integrations</h2>
        <div class="mt-5 overflow-x-auto">
            <table class="table">
                <thead><tr><th>Provider</th><th>Label</th><th>API key</th><th>Mode</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($settings as $setting)
                        <tr><td>{{ ['ugsms' => 'SMS Gateway', 'sms_gateway' => 'SMS Gateway', 'iotec' => 'Payment Provider', 'sendcrane' => 'Email Provider'][$setting->provider] ?? 'Provider' }}</td><td>{{ $setting->label }}</td><td><code class="block max-w-80 whitespace-normal break-all rounded bg-slate-100 px-2 py-1 text-xs text-slate-700">{{ $setting->api_key ?: 'Not set' }}</code></td><td>{{ $setting->is_sandbox ? 'Sandbox' : 'Live' }}</td><td><span class="status-pill">{{ $setting->is_active ? 'active' : 'off' }}</span></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>
</section>
