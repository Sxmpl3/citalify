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
                <span class="w-9 h-9 rounded-xl flex items-center justify-center bg-gradient-to-br from-emerald-600 to-emerald-700 transition-transform group-hover:scale-105">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
                <span class="text-xl font-display font-bold text-slate-800">citalia</span>
            </a>
            <p class="mt-2 text-sm text-slate-500">Gestión de citas para tu negocio</p>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

