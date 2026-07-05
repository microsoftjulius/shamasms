<div class="auth-shell">
    <div class="auth-copy hidden lg:block">
        <a href="{{ route('home') }}" class="mb-8 inline-flex items-center gap-2 font-black text-white">
            <span class="grid h-10 w-10 place-items-center rounded-lg bg-sky-500 text-white">S</span>
            <span>ShamaSMS</span>
        </a>
        <h1 class="text-4xl font-black tracking-normal text-white">Set a new password.</h1>
        <p class="mt-4 max-w-xl text-lg leading-8 text-white">Choose a strong password that you haven't used before.</p>
    </div>
    <form wire:submit="resetPassword" class="auth-card">
        <h2 class="text-2xl font-black">Reset Password</h2>
        <p class="mt-1 text-sm text-slate-600">Enter your new password below.</p>

        <div class="mt-5 grid gap-4">
            <label class="label">
                Email address
                <input wire:model="email" type="email" class="field" readonly>
            </label>

            <label class="label">
                New Password <span class="req">*</span>
                <span class="password-wrap">
                    <input id="reset-password" wire:model="password" type="password" class="field pr-12" placeholder="Min. 8 characters">
                    <button type="button" class="password-eye" aria-label="Show password" onclick="togglePassword('reset-password', this)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.1 12s3.5-6.5 9.9-6.5S21.9 12 21.9 12s-3.5 6.5-9.9 6.5S2.1 12 2.1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </span>
            </label>

            <label class="label">
                Confirm Password <span class="req">*</span>
                <span class="password-wrap">
                    <input id="reset-confirm" wire:model="password_confirmation" type="password" class="field pr-12" placeholder="Repeat password">
                    <button type="button" class="password-eye" aria-label="Show password" onclick="togglePassword('reset-confirm', this)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.1 12s3.5-6.5 9.9-6.5S21.9 12 21.9 12s-3.5 6.5-9.9 6.5S2.1 12 2.1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </span>
            </label>
        </div>

        @foreach($errors->all() as $error)
            <p class="mt-2 text-sm font-semibold text-red-600">{{ $error }}</p>
        @endforeach

        <button type="submit" class="mt-6 w-full rounded-lg bg-sky-500 px-5 py-3 font-black text-white hover:bg-sky-600">
            Reset Password
        </button>

        <div class="mt-4 border-t border-slate-100 pt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm font-bold text-sky-700 hover:underline">Back to Login</a>
        </div>
    </form>
</div>
<script>
    function togglePassword(id, button) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
        button.setAttribute('aria-label', input.type === 'password' ? 'Show password' : 'Hide password');
    }
</script>
