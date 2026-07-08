<div class="auth-shell">
    <div class="auth-copy hidden lg:block">
        <a href="{{ route('home') }}" class="mb-8 inline-flex items-center gap-2 font-black text-white">
            <span class="grid h-10 w-10 place-items-center rounded-lg bg-sky-500 text-white">S</span>
            <span>ShamaSMS</span>
        </a>
        <h1 class="text-4xl font-black tracking-normal text-white">Welcome back.</h1>
        <p class="mt-4 max-w-xl text-lg leading-8 text-white">Your SMS balance is visible across the app, and Compose SMS is always the first working screen after login.</p>
        <div class="mt-8 max-w-xl rounded-lg border border-white/20 bg-white/10 p-5">
            <p class="text-sm font-black text-white">Fast workflow</p>
            <p class="mt-2 text-sm leading-6 text-white/80">Login, choose recipients, set the sender ID, send now or schedule later. No sidebar, no clutter.</p>
        </div>
    </div>
    <form wire:submit="login" class="auth-card">
        <h2 class="text-2xl font-black">Login</h2>
        <div class="mt-5 grid gap-4">
            <label class="label">Username <span class="req">*</span><input wire:model="username" class="field" placeholder="Enter your username"></label>
            <label class="label">Password <span class="req">*</span>
                <span class="password-wrap">
                    <input id="login-password" wire:model="password" type="password" class="field pr-12" placeholder="Enter your password">
                    <button type="button" class="password-eye" aria-label="Show password" onclick="togglePassword('login-password', this)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.1 12s3.5-6.5 9.9-6.5S21.9 12 21.9 12s-3.5 6.5-9.9 6.5S2.1 12 2.1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </span>
            </label>
            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700"><input wire:model="remember" type="checkbox" class="rounded border-slate-300"> Remember me</label>
        </div>
        <div class="text-right mt-1"><a href="{{ route('password.request') }}" class="text-xs font-semibold text-sky-600 hover:underline">Forgot password?</a></div>
 @foreach($errors->all() as $error)
            <p class="mt-2 text-sm font-semibold text-red-600">{{ $error }}</p>
        @endforeach
        <button class="mt-6 w-full rounded-lg bg-sky-500 px-5 py-3 font-black text-white hover:bg-sky-600">Login</button>
        <div class="mt-3 border-t border-slate-100 pt-4">
 <p class="mb-3 text-center text-xs font-semibold uppercase tracking-widest text-slate-400">Don't have an account?</p>
 <a href="{{ route('register') }}" class="flex w-full items-center justify-center gap-2 rounded-lg border-2 border-sky-500 bg-transparent px-5 py-3 font-black text-sky-600 shadow-sm transition hover:border-sky-600 hover:bg-sky-50 hover:text-sky-700 hover:shadow-md">
 <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
 Create account
 </a>
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
