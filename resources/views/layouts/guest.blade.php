<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>{{ config('app.name', 'ShamaSMS') }}</title>
 @vite(['resources/css/app.css', 'resources/js/app.js'])
 @livewireStyles
</head>
<body class="min-h-screen text-slate-950 antialiased" style="background-image:url(/images/login-bg.jpg);background-size:cover;background-position:center;background-attachment:fixed;">
 <div class="min-h-screen backdrop-blur-sm" style="background:rgba(3,105,161,0.55);">
 {{ $slot }}
 </div>
 @livewireScripts
</body>
</html>
