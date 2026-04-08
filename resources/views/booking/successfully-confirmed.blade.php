<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cita Confirmada — Citalify</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700,800|outfit:600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-favicons />
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen">

<header class="bg-white/95 backdrop-blur-sm border-b border-slate-100 shadow-sm">
    <div class="max-w-lg mx-auto px-4 py-4 flex items-center gap-3">
        <img src="{{ asset('img/logo.png') }}" alt="Citalify Logo" class="h-9 w-auto rounded-xl">
        <p class="font-display font-bold text-slate-800">{{ $booking->employee->user->business_name }}</p>
    </div>
</header>

<main class="max-w-lg mx-auto px-4 py-16 text-center">

    <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="font-display font-bold text-2xl text-slate-800 mb-2">¡Asistencia confirmada!</h1>
    <p class="text-slate-500 max-w-sm mx-auto mb-8">
        Muchas gracias por confirmar tu cita, {{ explode(' ', $booking->customer_name)[0] }}. 
        Te esperamos el <strong>{{ \Carbon\Carbon::parse($booking->starts_at)->timezone($booking->employee->user->timezone ?? 'Europe/Madrid')->translatedFormat('d \d\e F') }}</strong> a las <strong>{{ \Carbon\Carbon::parse($booking->starts_at)->timezone($booking->employee->user->timezone ?? 'Europe/Madrid')->format('H:i') }}</strong> para tu servicio de {{ $booking->service->name }}.
    </p>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-left mb-8">
        <p class="text-sm text-slate-400 mb-1">Negocio</p>
        <p class="font-semibold text-slate-800 mb-4">{{ $booking->employee->user->business_name }}</p>
        
        <p class="text-sm text-slate-400 mb-1">Localización</p>
        <p class="font-semibold text-slate-800">{{ $booking->employee->user->address ?: 'Consultar directamente con el negocio' }}</p>
    </div>

    @if($booking->employee->user->phone)
        <p class="text-sm text-slate-400 mb-2">¿Necesitas contactar con el negocio?</p>
        <a href="tel:{{ $booking->employee->user->phone }}"
           class="inline-flex items-center gap-2 text-emerald-600 font-semibold hover:text-emerald-800 transition-colors mb-8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            {{ $booking->employee->user->phone }}
        </a>
    @endif

    <p class="text-center text-xs text-slate-300 mt-12">
        Reservas gestionadas por <a href="/" class="text-emerald-500 font-medium hover:text-emerald-700">Citalify</a>
    </p>

</main>

</body>
</html>
