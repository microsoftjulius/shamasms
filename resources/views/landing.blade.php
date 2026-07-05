<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SEO --}}
    <title>ShamaSMS — Bulk SMS Platform for Businesses, Schools & Developers in Uganda</title>
    <meta name="description" content="Send bulk SMS, personalized messages, and scheduled reminders to any phone in Uganda. Fast delivery, simple pricing, and a powerful API for developers. Start free today.">
    <meta name="keywords" content="bulk SMS Uganda, send SMS online, SMS API Uganda, business SMS, school SMS Uganda, bulk messaging, affordable SMS">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="ShamaSMS — Bulk SMS for Businesses &amp; Developers in Uganda">
    <meta property="og:description" content="Send bulk SMS, personalized messages, and scheduled reminders instantly. Trusted by businesses, schools, churches, and SACCOs across Uganda.">
    <meta property="og:image" content="{{ url('/images/bulk-sms.png') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="ShamaSMS — Bulk SMS for Uganda">
    <meta name="twitter:description" content="Fast, reliable bulk SMS with personalization, scheduling, and a developer API.">
    <meta name="twitter:image" content="{{ url('/images/bulk-sms.png') }}">

    {{-- Structured data --}}
    <script type="application/ld+json">
    {
        "\u0040context": "https://schema.org",
        "\u0040type": "SoftwareApplication",
        "name": "ShamaSMS",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "description": "Bulk SMS platform for businesses, schools, churches, SACCOs, and developers in Uganda.",
        "url": "{{ url('/') }}",
        "offers": { "\u0040type": "Offer", "price": "0", "priceCurrency": "UGX" }
    }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="overflow-x-hidden bg-white text-slate-950 antialiased">
<div class="landing-header-fixed">
<div class="landing-topbar bg-sky-600 text-white" role="banner">
 <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-2 text-xs font-semibold sm:px-6 lg:px-8">
 <div class="flex flex-wrap items-center gap-4">
 <a href="tel:+256702133428" class="flex items-center gap-1.5 text-white hover:text-sky-200 transition">
 <svg class="h-3.5 w-3.5 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg>
 +256 702 133428
 </a>
 <a href="mailto:support@shamasms.com" class="flex items-center gap-1.5 text-white hover:text-sky-200 transition">
 <svg class="h-3.5 w-3.5 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
 support@shamasms.com
 </a>
 </div>
 <span class="hidden text-sky-200 sm:inline">Fast &amp; reliable bulk SMS &middot; Uganda</span>
 </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════════
     NAVIGATION
═══════════════════════════════════════════════════════════════════════ --}}
<header class="landing-nav">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8">
        {{-- Logo --}}
        <a href="/" class="flex min-w-0 items-center gap-2.5 font-black text-sky-700" aria-label="ShamaSMS home">
            <svg viewBox="0 0 48 48" class="h-9 w-9 shrink-0" aria-hidden="true"><rect width="48" height="48" rx="12" fill="#0ea5e9"/><path d="M14 20c0-1.1.9-2 2-2h16a2 2 0 0 1 2 2v.01L24 26 14 20.01V20z" fill="white"/><path d="M14 22.5l9.4 6.1a1.2 1.2 0 0 0 1.2 0l9.4-6.1V32a2 2 0 0 1-2 2H16a2 2 0 0 1-2-2V22.5z" fill="white"/></svg>
            <span class="text-lg tracking-tight">ShamaSMS</span>
        </a>

        {{-- Desktop nav --}}
        <nav class="hidden items-center gap-1 text-sm font-semibold text-slate-700 md:flex" aria-label="Main navigation">
            <a href="#why-sms"     class="rounded-lg px-3 py-2 hover:bg-sky-50 hover:text-sky-700">Why SMS</a>
            <a href="#features"    class="rounded-lg px-3 py-2 hover:bg-sky-50 hover:text-sky-700">Features</a>
            <a href="#how-it-works"class="rounded-lg px-3 py-2 hover:bg-sky-50 hover:text-sky-700">How it works</a>
            <a href="#pricing"     class="rounded-lg px-3 py-2 hover:bg-sky-50 hover:text-sky-700">Pricing</a>
            <a href="{{ route('developers') }}" class="rounded-lg px-3 py-2 hover:bg-sky-50 hover:text-sky-700">API Docs</a>
        </nav>

        {{-- Auth buttons --}}
        <div class="flex min-w-0 items-center gap-2">
            <a href="{{ route('login') }}" class="btn-primary shrink-0 !py-2 !px-4 !text-sm sm:!px-5">Login</a>
        </div>
    </div>
</header>
</div>

<main class="landing-main">

{{-- ══════════════════════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════════════════════ --}}
<section class="landing-hero border-b border-sky-100" aria-labelledby="hero-heading">
    <div class="mx-auto grid max-w-7xl items-center gap-8 px-4 pb-8 pt-4 sm:px-6 sm:pb-14 sm:pt-5 lg:grid-cols-2 lg:px-8 lg:pb-24 lg:pt-6">

        {{-- Copy --}}
        <div class="min-w-0">
 <p class="section-eyebrow">
                <svg class="h-3 w-3 fill-sky-500" viewBox="0 0 8 8" aria-hidden="true"><circle cx="4" cy="4" r="4"/></svg>
                Trusted bulk SMS platform · Uganda
            </p>
            <h1 id="hero-heading" class="text-4xl font-black leading-tight tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                Reach anyone,<br>
                <span class="gradient-text">instantly by SMS.</span>
            </h1>
            <p class="mt-5 max-w-xl text-lg leading-8 text-slate-600">
                ShamaSMS lets businesses, schools, churches, SACCOs, and developers send bulk SMS, personalized messages, and scheduled reminders — all from one simple dashboard.
            </p>

            {{-- Social proof nudge --}}
            <p class="mt-4 text-sm font-semibold text-slate-500">
                <span class="text-sky-600">✓</span> No contract &nbsp;·&nbsp;
                <span class="text-sky-600">✓</span> Pay as you go &nbsp;·&nbsp;
                <span class="text-sky-600">✓</span> Instant delivery
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a href="{{ route('login') }}" class="btn-primary">
                    Start sending free SMS
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="#how-it-works" class="btn-outline">See how it works</a>
 <a href="tel:+256702133428" class="btn-outline flex items-center gap-2"><svg class="h-4 w-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg>Call us</a>
            </div>
        </div>

        {{-- Hero image --}}
        <div class="hidden lg:flex lg:justify-end lg:min-w-0">
            <div class="relative w-full max-w-full overflow-hidden rounded-3xl lg:max-w-lg">
                
                <img
                    src="/images/bulk-sms.png"
                    alt="Bulk SMS dashboard illustration showing messages being sent to multiple recipients"
                    class="relative w-full max-w-full rounded-2xl shadow-2xl shadow-sky-100"
                    loading="eager"
                    width="600"
                    height="420"
                >
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════
     STATS BAND
═══════════════════════════════════════════════════════════════════════ --}}
<section class="border-b border-slate-100 bg-slate-50" aria-label="Platform statistics">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-px bg-slate-200 sm:grid-cols-2 md:grid-cols-4">
        <div class="bg-white px-6 py-8 text-center">
            <strong class="block text-4xl font-black text-sky-600">98%</strong>
            <span class="mt-1 block text-sm font-semibold text-slate-600">SMS open rate vs 20% email</span>
        </div>
        <div class="bg-white px-6 py-8 text-center">
            <strong class="block text-4xl font-black text-sky-600">&lt; 7s</strong>
            <span class="mt-1 block text-sm font-semibold text-slate-600">Average delivery time</span>
        </div>
        <div class="bg-white px-6 py-8 text-center">
            <strong class="block text-4xl font-black text-sky-600">100%</strong>
            <span class="mt-1 block text-sm font-semibold text-slate-600">No smartphone needed</span>
        </div>
        <div class="bg-white px-6 py-8 text-center">
            <strong class="block text-4xl font-black text-sky-600">24/7</strong>
            <span class="mt-1 block text-sm font-semibold text-slate-600">Platform availability</span>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════
     WHY SMS
═══════════════════════════════════════════════════════════════════════ --}}
<section id="why-sms" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8" aria-labelledby="why-heading">
    <div class="mx-auto max-w-2xl text-center">
        <p class="section-eyebrow mx-auto">Why SMS</p>
        <h2 id="why-heading" class="text-4xl font-black tracking-tight text-slate-950">
            Why SMS is still the <span class="gradient-text">most powerful</span> way to reach people
        </h2>
        <p class="mt-4 text-base leading-7 text-slate-600">
            In Uganda, over 28 million people use mobile phones — but not all have smartphones or data. SMS reaches everyone, every time, without an internet connection.
        </p>
    </div>

    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

        <div class="why-card">
            <div class="why-card-icon" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <h3>Instant, guaranteed delivery</h3>
            <p>SMS messages are delivered within seconds directly to the handset — no app download, no data connection, no spam folder. Your message lands where it can't be missed.</p>
        </div>

        <div class="why-card">
            <div class="why-card-icon" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <h3>98% open rate</h3>
            <p>Compared to email's 20% open rate, SMS is read by almost everyone within 3 minutes. That means your promotions, reminders, and alerts actually get seen.</p>
        </div>

        <div class="why-card">
            <div class="why-card-icon" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3>Reaches everyone in Uganda</h3>
            <p>SMS works on every phone — smart or basic. Reach customers, students, members, and staff regardless of their device, network, or internet access.</p>
        </div>

        <div class="why-card">
            <div class="why-card-icon" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3>Save time with scheduling</h3>
            <p>Set your messages to send at the right moment — morning reminders, payment due dates, weekly newsletters — all automated so you focus on running your business.</p>
        </div>

        <div class="why-card">
            <div class="why-card-icon" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
            </div>
            <h3>Personal at scale</h3>
            <p>Insert each recipient's name, balance, amount, or any custom value automatically. One message template, thousands of unique personal messages.</p>
        </div>

        <div class="why-card">
            <div class="why-card-icon" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3>Cost-effective communication</h3>
            <p>Bulk SMS costs a fraction of print, radio, or social ads — with measurable reach. Pay only for what you send, with no monthly fees or hidden costs.</p>
        </div>
    </div>

    {{-- Who is it for --}}
    <div class="mt-14 text-center">
        <p class="mb-5 text-sm font-black uppercase tracking-widest text-slate-500">Perfect for</p>
        <div class="flex flex-wrap justify-center gap-3">
            <span class="use-pill">🏢 Businesses</span>
            <span class="use-pill">🏫 Schools &amp; Universities</span>
            <span class="use-pill">⛪ Churches &amp; NGOs</span>
            <span class="use-pill">🏦 SACCOs &amp; MFIs</span>
            <span class="use-pill">🏥 Clinics &amp; Hospitals</span>
            <span class="use-pill">🛒 E-commerce</span>
            <span class="use-pill">💻 Developers &amp; SaaS</span>
            <span class="use-pill">📦 Delivery services</span>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════
     FEATURES
═══════════════════════════════════════════════════════════════════════ --}}
<section id="features" class="border-y border-slate-100 bg-slate-50 py-20" aria-labelledby="features-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow mx-auto">Features</p>
            <h2 id="features-heading" class="text-4xl font-black tracking-tight text-slate-950">
                Everything you need to send SMS <span class="gradient-text">like a pro</span>
            </h2>
        </div>

        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

            {{-- Feature 1 --}}
            <div class="feature-card group">
                <div class="mb-3 grid h-11 w-11 place-items-center rounded-xl bg-sky-500 text-white" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <h2>Bulk SMS</h2>
                <p>Send one message to hundreds or thousands of recipients at once. Upload a file, paste numbers, or select a phonebook group — done in under a minute.</p>
            </div>

            {{-- Feature 2 --}}
            <div class="feature-card">
                <div class="mb-3 grid h-11 w-11 place-items-center rounded-xl bg-cyan-500 text-white" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h2>Personalized SMS</h2>
                <p>Use placeholders like <code class="inline-code">&#64;&#64;name&#64;&#64;</code>, <code class="inline-code">&#64;&#64;var1&#64;&#64;</code>–<code class="inline-code">&#64;&#64;var5&#64;&#64;</code> to inject names, balances, amounts or any value per row. Every recipient gets a unique message.</p>
            </div>

            {{-- Feature 3 --}}
            <div class="feature-card">
                <div class="mb-3 grid h-11 w-11 place-items-center rounded-xl bg-violet-500 text-white" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h2>Scheduled &amp; Recurring sends</h2>
                <p>Schedule messages to send at a specific date and time. Set recurring sends on chosen days of the week — weekly reports, payment reminders, and more, fully automated.</p>
            </div>

            {{-- Feature 4 --}}
            <div class="feature-card">
                <div class="mb-3 grid h-11 w-11 place-items-center rounded-xl bg-emerald-500 text-white" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h2>Phonebook &amp; Contact groups</h2>
                <p>Organise your contacts into named groups. Reuse them instantly from the Compose screen without uploading or typing numbers again. Build your lists once, use them forever.</p>
            </div>

            {{-- Feature 5 --}}
            <div class="feature-card">
                <div class="mb-3 grid h-11 w-11 place-items-center rounded-xl bg-amber-500 text-white" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h2>Sent messages &amp; delivery reports</h2>
                <p>View every message you have sent, who received it, the status of each recipient, and when it was delivered. Full history at a glance.</p>
            </div>

            {{-- Feature 6 --}}
            <div class="feature-card">
                <div class="mb-3 grid h-11 w-11 place-items-center rounded-xl bg-rose-500 text-white" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <h2>Me 2 U credit transfers</h2>
                <p>Share SMS credits with another ShamaSMS user instantly. Great for teams, resellers, and organisations that manage multiple sub-accounts.</p>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════
     HOW IT WORKS
═══════════════════════════════════════════════════════════════════════ --}}
<section id="how-it-works" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8" aria-labelledby="how-heading">
    <div class="mx-auto max-w-2xl text-center">
        <p class="section-eyebrow mx-auto">How it works</p>
        <h2 id="how-heading" class="text-4xl font-black tracking-tight text-slate-950">
            Up and sending in <span class="gradient-text">under 5 minutes</span>
        </h2>
        <p class="mt-4 text-base leading-7 text-slate-600">No complicated setup. No contracts. No technical knowledge required.</p>
    </div>

    <div class="mt-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Step 1 --}}
        <div class="relative flex flex-col items-center px-4 text-center">
            <div class="step-number" aria-label="Step 1">1</div>
            <div class="my-4 h-px w-full bg-sky-100 md:absolute md:left-1/2 md:top-5 md:h-px md:w-full md:translate-y-0" aria-hidden="true"></div>
            <h3 class="mt-2 text-base font-black text-slate-900">Create your account</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Sign up in 30 seconds with your email. Verify and you are in — no card required to start.</p>
        </div>

        {{-- Step 2 --}}
        <div class="flex flex-col items-center px-4 text-center">
            <div class="step-number" aria-label="Step 2">2</div>
            <h3 class="mt-6 text-base font-black text-slate-900">Add your recipients</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Type numbers manually, paste a list, upload a CSV/Excel file, or pick a saved phonebook group.</p>
        </div>

        {{-- Step 3 --}}
        <div class="flex flex-col items-center px-4 text-center">
            <div class="step-number" aria-label="Step 3">3</div>
            <h3 class="mt-6 text-base font-black text-slate-900">Write &amp; send or schedule</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Compose your message, personalise it, then send now or set a future date, time, and repeat days.</p>
        </div>

        {{-- Step 4 --}}
        <div class="flex flex-col items-center px-4 text-center">
            <div class="step-number" aria-label="Step 4">4</div>
            <h3 class="mt-6 text-base font-black text-slate-900">Track &amp; top up</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Monitor delivery reports, view your credit balance, and top up via Iotec payments any time.</p>
        </div>

    </div>

    <div class="mt-12 text-center">
        <a href="{{ route('login') }}" class="btn-primary">
            Create your free account
        </a>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════
     SOCIAL PROOF / TESTIMONIALS
═══════════════════════════════════════════════════════════════════════ --}}
<section class="border-y border-slate-100 bg-slate-50 py-20" aria-labelledby="testimonials-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow mx-auto">What users say</p>
            <h2 id="testimonials-heading" class="text-4xl font-black tracking-tight text-slate-950">
                Organisations that communicate <span class="gradient-text">smarter with SMS</span>
            </h2>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-3">

            @php
                $starSvg = '<svg class="h-4 w-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
                $stars = str_repeat($starSvg, 5);
            @endphp

            <div class="testimonial-card">
                <div class="flex gap-1 text-amber-400" aria-label="5 stars">{!! $stars !!}</div>
                <blockquote>"We send fee-balance reminders to over 800 parents every Monday. ShamaSMS saves our office hours of phone calls every week. The personalized SMS feature is a game changer."</blockquote>
                <cite>— School Administrator, Kampala</cite>
            </div>

            <div class="testimonial-card">
                <div class="flex gap-1 text-amber-400" aria-label="5 stars">{!! $stars !!}</div>
                <blockquote>"Our SACCO uses ShamaSMS to notify members of their loan repayment dates and current balances. Default rates have dropped noticeably since we started. The API made integration with our system straightforward."</blockquote>
                <cite>— Finance Manager, SACCO Uganda</cite>
            </div>

            <div class="testimonial-card">
                <div class="flex gap-1 text-amber-400" aria-label="5 stars">{!! $stars !!}</div>
                <blockquote>"I plugged ShamaSMS into my e-commerce platform using the V2 API in one afternoon. Order confirmations, delivery alerts — all automated. Customers love the instant updates."</blockquote>
                <cite>— Developer &amp; Startup Founder, Entebbe</cite>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════
     PRICING
═══════════════════════════════════════════════════════════════════════ --}}
<section id="pricing" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8" aria-labelledby="pricing-heading">
    <div class="mx-auto max-w-2xl text-center">
        <p class="section-eyebrow mx-auto">Pricing</p>
        <h2 id="pricing-heading" class="text-4xl font-black tracking-tight text-slate-950">
            Simple, <span class="gradient-text">pay-as-you-go</span> pricing
        </h2>
        <p class="mt-4 text-base leading-7 text-slate-600">
            No monthly subscriptions. No hidden fees. Buy credits when you need them and use them at your own pace.
        </p>
    </div>

    <div class="mt-12 grid gap-8 md:grid-cols-3">

        {{-- Starter --}}
        <div class="pricing-card">
            <div>
                <p class="text-sm font-black uppercase tracking-widest text-slate-500">Starter</p>
                <p class="mt-2 text-4xl font-black text-slate-950">Free</p>
                <p class="mt-1 text-sm text-slate-600">To get you started</p>
            </div>
            <ul class="flex flex-col gap-3">
                <li class="pricing-check"><svg class="h-5 w-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Dashboard access</li>
                <li class="pricing-check"><svg class="h-5 w-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Phonebook &amp; groups</li>
                <li class="pricing-check"><svg class="h-5 w-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> API access (sandbox)</li>
                <li class="pricing-check text-slate-400"><svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> No SMS credits included</li>
            </ul>
            <a href="{{ route('login') }}" class="btn-outline w-full justify-center">Sign up free</a>
        </div>

        {{-- Pay as you go (popular) --}}
        <div class="pricing-card popular">
            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                <span class="rounded-full bg-sky-500 px-4 py-1 text-xs font-black text-white shadow-sm shadow-sky-300">Most popular</span>
            </div>
            <div>
                <p class="text-sm font-black uppercase tracking-widest text-sky-600">Pay as you go</p>
                <p class="mt-2 text-4xl font-black text-slate-950">UGX 35<span class="text-lg font-semibold text-slate-500">/SMS</span></p>
                <p class="mt-1 text-sm text-slate-600">Volume discounts available</p>
            </div>
            <ul class="flex flex-col gap-3">
                <li class="pricing-check"><svg class="h-5 w-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Bulk &amp; personalized SMS</li>
                <li class="pricing-check"><svg class="h-5 w-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Scheduled &amp; recurring</li>
                <li class="pricing-check"><svg class="h-5 w-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Delivery reports</li>
                <li class="pricing-check"><svg class="h-5 w-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> API access (live)</li>
                <li class="pricing-check"><svg class="h-5 w-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Me 2 U credit sharing</li>
            </ul>
            <a href="{{ route('login') }}" class="btn-primary w-full justify-center">Get started</a>
        </div>

        {{-- Enterprise --}}
        <div class="pricing-card">
            <div>
                <p class="text-sm font-black uppercase tracking-widest text-slate-500">Enterprise</p>
                <p class="mt-2 text-4xl font-black text-slate-950">Custom</p>
                <p class="mt-1 text-sm text-slate-600">For high-volume senders</p>
            </div>
            <ul class="flex flex-col gap-3">
                <li class="pricing-check"><svg class="h-5 w-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> All Pay-as-you-go features</li>
                <li class="pricing-check"><svg class="h-5 w-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Dedicated sender ID</li>
                <li class="pricing-check"><svg class="h-5 w-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Volume price negotiation</li>
                <li class="pricing-check"><svg class="h-5 w-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Priority support</li>
            </ul>
            <a href="tel:+256702133428" class="btn-outline w-full justify-center">Call us</a>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════
     DEVELOPER API
═══════════════════════════════════════════════════════════════════════ --}}
<section id="developers" class="border-y border-slate-100 bg-slate-950 py-20" aria-labelledby="dev-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="grid min-w-0 items-center gap-12 lg:grid-cols-2">

            <div class="min-w-0">
                <p class="section-eyebrow border-sky-800 bg-sky-950 text-sky-300">Developer API</p>
                <h2 id="dev-heading" class="text-4xl font-black tracking-tight text-white">
                    Plug SMS into <span class="gradient-text">any application</span>
                </h2>
                <p class="mt-4 text-base leading-7 text-slate-400">
                    Connect your website, school management system, billing platform, or mobile app to ShamaSMS using the REST API. Two versions available — V1 with credentials, V2 with API keys (recommended).
                </p>

                <ul class="mt-8 flex flex-col gap-4">
                    <li class="flex items-start gap-3 text-sm text-slate-300">
                        <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-sky-500 text-xs font-black text-white" aria-hidden="true">✓</span>
                        <span><strong class="text-white">API key authentication</strong> — generate keys from your dashboard settings, revoke any time.</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-300">
                        <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-sky-500 text-xs font-black text-white" aria-hidden="true">✓</span>
                        <span><strong class="text-white">Sandbox mode</strong> — test without spending credits. Flip <code class="rounded bg-slate-800 px-1 py-0.5 text-sky-300">"sandbox": true</code> in your request.</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-300">
                        <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-sky-500 text-xs font-black text-white" aria-hidden="true">✓</span>
                        <span><strong class="text-white">Personalization support</strong> — pass per-row data and use placeholders for unique messages at scale.</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-300">
                        <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-sky-500 text-xs font-black text-white" aria-hidden="true">✓</span>
                        <span><strong class="text-white">Simple JSON API</strong> — standard REST, works with any language: PHP, Python, JavaScript, Java, and more.</span>
                    </li>
                </ul>

                <div class="mt-8 flex gap-3">
                    <a href="{{ route('login') }}" class="btn-primary">Get your API key</a>
                </div>
            </div>

            {{-- Code block --}}
            <div class="min-w-0">
                <pre class="landing-code"><code><span class="text-slate-500">POST</span> <span class="text-sky-300">/api/v2/sms/send</span>
<span class="text-slate-500">Authorization:</span> Bearer <span class="text-emerald-400">shama_live_xxxxxxxxxxxxxxxx</span>
<span class="text-slate-500">Content-Type:</span> application/json

{
  <span class="text-sky-300">"sender_id"</span>:    <span class="text-amber-300">"MYSHOP"</span>,
  <span class="text-sky-300">"message"</span>:      <span class="text-amber-300">"Hello &#64;&#64;name&#64;&#64;, your order #&#64;&#64;var1&#64;&#64; is ready."</span>,
  <span class="text-sky-300">"numbers"</span>:      [<span class="text-amber-300">"256700000000"</span>, <span class="text-amber-300">"256712345678"</span>],
  <span class="text-sky-300">"personalized"</span>: <span class="text-violet-300">true</span>,
  <span class="text-sky-300">"data"</span>: [
    { <span class="text-sky-300">"name"</span>: <span class="text-amber-300">"Alice"</span>, <span class="text-sky-300">"var1"</span>: <span class="text-amber-300">"ORD-1042"</span> },
    { <span class="text-sky-300">"name"</span>: <span class="text-amber-300">"Bob"</span>,   <span class="text-sky-300">"var1"</span>: <span class="text-amber-300">"ORD-1043"</span> }
  ],
  <span class="text-sky-300">"sandbox"</span>:      <span class="text-violet-300">false</span>
}

<span class="text-slate-500">// Response</span>
{ <span class="text-sky-300">"status"</span>: <span class="text-amber-300">"sent"</span>, <span class="text-sky-300">"queued"</span>: <span class="text-violet-300">2</span>, <span class="text-sky-300">"credits_used"</span>: <span class="text-violet-300">2</span> }</code></pre>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════
     CTA BAND
═══════════════════════════════════════════════════════════════════════ --}}
<section class="border-y border-slate-100 bg-slate-50 py-16">
 <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
 <p class="section-eyebrow mx-auto mb-3">Support</p>
 <h2 class="text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Need Assistance?</h2>
 <p class="mt-4 text-base leading-7 text-slate-600">Our support team is ready to help you get started, troubleshoot any issue, or answer questions about the API. Reach us by phone or email.</p>
 <div class="mt-8 flex flex-wrap justify-center gap-4">
 <a href="tel:+256702133428" class="inline-flex items-center gap-2 rounded-xl border-2 border-sky-500 bg-sky-500 px-8 py-3.5 text-sm font-black uppercase tracking-widest text-white shadow-md transition hover:bg-sky-600 hover:border-sky-600">
 <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg>
 Talk to Support
 </a>
 <a href="mailto:support@shamasms.com" class="inline-flex items-center gap-2 rounded-xl border-2 border-slate-300 bg-white px-8 py-3.5 text-sm font-black uppercase tracking-widest text-slate-700 shadow-sm transition hover:border-sky-500 hover:text-sky-700">
 <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
 Email Support
 </a>
 </div>
 <p class="mt-5 text-sm text-slate-500">Or call us: <a href="tel:+256702133428" class="font-bold text-sky-600 hover:underline">+256 702 133428</a> &nbsp;&middot;&nbsp; <a href="mailto:support@shamasms.com" class="font-bold text-sky-600 hover:underline">support@shamasms.com</a></p>
 </div>
</section>


<section class="cta-band py-20" aria-labelledby="cta-heading">
    <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
        <h2 id="cta-heading" class="text-4xl font-black tracking-tight text-white sm:text-5xl">
            Start reaching your audience today
        </h2>
        <p class="mt-5 text-lg leading-8 text-sky-100">
            Join businesses, schools, and developers already using ShamaSMS to send faster, smarter, and more personally.
        </p>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-4 text-base font-black text-sky-700 shadow-xl shadow-sky-900/20 transition hover:bg-sky-50 active:scale-95">
                Create free account
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/40 bg-white/10 px-8 py-4 text-base font-bold text-white backdrop-blur transition hover:bg-white/20 active:scale-95">
                Login to dashboard
            </a>
        </div>
        <p class="mt-6 text-sm text-sky-200">No credit card required &nbsp;·&nbsp; Free to set up &nbsp;·&nbsp; Pay only when you send</p>
    </div>
</section>

</main>

{{-- ══════════════════════════════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════════════════════════════ --}}
<footer class="landing-footer" aria-label="Site footer">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">

        <div class="grid gap-10 md:grid-cols-4">

            {{-- Brand --}}
            <div class="md:col-span-2">
                <a href="/" class="flex items-center gap-2.5 font-black text-sky-700">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-sky-500 text-lg text-white">S</span>
                    <span class="text-lg tracking-tight">ShamaSMS</span>
                </a>
                <p class="mt-4 max-w-sm text-sm leading-6 text-slate-600">
                    Reliable bulk SMS for businesses, schools, churches, SACCOs, and developers across Uganda.
                    Fast delivery. Simple pricing. Powerful API.
                </p>
                <p class="mt-4 text-xs text-slate-400">&copy; {{ date('Y') }} ShamaSMS. All rights reserved.</p>
            </div>

            {{-- Product links --}}
            <div>
                <h3 class="mb-4 text-sm font-black uppercase tracking-widest text-slate-800">Product</h3>
                <ul class="flex flex-col gap-2 text-sm text-slate-600">
                    <li><a href="#features"     class="hover:text-sky-600">Features</a></li>
                    <li><a href="#how-it-works" class="hover:text-sky-600">How it works</a></li>
                    <li><a href="#pricing"      class="hover:text-sky-600">Pricing</a></li>
                    <li><a href="{{ route('developers') }}" class="hover:text-sky-600">Developer API</a></li>
                </ul>
            </div>

            {{-- Account links --}}
            <div>
                <h3 class="mb-4 text-sm font-black uppercase tracking-widest text-slate-800">Account</h3>
                <ul class="flex flex-col gap-2 text-sm text-slate-600">
                    <li><a href="{{ route('login') }}" class="hover:text-sky-600">Create account</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-sky-600">Login</a></li>
                </ul>
            </div>

        </div>

    </div>
</footer>

</body>
</html>
