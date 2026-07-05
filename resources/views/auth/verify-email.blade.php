<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify email - ShamaSMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950">
    <div class="mx-auto flex min-h-screen max-w-xl items-center px-4">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-xl shadow-sky-100">
            <div class="mb-5 flex items-center gap-2 font-black text-sky-700">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-sky-500 text-white">S</span>
                <span>ShamaSMS</span>
            </div>
            <h1 class="text-2xl font-black">Verify your email</h1>
            <p class="mt-3 leading-7 text-slate-700">A verification link has been sent to your email address through Sendcrane. If it does not appear soon, check your spam or promotions folder, then resend the link.</p>
            @if(session('status') === 'verification-link-sent')
                <p class="mt-4 rounded-lg bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-800">A fresh verification link has been sent.</p>
            @endif
            <div class="mt-6 flex flex-wrap gap-3">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button class="rounded-lg bg-sky-500 px-5 py-3 font-black text-white hover:bg-sky-600">Resend link</button>
                </form>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-lg border border-slate-200 px-5 py-3 font-bold hover:bg-slate-50">Logout</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
