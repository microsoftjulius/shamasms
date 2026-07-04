<div class="auth-shell">
    <div class="auth-copy">
        <a href="{{ route('home') }}" class="mb-8 inline-flex items-center gap-2 font-black text-sky-700">
            <span class="grid h-10 w-10 place-items-center rounded-lg bg-sky-500 text-white">S</span>
            <span>ShamaSMS</span>
        </a>
        <h1 class="text-4xl font-black tracking-normal text-slate-950">Start sending smarter SMS.</h1>
        <p class="mt-4 max-w-xl text-lg leading-8 text-slate-700">Create your account, verify your email through Sendcrane-backed mail delivery, then compose your first SMS from the navbar dashboard.</p>
        <div class="mt-8 grid max-w-xl gap-3 sm:grid-cols-3">
            <div class="auth-stat"><strong>160</strong><span>chars per segment</span></div>
            <div class="auth-stat"><strong>API</strong><span>ready for apps</span></div>
            <div class="auth-stat"><strong>SMS</strong><span>delivery network</span></div>
        </div>
    </div>
    <form wire:submit="register" class="auth-card">
        <h2 class="text-2xl font-black">Create account</h2>
        <div class="mt-5 grid gap-4">
            <label class="label">Full name <span class="req">*</span><input wire:model="name" class="field"></label>
            <label class="label">Username <span class="req">*</span><input wire:model="username" class="field"></label>
            <label class="label">Email <span class="req">*</span><input wire:model="email" type="email" class="field"></label>
            <label class="label">Phone<input wire:model="phone" class="field"></label>
            <label class="label">Password <span class="req">*</span>
                <span class="password-wrap">
                    <input id="signup-password" wire:model="password" type="password" class="field pr-12">
                    <button type="button" class="password-eye" aria-label="Show password" onclick="togglePassword('signup-password', this)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.1 12s3.5-6.5 9.9-6.5S21.9 12 21.9 12s-3.5 6.5-9.9 6.5S2.1 12 2.1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </span>
            </label>
            <label class="label">Confirm password <span class="req">*</span>
                <span class="password-wrap">
                    <input id="signup-password-confirmation" wire:model="password_confirmation" type="password" class="field pr-12">
                    <button type="button" class="password-eye" aria-label="Show password" onclick="togglePassword('signup-password-confirmation', this)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.1 12s3.5-6.5 9.9-6.5S21.9 12 21.9 12s-3.5 6.5-9.9 6.5S2.1 12 2.1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </span>
            </label>
        </div>
        @foreach($errors->all() as $error)
            <p class="mt-2 text-sm font-semibold text-red-600">{{ $error }}</p>
        @endforeach
        <button class="mt-6 w-full rounded-lg bg-sky-500 px-5 py-3 font-black text-white hover:bg-sky-600">Start now</button>
        <p class="mt-4 text-sm text-slate-600">Already registered? <a class="font-bold text-sky-700" href="{{ route('login') }}">Login</a></p>
    </form>
</div>
<script>
    function togglePassword(id, button) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
        button.setAttribute('aria-label', input.type === 'password' ? 'Show password' : 'Hide password');
    }
</script>
