<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ShamaSMS') }}</title>
    @include('partials.google-tag')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased" data-route-name="{{ request()->route()?->getName() }}">
    <nav class="sticky top-0 z-30 border-b border-sky-100 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-3 px-3 py-3 sm:flex-nowrap sm:px-6 lg:px-8">
            <div class="flex w-full items-center justify-between gap-2 sm:w-auto sm:shrink-0 sm:justify-start">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2 font-black tracking-tight text-sky-700 sm:mr-4">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sky-500 text-white sm:h-10 sm:w-10">S</span>
                    <span class="text-sm sm:text-base">ShamaSMS</span>
                </a>
                <div class="ml-auto flex min-w-0 items-center justify-end gap-1.5 sm:gap-3">
                    <div data-tour="credits" class="whitespace-nowrap rounded-lg border border-sky-100 bg-sky-50 px-2 py-1.5 text-xs font-bold text-sky-800 sm:px-3 sm:py-2 sm:text-sm">
                        My credits {{ number_format(auth()->user()->sms_balance) }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="whitespace-nowrap rounded-lg border border-slate-200 px-2 py-1.5 text-xs font-semibold hover:bg-slate-100 sm:px-3 sm:py-2 sm:text-sm">Logout</button>
                    </form>
                </div>
            </div>
            <div class="flex w-full flex-wrap gap-1 text-xs font-semibold sm:w-auto sm:flex-1 sm:text-sm">
                <a data-tour="nav-compose" class="nav-link @if(request()->routeIs('compose')) active @endif" href="{{ route('compose') }}">Compose SMS</a>
                <a data-tour="nav-sent" class="nav-link @if(request()->routeIs('sent')) active @endif" href="{{ route('sent') }}">Sent</a>
                <a data-tour="nav-phonebook" class="nav-link @if(request()->routeIs('phonebook')) active @endif" href="{{ route('phonebook') }}">Phonebook</a>
                <a data-tour="nav-buy" class="nav-link @if(request()->routeIs('buy')) active @endif" href="{{ route('buy') }}">Buy</a>
                <a data-tour="nav-me2u" class="nav-link @if(request()->routeIs('me2u')) active @endif" href="{{ route('me2u') }}">Me 2 U</a>
                <a data-tour="nav-developers" class="nav-link @if(request()->routeIs('developers')) active @endif" href="{{ route('developers') }}">API Docs</a>
                <a data-tour="nav-settings" class="nav-link @if(request()->routeIs('settings')) active @endif" href="{{ route('settings') }}">Settings</a>
                @if(auth()->user()->is_admin)
                    <a class="nav-link @if(request()->routeIs('admin.payments')) active @endif" href="{{ route('admin.payments') }}">Payments</a>
                    <a class="nav-link @if(request()->routeIs('admin.reports')) active @endif" href="{{ route('admin.reports') }}">Reports</a>
                    <a class="nav-link @if(request()->routeIs('admin.dashboard')) active @endif" href="{{ route('admin.dashboard') }}">Admin</a>
                @endif
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @php
            $activeAdvert = \App\Models\Advert::query()->where('is_active', true)->latest()->first();
        @endphp
        @if($activeAdvert)
            <section class="mb-5 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 shadow-sm shadow-sky-100">
                <p class="text-sm font-black text-sky-950">{{ $activeAdvert->title }}</p>
                <p class="mt-1 whitespace-pre-wrap text-sm leading-6 text-sky-900">{{ $activeAdvert->body }}</p>
            </section>
        @endif
        @if(session('status'))
            <div class="mb-5 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-900">{{ session('status') }}</div>
        @endif
        {{ $slot }}
    </main>
 <footer class="mx-auto max-w-7xl px-4 pb-6 sm:px-6 lg:px-8">
 <p class="text-center text-xs text-slate-400">Designed by <a href="https://kishanit.com/" target="_blank" rel="noopener" class="font-semibold text-sky-600 hover:underline">Kishan IT Solutions LTD</a></p>
 </footer>

    @livewireScripts
</body>
</html>
