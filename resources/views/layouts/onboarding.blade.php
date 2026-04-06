<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Configurar tu negocio — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700,800|outfit:600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50">

<div class="min-h-screen flex flex-col">

    {{-- Header --}}
    <header class="bg-white/95 backdrop-blur-sm border-b border-slate-100 shadow-sm">
        <div class="max-w-2xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <img src="{{ asset('img/logo.png') }}" alt="Citalify Logo" class="h-9 w-auto rounded-xl">
                <span class="text-lg font-display font-bold text-slate-800">citalify</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-slate-500 hover:text-emerald-600 font-medium transition-colors">Salir</button>
            </form>
        </div>
    </header>

    {{-- Pasos --}}
    <div class="max-w-2xl mx-auto w-full px-4 pt-8">
        <div class="flex items-center gap-2 mb-8">
            @foreach([1 => 'Tu negocio', 2 => 'Servicios', 3 => 'Horarios'] as $n => $label)
                <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold
                            {{ $currentStep == $n ? 'bg-emerald-600 text-white' : ($currentStep > $n ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-500') }}">
                            {{ $currentStep > $n ? '✓' : $n }}
                        </div>
                        <span class="text-sm font-medium {{ $currentStep == $n ? 'text-emerald-600' : ($currentStep > $n ? 'text-emerald-600' : 'text-slate-400') }} hidden sm:inline">
                            {{ $label }}
                        </span>
                    </div>
                    @if (!$loop->last)
                        <div class="flex-1 h-px mx-3 {{ $currentStep > $n ? 'bg-emerald-300' : 'bg-slate-200' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Contenido --}}
    <main class="flex-1 max-w-2xl mx-auto w-full px-4 pb-12">
        {{ $slot }}
    </main>
</div>

</body>
</html>

