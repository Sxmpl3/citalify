<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }} — @yield('title', 'Accede a tu cuenta')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700,800|outfit:600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4 bg-gradient-to-br from-slate-50 via-emerald-50/30 to-slate-50">
            <a href="/" class="flex items-center gap-2.5 group">
                <img src="{{ asset('img/logo.png') }}" alt="Citalify Logo" class="h-9 w-auto rounded-xl">
                <span class="text-xl font-display font-bold text-slate-800">citalify</span>
            </a>
            <p class="mt-2 text-sm text-slate-500">Gestión de citas para tu negocio</p>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

