<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ShamaSMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased" data-route-name="{{ request()->route()?->getName() }}">
    <nav class="sticky top-0 z-30 border-b border-sky-100 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-3 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('compose') }}" class="mr-auto flex items-center gap-2 font-black tracking-tight text-sky-700 sm:mr-4">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-sky-500 text-white">S</span>
                <span>ShamaSMS</span>
            </a>
            <div class="order-3 -mx-4 flex w-[calc(100%+2rem)] gap-1 overflow-x-auto px-4 pb-1 text-sm font-semibold sm:order-none sm:mx-0 sm:w-auto sm:flex-1 sm:flex-wrap sm:overflow-visible sm:px-0 sm:pb-0">
                <a data-tour="nav-compose" class="nav-link @if(request()->routeIs('compose')) active @endif" href="{{ route('compose') }}">Compose SMS</a>
                <a data-tour="nav-sent" class="nav-link @if(request()->routeIs('sent')) active @endif" href="{{ route('sent') }}">Sent</a>
                <a data-tour="nav-phonebook" class="nav-link @if(request()->routeIs('phonebook')) active @endif" href="{{ route('phonebook') }}">Phonebook</a>
                <a data-tour="nav-buy" class="nav-link @if(request()->routeIs('buy')) active @endif" href="{{ route('buy') }}">Buy</a>
                <a data-tour="nav-me2u" class="nav-link @if(request()->routeIs('me2u')) active @endif" href="{{ route('me2u') }}">Me 2 U</a>
                <a data-tour="nav-developers" class="nav-link @if(request()->routeIs('developers')) active @endif" href="{{ route('developers') }}">API Docs</a>
                <a data-tour="nav-settings" class="nav-link @if(request()->routeIs('settings')) active @endif" href="{{ route('settings') }}">Settings</a>
                @if(auth()->user()->is_admin)
                    <a class="nav-link @if(request()->routeIs('admin.*')) active @endif" href="{{ route('admin.dashboard') }}">Admin</a>
                @endif
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                <div data-tour="credits" class="rounded-lg border border-sky-100 bg-sky-50 px-3 py-2 text-sm font-bold text-sky-800">
                    My credits {{ number_format(auth()->user()->sms_balance) }}
                </div>
                <button type="button" data-tour-start class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-black text-white hover:bg-slate-800">Tour</button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold hover:bg-slate-100">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if(session('status'))
            <div class="mb-5 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-900">{{ session('status') }}</div>
        @endif
        {{ $slot }}
    </main>

    @include('partials.tour')
    @livewireScripts
</body>
</html>
