<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>ShamaSMS API Documentation</title>
 @vite(["resources/css/app.css", "resources/js/app.js"])
</head>
<body class="min-h-screen bg-white text-slate-950 antialiased">

{{-- ═══ TOP NAV ═══ --}}
@auth
<nav class="sticky top-0 z-50 border-b border-sky-100 bg-white/95 backdrop-blur-md">
 <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
 <a href="{{ route('compose') }}" class="flex items-center gap-2 font-black text-sky-700">
 <span class="grid h-9 w-9 place-items-center rounded-xl bg-sky-500 text-white">S</span>
 <span>ShamaSMS</span>
 </a>
 <div class="hidden items-center gap-1 text-sm font-semibold md:flex">
 <a class="nav-link @if(request()->routeIs('compose')) active @endif" href="{{ route('compose') }}">Compose</a>
 <a class="nav-link @if(request()->routeIs('sent')) active @endif" href="{{ route('sent') }}">Sent</a>
 <a class="nav-link @if(request()->routeIs('phonebook')) active @endif" href="{{ route('phonebook') }}">Phonebook</a>
 <a class="nav-link @if(request()->routeIs('buy')) active @endif" href="{{ route('buy') }}">Buy</a>
 <a class="nav-link @if(request()->routeIs('me2u')) active @endif" href="{{ route('me2u') }}">Me 2 U</a>
 <a class="nav-link active" href="{{ route('developers') }}">API Docs</a>
 <a class="nav-link @if(request()->routeIs('settings')) active @endif" href="{{ route('settings') }}">Settings</a>
 </div>
 <div class="flex items-center gap-2">
 @auth<div class="rounded-lg border border-sky-100 bg-sky-50 px-3 py-2 text-sm font-bold text-sky-800">Balance {{ number_format(auth()->user()->sms_balance) }}</div>@endauth
 @auth
<form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold hover:bg-slate-100">Logout</button></form>@else<a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Login</a><a href="{{ route('register') }}" class="btn-primary !py-2 !px-4 !text-sm">Get started</a>@endauth
 </div>
 </div>
</nav>
@endauth

<div class="mx-auto flex max-w-7xl gap-0 px-0 lg:gap-8 lg:px-8 lg:py-10">

{{-- ═══ SIDEBAR ═══ --}}
<aside class="doc-sidebar hidden w-56 shrink-0 lg:block" style="position:sticky;top:4.5rem;height:calc(100vh - 5rem);overflow-y:auto">
 <div class="grp">Getting Started</div>
 <a href="#intro">Introduction</a>
 <a href="#authentication">Authentication</a>
 <a href="#sandbox">Sandbox vs Live</a>
 <a href="#numbers">Phone Numbers</a>
 <a href="#errors">Errors</a>
 <div class="grp mt-2">API V2 — Recommended</div>
 <a href="#v2-send">Send SMS</a>
 <a href="#v2-personalized">Personalized SMS</a>
 <a href="#v2-balance">Check Balance</a>
 <div class="grp mt-2">API V1 — Legacy</div>
 <a href="#v1-send">Send SMS</a>
 <a href="#v1-balance">Check Balance</a>
 <div class="grp mt-2">Code Examples</div>
 <a href="#ex-php">PHP</a>
 <a href="#ex-js">JavaScript</a>
 <a href="#ex-python">Python</a>
 <a href="#ex-curl">cURL</a>
</aside>

{{-- ═══ MAIN CONTENT ═══ --}}
<main class="min-w-0 flex-1 px-4 py-8 sm:px-6 lg:px-0 lg:py-0">
<div class="space-y-16">

{{-- INTRO --}}
<section id="intro">
 <div class="panel">
 <div class="flex flex-wrap items-start justify-between gap-4">
 <div>
 <p class="section-eyebrow mb-2">Developer Reference</p>
 <h1 class="page-title">ShamaSMS API Documentation</h1>
 <p class="page-subtitle mt-2">Integrate SMS sending into any website, mobile app, school system, billing platform, or CRM using the ShamaSMS REST API. Two versions are available — V2 with API keys is recommended for all new integrations.</p>
 </div>
 @auth
 <a href="{{ route('settings') }}" class="btn-primary !py-2 !px-5 !text-sm shrink-0">Get API key &rarr;</a>
 @endauth
 </div>
 <div class="mt-6 grid gap-4 sm:grid-cols-3">
 <div class="rounded-xl border border-slate-100 bg-slate-50 p-4"><p class="text-xs font-black uppercase tracking-widest text-slate-500">Base URL</p><p class="mt-1 font-mono text-sm text-sky-700">{{ url("/api") }}</p></div>
 <div class="rounded-xl border border-slate-100 bg-slate-50 p-4"><p class="text-xs font-black uppercase tracking-widest text-slate-500">Format</p><p class="mt-1 font-mono text-sm text-slate-800">JSON (application/json)</p></div>
 <div class="rounded-xl border border-slate-100 bg-slate-50 p-4"><p class="text-xs font-black uppercase tracking-widest text-slate-500">Versions</p><p class="mt-1 font-mono text-sm text-slate-800">v1 (legacy) &nbsp;&middot;&nbsp; v2 (recommended)</p></div>
 </div>
 </div>
</section>

{{-- AUTHENTICATION --}}
<section id="authentication">
 <div class="panel">
 <h2 class="page-title text-2xl">Authentication</h2>
 <p class="page-subtitle mt-1">ShamaSMS supports two authentication methods depending on the API version.</p>
 <div class="mt-5 grid gap-4 md:grid-cols-2">
 <div class="rounded-xl border border-sky-200 bg-sky-50 p-5">
 <div class="mb-2 flex items-center gap-2"><span class="rounded-full bg-sky-500 px-2.5 py-0.5 text-xs font-black text-white">V2 Recommended</span></div>
 <h3 class="font-black text-slate-900">API Key (Bearer Token)</h3>
 <p class="mt-2 text-sm leading-6 text-slate-700">Generate keys in your dashboard Settings. Pass the key in the <code class="inline-code">Authorization</code> header.</p>
 <pre class="code-block mt-3 text-xs"><code>Authorization: Bearer shama_live_xxxxxxxxxxxx</code></pre>
 <p class="mt-3 text-xs text-slate-500">You can also use the <code class="inline-code">X-API-Key</code> header or the <code class="inline-code">api_key</code> body field, but Bearer is preferred.</p>
 </div>
 <div class="rounded-xl border border-slate-200 bg-white p-5">
 <div class="mb-2 flex items-center gap-2"><span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-black text-slate-600">V1 Legacy</span></div>
 <h3 class="font-black text-slate-900">Email &amp; Password</h3>
 <p class="mt-2 text-sm leading-6 text-slate-700">Pass your account email and password in the request body. Works but not recommended — anyone with the credentials can send on your behalf.</p>
 <pre class="code-block mt-3 text-xs"><code>{ "email": "you@example.com", "password": "..." }</code></pre>
 </div>
 </div>
 </div>
</section>

{{-- SANDBOX --}}
<section id="sandbox">
 <div class="panel">
 <h2 class="page-title text-2xl">Sandbox vs Live</h2>
 <p class="page-subtitle mt-1">Every API key has a mode: <strong>sandbox</strong> or <strong>live</strong>. You can also force sandbox per-request by passing <code class="inline-code">"sandbox": true</code> in the body.</p>
 <div class="mt-5 grid gap-4 md:grid-cols-2">
 <div class="rounded-xl border border-amber-200 bg-amber-50 p-5"><h3 class="font-black text-amber-900">&#128247; Sandbox key / sandbox:true</h3><ul class="mt-3 space-y-1.5 text-sm text-amber-800"><li>&#10003; Returns <code class="inline-code">202 Accepted</code> response</li><li>&#10003; Stores message records with status <em>sandbox</em></li><li>&#10003; Does NOT send real SMS</li><li>&#10003; Does NOT deduct credits from balance</li><li>&#10003; Safe for development and testing</li></ul></div>
 <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5"><h3 class="font-black text-emerald-900">&#9889; Live key</h3><ul class="mt-3 space-y-1.5 text-sm text-emerald-800"><li>&#10003; Sends real SMS through the ShamaSMS network</li><li>&#10003; Deducts credits from your account balance</li><li>&#10003; Delivery reports recorded per recipient</li><li>&#10003; Full message history stored in Sent</li></ul></div>
 </div>
 <p class="mt-4 text-sm text-slate-600">&#128161; Always build and test with a sandbox key first. Switch to a live key only when you are ready for production.</p>
 </div>
</section>

{{-- PHONE NUMBERS --}}
<section id="numbers">
 <div class="panel">
 <h2 class="page-title text-2xl">Phone Number Format</h2>
 <p class="page-subtitle mt-1">All recipients must be valid Ugandan mobile numbers. ShamaSMS normalises local and international formats automatically before delivery.</p>
 <div class="mt-5 grid gap-3 sm:grid-cols-3">
 <div class="rounded-xl border border-slate-200 bg-white p-4 text-center"><p class="text-xs font-black uppercase tracking-widest text-slate-400">Local format</p><p class="mt-2 font-mono text-lg font-bold text-slate-800">0700123456</p><span class="inline-block mt-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700">&#10003; Accepted</span></div>
 <div class="rounded-xl border border-slate-200 bg-white p-4 text-center"><p class="text-xs font-black uppercase tracking-widest text-slate-400">International</p><p class="mt-2 font-mono text-lg font-bold text-slate-800">256700123456</p><span class="inline-block mt-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700">&#10003; Accepted</span></div>
 <div class="rounded-xl border border-slate-200 bg-white p-4 text-center"><p class="text-xs font-black uppercase tracking-widest text-slate-400">E.164</p><p class="mt-2 font-mono text-lg font-bold text-slate-800">+256700123456</p><span class="inline-block mt-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700">&#10003; Accepted</span></div>
 </div>
 <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-4">
 <p class="text-sm font-black text-slate-700 mb-2">Supported Uganda prefixes</p>
 <div class="flex flex-wrap gap-2 text-xs font-mono">
 <span class="rounded bg-white border border-slate-200 px-2 py-1">Airtel: 70, 74, 75</span>
 <span class="rounded bg-white border border-slate-200 px-2 py-1">MTN: 76, 77, 78, 79</span>
 <span class="rounded bg-white border border-slate-200 px-2 py-1">Other: 71, 72, 73, 39, 20</span>
 </div>
 </div>
 </div>
</section>

{{-- ERRORS --}}
<section id="errors">
 <div class="panel">
 <h2 class="page-title text-2xl">HTTP Status Codes &amp; Errors</h2>
 <p class="page-subtitle mt-1">All error responses return JSON with a <code class="inline-code">message</code> key describing the problem.</p>
 <div class="mt-5 overflow-x-auto">
 <table class="table">
 <thead><tr><th>Status</th><th>Meaning</th><th>Common cause</th><th>Fix</th></tr></thead>
 <tbody>
 <tr><td><span class="status-pill bg-emerald-50 text-emerald-800">202</span></td><td>Accepted</td><td>Request processed successfully</td><td>Check <code class="inline-code">sent_count</code> and <code class="inline-code">failed_count</code> in response</td></tr>
 <tr><td><span class="status-pill bg-red-50 text-red-800">401</span></td><td>Unauthorized</td><td>Bad API key or wrong email/password</td><td>Regenerate your key in Settings or check your credentials</td></tr>
 <tr><td><span class="status-pill bg-amber-50 text-amber-800">422</span></td><td>Unprocessable</td><td>Validation failed or insufficient balance</td><td>Check required fields, valid numbers, and your credit balance</td></tr>
 <tr><td><span class="status-pill bg-slate-100 text-slate-700">500</span></td><td>Server Error</td><td>Unexpected server-side problem</td><td>Retry after a few seconds; contact support if persistent</td></tr>
 </tbody>
 </table>
 </div>
 <pre class="code-block mt-5"><code>// Error response example
{
 "message": "Insufficient SMS balance."
}</code></pre>
 </div>
</section>

{{-- V2 SEND --}}
<section id="v2-send">
 <div class="panel">
 <div class="flex items-center gap-2 mb-1"><span class="rounded-full bg-sky-500 px-2.5 py-0.5 text-xs font-black text-white">V2</span><h2 class="page-title text-2xl">Send SMS</h2></div>
 <p class="page-subtitle">Send a message to one or many recipients at once.</p>
 <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4 flex flex-wrap items-center gap-3">
 <span class="badge-post font-black text-xs">POST</span>
 <code class="font-mono text-sm text-sky-700">{{ url("/api/v2/sms/send") }}</code>
 <span class="ml-auto rounded-full bg-sky-50 px-3 py-0.5 text-xs font-bold text-sky-700">Requires API Key</span>
 </div>
 <div class="mt-5 grid gap-6 lg:grid-cols-2">
 <div>
 <p class="label mb-3">Request Headers</p>
 <pre class="code-block"><code>Content-Type: application/json
Authorization: Bearer YOUR_API_KEY</code></pre>
 <p class="label mt-5 mb-3">Request Parameters</p>
 <div class="overflow-x-auto">
 <table class="table text-xs">
 <thead><tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
 <tbody>
 <tr><td class="font-mono text-sky-700">sender_id</td><td><span class="inline-code">string</span></td><td><span class="rounded bg-slate-100 px-1 text-slate-500 text-xs">optional</span></td><td>Sender name shown on recipient phone. Max 32 chars.</td></tr>
 <tr><td class="font-mono text-sky-700">message</td><td><span class="inline-code">string</span></td><td><span class="rounded bg-red-50 px-1 text-red-600 text-xs font-bold">required*</span></td><td>The message text. Max 765 chars (5 SMS segments). Use <code class="inline-code">message_body</code> as alias.</td></tr>
 <tr><td class="font-mono text-sky-700">numbers</td><td><span class="inline-code">array|string</span></td><td><span class="rounded bg-red-50 px-1 text-red-600 text-xs font-bold">required*</span></td><td>Phone numbers. Can be array, comma-separated string, or use <code class="inline-code">recipients</code> instead.</td></tr>
 <tr><td class="font-mono text-sky-700">recipients</td><td><span class="inline-code">array</span></td><td><span class="rounded bg-red-50 px-1 text-red-600 text-xs font-bold">required*</span></td><td>Array of objects with <code class="inline-code">phone</code> key. Use this for personalized SMS.</td></tr>
 <tr><td class="font-mono text-sky-700">personalized</td><td><span class="inline-code">boolean</span></td><td><span class="rounded bg-slate-100 px-1 text-slate-500 text-xs">optional</span></td><td>Set <code class="inline-code">true</code> to render placeholders per recipient.</td></tr>
 <tr><td class="font-mono text-sky-700">sandbox</td><td><span class="inline-code">boolean</span></td><td><span class="rounded bg-slate-100 px-1 text-slate-500 text-xs">optional</span></td><td>Force sandbox mode for this request regardless of key mode.</td></tr>
 </tbody>
 </table>
 </div>
 <p class="mt-2 text-xs text-slate-500">* Either <code class="inline-code">numbers</code>, <code class="inline-code">to</code>, <code class="inline-code">phone</code>, or <code class="inline-code">recipients</code> is required.</p>
 </div>
 <div>
 <p class="label mb-3">Request Body Example</p>
 <pre class="code-block"><code>{
 "sender_id": "MYSHOP",
 "message": "Hello! Your order is ready for pickup.",
 "numbers": ["0700000000", "256750000001"],
 "sandbox": true
}</code></pre>
 <p class="label mt-5 mb-3">Success Response <span class="status-pill ml-1">202</span></p>
 <pre class="code-block"><code>{
 "message": "Accepted",
 "reference": "api-xxxxxxxx-xxxx-xxxx",
 "segments": 1,
 "recipient_count": 2,
 "sent_count": 2,
 "failed_count": 0,
 "credits_used": 2,
 "balance": 998
}</code></pre>
 </div>
 </div>
 </div>
</section>

{{-- V2 PERSONALIZED --}}
<section id="v2-personalized">
 <div class="panel">
 <div class="flex items-center gap-2 mb-1"><span class="rounded-full bg-sky-500 px-2.5 py-0.5 text-xs font-black text-white">V2</span><h2 class="page-title text-2xl">Personalized SMS</h2></div>
 <p class="page-subtitle">Inject a unique name, balance, amount, or any value per recipient using placeholders in your message template.</p>
 <div class="mt-5 rounded-xl border border-slate-100 bg-slate-50 p-4">
 <p class="text-sm font-black text-slate-700 mb-3">Available Placeholders</p>
 <div class="flex flex-wrap gap-2">
 <code class="inline-code">@@name@@</code>
 <code class="inline-code">@@var1@@</code>
 <code class="inline-code">@@var2@@</code>
 <code class="inline-code">@@var3@@</code>
 <code class="inline-code">@@var4@@</code>
 <code class="inline-code">@@var5@@</code>
 </div>
 <p class="mt-3 text-sm text-slate-600">Each recipient row in <code class="inline-code">recipients</code> supplies the values. If a placeholder has no value, it is left blank (not an error).</p>
 </div>
 <div class="mt-5 grid gap-6 lg:grid-cols-2">
 <div>
 <p class="label mb-3">Request Body — Personalized</p>
 <pre class="code-block"><code>POST {{ url("/api/v2/sms/send") }}
Authorization: Bearer YOUR_API_KEY

{
 "message": "Hi @@name@@, your loan balance is UGX @@var1@@. Due @@var2@@.",
 "personalized": true,
 "recipients": [
 { "phone": "0700000000", "name": "Alice", "var1": "450,000", "var2": "10 Jul" },
 { "phone": "0750000001", "name": "Robert", "var1": "820,000", "var2": "15 Jul" }
 ],
 "sandbox": true
}</code></pre>
 </div>
 <div>
 <p class="label mb-3">What each recipient receives</p>
 <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-3 text-sm">
 <div class="rounded-lg border border-sky-100 bg-sky-50 p-3"><p class="font-black text-sky-900 text-xs mb-1">0700000000 (Alice)</p><p class="text-slate-700">"Hi Alice, your loan balance is UGX 450,000. Due 10 Jul."</p></div>
 <div class="rounded-lg border border-sky-100 bg-sky-50 p-3"><p class="font-black text-sky-900 text-xs mb-1">0750000001 (Robert)</p><p class="text-slate-700">"Hi Robert, your loan balance is UGX 820,000. Due 15 Jul."</p></div>
 </div>
 <p class="mt-4 text-xs text-slate-500">Each recipient gets a fully personalized message. Credits are consumed per segment per recipient.</p>
 </div>
 </div>
 </div>
</section>

{{-- V2 BALANCE --}}
<section id="v2-balance">
 <div class="panel">
 <div class="flex items-center gap-2 mb-1"><span class="rounded-full bg-sky-500 px-2.5 py-0.5 text-xs font-black text-white">V2</span><h2 class="page-title text-2xl">Check Balance</h2></div>
 <p class="page-subtitle">Retrieve the SMS credit balance for the account linked to the API key.</p>
 <div class="mt-5 grid gap-6 lg:grid-cols-2">
 <div>
 <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 flex flex-wrap items-center gap-3 mb-5">
 <span class="inline-flex rounded px-2 py-0.5 text-xs font-black bg-sky-100 text-sky-800">GET</span>
 <code class="font-mono text-sm text-sky-700">{{ url("/api/v2/balance") }}</code>
 </div>
 <p class="label mb-2">Headers</p>
 <pre class="code-block"><code>Authorization: Bearer YOUR_API_KEY</code></pre>
 </div>
 <div>
 <p class="label mb-3">Success Response <span class="status-pill ml-1">200</span></p>
 <pre class="code-block"><code>{
 "balance": 1450,
 "mode": "live",
 "unit": "sms_credits",
 "sms_unit_price": 65,
 "currency": "UGX"
}</code></pre>
 </div>
 </div>
 </div>
</section>

{{-- V1 SEND --}}
<section id="v1-send">
 <div class="panel">
 <div class="flex items-center gap-2 mb-1"><span class="rounded-full bg-slate-400 px-2.5 py-0.5 text-xs font-black text-white">V1 Legacy</span><h2 class="page-title text-2xl">Send SMS</h2></div>
 <p class="page-subtitle">Authenticates using your account email and password directly in the request body. Supports the same personalization and bulk features as V2.</p>
 <div class="mt-5 grid gap-6 lg:grid-cols-2">
 <div>
 <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 flex flex-wrap items-center gap-3 mb-5">
 <span class="badge-post">POST</span>
 <code class="font-mono text-sm text-sky-700">{{ url("/api/v1/sms/send") }}</code>
 </div>
 <p class="label mb-3">Request Body</p>
 <pre class="code-block"><code>{
 "email": "you@example.com",
 "password": "your-account-password",
 "sender_id": "SHAMA",
 "message": "Hello from ShamaSMS!",
 "numbers": ["0700000000", "0750000001"],
 "sandbox": true
}</code></pre>
 </div>
 <div>
 <p class="label mb-3">Personalized V1 Request</p>
 <pre class="code-block"><code>{
 "email": "you@example.com",
 "password": "...",
 "message": "Hi @@name@@, balance: @@var1@@",
 "personalized": true,
 "recipients": [
 { "phone": "0700000000", "name": "Jane", "var1": "50,000" }
 ],
 "sandbox": true
}</code></pre>
 <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800"><strong>&#9888; Security note:</strong> V1 sends your login password over the API. Use V2 with API keys for better security — keys can be revoked without changing your password.</div>
 </div>
 </div>
 </div>
</section>

{{-- V1 BALANCE --}}
<section id="v1-balance">
 <div class="panel">
 <div class="flex items-center gap-2 mb-1"><span class="rounded-full bg-slate-400 px-2.5 py-0.5 text-xs font-black text-white">V1 Legacy</span><h2 class="page-title text-2xl">Check Balance</h2></div>
 <p class="page-subtitle">Returns the SMS credit balance for the authenticated account.</p>
 <div class="mt-5 grid gap-6 lg:grid-cols-2">
 <div>
 <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 flex flex-wrap items-center gap-3 mb-5"><span class="badge-post">POST</span><code class="font-mono text-sm text-sky-700">{{ url("/api/v1/balance") }}</code></div>
 <pre class="code-block"><code>{ "email": "you@example.com", "password": "..." }</code></pre>
 </div>
 <div>
 <p class="label mb-3">Response</p>
 <pre class="code-block"><code>{
 "balance": 1450,
 "unit": "sms_credits",
 "sms_unit_price": 65,
 "currency": "UGX"
}</code></pre>
 </div>
 </div>
 </div>
</section>

{{-- CODE EXAMPLES --}}
<section id="ex-php">
 <div class="panel">
 <h2 class="page-title text-2xl">Code Examples</h2>
 <p class="page-subtitle mt-1">Copy-paste examples in common languages. Replace <code class="inline-code">YOUR_API_KEY</code> with your live or sandbox key from Settings.</p>
 <div class="mt-6 space-y-8">
 {{-- PHP --}}
 <div id="ex-php-block">
 <div class="flex items-center justify-between mb-2"><p class="label">PHP (cURL)</p></div>
 <pre class="code-block"><code>&lt;?php
$apiKey = "YOUR_API_KEY";
$payload = json_encode([
 "sender_id" => "MYAPP",
 "message" => "Hello @@name@@, your balance is @@var1@@.",
 "personalized"=> true,
 "recipients" => [
 ["phone" => "0700000000", "name" => "Alice", "var1" => "35,000"],
 ["phone" => "0750000001", "name" => "Bob", "var1" => "12,000"],
 ],
 "sandbox" => false,
]);
$ch = curl_init("https://shamasms.com/api/v2/sms/send");
curl_setopt_array($ch, [
 CURLOPT_RETURNTRANSFER => true,
 CURLOPT_POST => true,
 CURLOPT_POSTFIELDS => $payload,
 CURLOPT_HTTPHEADER => [
 "Content-Type: application/json",
 "Authorization: Bearer " . $apiKey,
 ],
]);
$response = json_decode(curl_exec($ch), true);
curl_close($ch);
echo "Sent: " . $response["sent_count"] . ", Credits left: " . $response["balance"];</code></pre>
 </div>

 {{-- JavaScript --}}
 <div id="ex-js-block">
 <p class="label mb-2">JavaScript (fetch)</p>
 <pre class="code-block"><code>const API_KEY = "YOUR_API_KEY";

const response = await fetch("https://shamasms.com/api/v2/sms/send", {
 method: "POST",
 headers: {
 "Content-Type": "application/json",
 "Authorization": `Bearer ${API_KEY}`,
 },
 body: JSON.stringify({
 sender_id: "MYAPP",
 message: "Hello @@name@@, your order @@var1@@ is ready!",
 personalized: true,
 recipients: [
 { phone: "0700000000", name: "Alice", var1: "ORD-1042" },
 ],
 sandbox: false,
 }),
});

const data = await response.json();
console.log(`Sent: ${data.sent_count}, Balance: ${data.balance}`);</code></pre>
 </div>

 {{-- Python --}}
 <div id="ex-python-block">
 <p class="label mb-2">Python (requests)</p>
 <pre class="code-block"><code>import requests

API_KEY = "YOUR_API_KEY"

resp = requests.post(
 "https://shamasms.com/api/v2/sms/send",
 headers={"Authorization": f"Bearer {API_KEY}"},
 json={
 "sender_id": "MYAPP",
 "message": "Hi @@name@@, your invoice of UGX @@var1@@ is due.",
 "personalized": True,
 "recipients": [
 {"phone": "0700000000", "name": "Alice", "var1": "150,000"},
 {"phone": "0750000001", "name": "Bob", "var1": "80,000"},
 ],
 "sandbox": False,
 }
)

data = resp.json()
print(f"Sent: {data['sent_count']}, Balance: {data['balance']}")</code></pre>
 </div>

 {{-- cURL --}}
 <div id="ex-curl-block">
 <p class="label mb-2">cURL (command line)</p>
 <pre class="code-block"><code># Simple bulk send
curl -X POST https://shamasms.com/api/v2/sms/send \
 -H "Content-Type: application/json" \
 -H "Authorization: Bearer YOUR_API_KEY" \
 -d '{
 "sender_id": "SHAMA",
 "message": "Test message from ShamaSMS API.",
 "numbers": ["0700000000"],
 "sandbox": true
 }'

# Check balance
curl https://shamasms.com/api/v2/balance \
 -H "Authorization: Bearer YOUR_API_KEY"</code></pre>
 </div>
 </div>
 </div>
</section>

{{-- CREDITS EXPLAINER --}}
<section id="credits">
 <div class="panel">
 <h2 class="page-title text-2xl">Credits &amp; SMS Segments</h2>
 <p class="page-subtitle mt-1">Understand how credits are calculated so you can estimate costs accurately.</p>
 <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
 <div class="rounded-xl border border-slate-100 bg-slate-50 p-4"><p class="text-xs font-black uppercase tracking-widest text-slate-400">1 credit</p><p class="mt-1 text-sm text-slate-700">= 1 SMS segment delivered to 1 recipient</p></div>
 <div class="rounded-xl border border-slate-100 bg-slate-50 p-4"><p class="text-xs font-black uppercase tracking-widest text-slate-400">1 segment</p><p class="mt-1 text-sm text-slate-700">= 160 characters (GSM-7) or 70 characters (Unicode)</p></div>
 <div class="rounded-xl border border-slate-100 bg-slate-50 p-4"><p class="text-xs font-black uppercase tracking-widest text-slate-400">2 segments</p><p class="mt-1 text-sm text-slate-700">= 161–306 chars. Counts as 2 credits per recipient.</p></div>
 <div class="rounded-xl border border-slate-100 bg-slate-50 p-4"><p class="text-xs font-black uppercase tracking-widest text-slate-400">Formula</p><p class="mt-1 font-mono text-sm text-sky-700">credits = segments &times; recipients</p></div>
 </div>
 <div class="mt-4 rounded-xl border border-sky-100 bg-sky-50 p-4 text-sm text-sky-900">
 <strong>Example:</strong> A 200-character message sent to 500 recipients = 2 segments &times; 500 = <strong>1,000 credits</strong> used.
 </div>
 </div>
</section>

{{-- FOOTER CTA --}}
<section>
 <div class="cta-band rounded-2xl px-8 py-10 text-center">
 <h2 class="text-3xl font-black text-white">Ready to start sending?</h2>
 <p class="mt-3 text-sky-100">Create a free account, generate an API key in Settings, and send your first message in minutes.</p>
 <div class="mt-6 flex flex-wrap justify-center gap-4">
 @guest
 <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-3.5 text-base font-black text-sky-700 shadow-lg hover:bg-sky-50">Create free account &rarr;</a>
 @else
 <a href="{{ route('settings') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-3.5 text-base font-black text-sky-700 shadow-lg hover:bg-sky-50">Get your API key &rarr;</a>
 @endguest
 </div>
 </div>
</section>

</div>{{-- /space-y-16 --}}
</main>
</div>{{-- /flex container --}}

<script>
// Sidebar active link on scroll
document.addEventListener("DOMContentLoaded",function(){
 var links = document.querySelectorAll(".doc-sidebar a[href^=\"#\"]");
 var sections = Array.from(links).map(function(l){return document.querySelector(l.getAttribute("href"));}).filter(Boolean);
 function onScroll(){
 var scrollY = window.scrollY + 100;
 var current = sections[0];
 sections.forEach(function(s){if(s.offsetTop <= scrollY) current = s;});
 links.forEach(function(l){
 l.classList.toggle("is-active", l.getAttribute("href") === "#"+current.id);
 });
 }
 window.addEventListener("scroll", onScroll, {passive:true});
 onScroll();
});
</script>

</body>
</html>
