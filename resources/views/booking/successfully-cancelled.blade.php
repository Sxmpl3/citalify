<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cita Cancelada — Citalify</title>
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

    <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </div>

    <h1 class="font-display font-bold text-2xl text-slate-800 mb-2">Cita cancelada correctamente</h1>
    <p class="text-slate-500 max-w-sm mx-auto mb-8">
        Tu cita para el <strong>{{ \Carbon\Carbon::parse($booking->starts_at)->timezone($booking->employee->user->timezone ?? 'Europe/Madrid')->translatedFormat('d \d\e F') }}</strong> ha sido anulada. El negocio ha sido notificado.
    </p>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-center mb-8">
        <p class="text-slate-600">Si deseas volver a reservar, puedes hacerlo desde la página de reservas del negocio.</p>
    </div>

    <p class="text-center text-xs text-slate-300 mt-12">
        Reservas gestionadas por <a href="/" class="text-emerald-500 font-medium hover:text-emerald-700">Citalify</a>
    </p>

</main>

</body>
</html>
