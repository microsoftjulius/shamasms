<section class="grid gap-6 lg:grid-cols-2">
    <form wire:submit="saveProfile" class="panel" data-tour="settings-profile">
        <h1 class="page-title">Settings</h1>
        <p class="page-subtitle">Manage user details, balance visibility, password, and developer access.</p>
        <div class="mt-6 grid gap-4">
            <label class="label">Name <span class="req">*</span><input wire:model="name" class="field"></label>
            <label class="label">Phone<input wire:model="phone" class="field"></label>
            <label class="label">Company<input wire:model="company" class="field"></label>
            <div class="rounded-lg bg-sky-50 px-4 py-3 font-black text-sky-900">SMS balance: {{ number_format(auth()->user()->sms_balance) }}</div>
        </div>
        <button class="mt-6 rounded-lg bg-sky-500 px-6 py-3 font-black text-white hover:bg-sky-600">Save profile</button>
    </form>

    <form wire:submit="changePassword" class="panel" data-tour="settings-password">
        <h2 class="text-xl font-black">Reset password</h2>
        <div class="mt-6 grid gap-4">
            <label class="label">Current password <span class="req">*</span>
                <span class="password-wrap">
                    <input id="settings-current-password" wire:model="current_password" type="password" class="field pr-12">
                    <button type="button" class="password-eye" aria-label="Show password" onclick="togglePassword('settings-current-password', this)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.1 12s3.5-6.5 9.9-6.5S21.9 12 21.9 12s-3.5 6.5-9.9 6.5S2.1 12 2.1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </span>
            </label>
            <label class="label">New password <span class="req">*</span>
                <span class="password-wrap">
                    <input id="settings-new-password" wire:model="password" type="password" class="field pr-12">
                    <button type="button" class="password-eye" aria-label="Show password" onclick="togglePassword('settings-new-password', this)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.1 12s3.5-6.5 9.9-6.5S21.9 12 21.9 12s-3.5 6.5-9.9 6.5S2.1 12 2.1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </span>
            </label>
            <label class="label">Confirm password <span class="req">*</span>
                <span class="password-wrap">
                    <input id="settings-password-confirmation" wire:model="password_confirmation" type="password" class="field pr-12">
                    <button type="button" class="password-eye" aria-label="Show password" onclick="togglePassword('settings-password-confirmation', this)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.1 12s3.5-6.5 9.9-6.5S21.9 12 21.9 12s-3.5 6.5-9.9 6.5S2.1 12 2.1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </span>
            </label>
        </div>
        <button class="mt-6 rounded-lg bg-slate-900 px-6 py-3 font-black text-white hover:bg-slate-800">Change password</button>
    </form>

    <div class="panel lg:col-span-2" data-tour="settings-api">
        <h2 class="text-xl font-black">Developer API keys</h2>
        <p class="page-subtitle">Use API keys when connecting another website or app to ShamaSMS. Sandbox keys let developers test requests without sending real SMS or spending credits. Live keys send real messages and debit the account balance.</p>
        <form wire:submit="createApiKey" class="mt-5 grid gap-4 md:grid-cols-[1fr_200px_auto]" data-tour="settings-api-form">
            <label class="label">Key name <span class="req">*</span><input wire:model="api_key_name" class="field"></label>
            <label class="label">API key mode <span class="req">*</span><select wire:model="api_key_mode" class="field"><option value="sandbox">Sandbox test key</option><option value="live">Live sending key</option></select></label>
            <button class="self-end rounded-lg bg-sky-500 px-6 py-3 font-black text-white hover:bg-sky-600">Create key</button>
        </form>
        <p class="mt-4 text-sm leading-6 text-slate-600">Full integration guide: <a class="font-black text-sky-700" href="{{ route('developers') }}">open API documentation</a>.</p>
        <div class="mt-5 overflow-x-auto">
            <table class="table">
                <thead><tr><th>Name</th><th>Mode</th><th>Key</th><th>Last used</th></tr></thead>
                <tbody>
                    @forelse($keys as $key)
                        <tr><td>{{ $key->name }}</td><td>{{ $key->mode }}</td><td class="font-mono text-xs">{{ $key->plain_text_key }}</td><td>{{ $key->last_used_at?->diffForHumans() ?? 'Never' }}</td></tr>
                    @empty
                        <tr><td colspan="4">No API keys yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
<script>
    function togglePassword(id, button) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
        button.setAttribute('aria-label', input.type === 'password' ? 'Show password' : 'Hide password');
    }
</script>
