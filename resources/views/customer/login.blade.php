<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Reservas — Citalify</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700,800|outfit:600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-favicons />
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2 mb-6">
                <img src="{{ asset('img/logo.png') }}" alt="Citalify Logo" class="h-10 w-auto rounded-xl">
                <span class="text-xl font-display font-bold text-slate-800">citalify</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900">Gestionar mis citas</h1>
            <p class="text-slate-500 mt-2">Dinos tu email y te enviaremos un código de acceso.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
            @if(session('error'))
                <div class="mb-5 p-3 bg-red-50 border border-red-100 rounded-xl text-sm text-red-600 font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('customer.login.post') }}">
                @csrf
                <div class="mb-6">
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Tu correo electrónico</label>
                    <input type="email" name="email" id="email" required placeholder="tu@email.com"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-3">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-200 transition-all flex items-center justify-center gap-2">
                    Enviar código de acceso
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400 mt-8">
            ¿Solo quieres reservar una cita? <a href="javascript:history.back()" class="text-emerald-600 font-medium hover:underline">Volver atrás</a>
        </p>
    </div>

</body>
</html>
