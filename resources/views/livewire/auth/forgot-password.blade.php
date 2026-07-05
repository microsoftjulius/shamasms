<div class="auth-shell">
 <div class="auth-copy hidden lg:block">
 <a href="{{ route('home') }}" class="mb-8 inline-flex items-center gap-2 font-black text-sky-700">
 <span class="grid h-10 w-10 place-items-center rounded-lg bg-sky-500 text-white">S</span>
 <span>ShamaSMS</span>
 </a>
 <h1 class="text-4xl font-black tracking-normal text-slate-950">Reset your password.</h1>
 <p class="mt-4 max-w-xl text-lg leading-8 text-slate-700">Enter your email address and we will send you a password reset link.</p>
 </div>
 <div class="auth-card">
 @if ($sent)
 <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
 If an account with that email exists, a reset link has been sent. Check your inbox.
 </div>
 <a href="{{ route('login') }}" class="mt-5 block text-center text-sm font-bold text-sky-700 hover:underline">Back to Login</a>
 @else
 <h2 class="text-2xl font-black">Forgot Password</h2>
 <p class="mt-1 text-sm text-slate-600">Enter your email and we will send a reset link.</p>
 <form wire:submit="send" class="mt-5 grid gap-4">
 <label class="label">Email address <span class="req">*</span><input wire:model="email" type="email" class="field" placeholder="you@example.com"></label>
 @foreach($errors->all() as $error)
 <p class="text-sm font-semibold text-red-600">{{ $error }}</p>
 @endforeach
 <button type="submit" class="w-full rounded-lg bg-sky-500 px-5 py-3 font-black text-white hover:bg-sky-600">Send reset link</button>
 </form>
 <div class="mt-4 border-t border-slate-100 pt-4 text-center">
 <a href="{{ route('login') }}" class="text-sm font-bold text-sky-700 hover:underline">Back to Login</a>
 </div>
 @endif
 </div>
</div>