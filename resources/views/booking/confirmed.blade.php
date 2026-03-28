<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reserva confirmada — {{ $business->business_name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700,800|outfit:600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen">

<header class="bg-white/95 backdrop-blur-sm border-b border-slate-100 shadow-sm">
    <div class="max-w-lg mx-auto px-4 py-4 flex items-center gap-3">
        <span class="w-9 h-9 rounded-xl flex items-center justify-center bg-gradient-to-br from-emerald-600 to-emerald-700 shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </span>
        <p class="font-display font-bold text-slate-800">{{ $business->business_name }}</p>
    </div>
</header>

<main class="max-w-lg mx-auto px-4 py-16 text-center">

    {{-- Icono de éxito --}}
    <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="font-display font-bold text-2xl text-slate-800 mb-2">¡Reserva recibida!</h1>
    <p class="text-slate-500 max-w-sm mx-auto mb-8">
        Tu solicitud ha sido enviada a <span class="font-semibold text-slate-700">{{ $business->business_name }}</span>.
        Te contactarán para confirmar la cita.
    </p>

    @if($business->phone)
        <p class="text-sm text-slate-400 mb-2">¿Necesitas contactar con el negocio?</p>
        <a href="tel:{{ $business->phone }}"
           class="inline-flex items-center gap-2 text-emerald-600 font-semibold hover:text-emerald-800 transition-colors mb-8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            {{ $business->phone }}
        </a>
    @endif

    <div>
        <a href="{{ route('booking.show', $business->business_slug) }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:border-emerald-400 hover:bg-slate-50 transition-colors text-sm">
            Hacer otra reserva
        </a>
    </div>

    <p class="text-center text-xs text-slate-300 mt-12">
        Reservas gestionadas por <a href="/" class="text-emerald-500 font-medium hover:text-emerald-700">citalia</a>
    </p>

</main>

</body>
</html>

