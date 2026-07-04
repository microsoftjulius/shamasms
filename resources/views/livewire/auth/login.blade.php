<div class="auth-shell">
    <div class="auth-copy hidden lg:block">
        <a href="{{ route('home') }}" class="mb-8 inline-flex items-center gap-2 font-black text-sky-700">
            <span class="grid h-10 w-10 place-items-center rounded-lg bg-sky-500 text-white">S</span>
            <span>ShamaSMS</span>
        </a>
        <h1 class="text-4xl font-black tracking-normal text-slate-950">Welcome back.</h1>
        <p class="mt-4 max-w-xl text-lg leading-8 text-slate-700">Your SMS balance is visible across the app, and Compose SMS is always the first working screen after login.</p>
        <div class="mt-8 max-w-xl rounded-lg border border-sky-200 bg-white/70 p-5 shadow-sm shadow-sky-100">
            <p class="text-sm font-black text-sky-800">Fast workflow</p>
            <p class="mt-2 text-sm leading-6 text-slate-700">Login, choose recipients, set the sender ID, send now or schedule later. No sidebar, no clutter.</p>
        </div>
    </div>
    <form wire:submit="login" class="auth-card">
        <h2 class="text-2xl font-black">Login</h2>
        <div class="mt-5 grid gap-4">
            <label class="label">Email <span class="req">*</span><input wire:model="email" type="email" class="field"></label>
            <label class="label">Password <span class="req">*</span>
                <span class="password-wrap">
                    <input id="login-password" wire:model="password" type="password" class="field pr-12">
                    <button type="button" class="password-eye" aria-label="Show password" onclick="togglePassword('login-password', this)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.1 12s3.5-6.5 9.9-6.5S21.9 12 21.9 12s-3.5 6.5-9.9 6.5S2.1 12 2.1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </span>
            </label>
            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700"><input wire:model="remember" type="checkbox" class="rounded border-slate-300"> Remember me</label>
        </div>
        @foreach($errors->all() as $error)
            <p class="mt-2 text-sm font-semibold text-red-600">{{ $error }}</p>
        @endforeach
        <button class="mt-6 w-full rounded-lg bg-sky-500 px-5 py-3 font-black text-white hover:bg-sky-600">Login</button>
        <p class="mt-4 text-sm text-slate-600">New to ShamaSMS? <a class="font-bold text-sky-700" href="{{ route('register') }}">Start now</a></p>
    </form>
</div>
<script>
    function togglePassword(id, button) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
        button.setAttribute('aria-label', input.type === 'password' ? 'Show password' : 'Hide password');
    }
</script>
