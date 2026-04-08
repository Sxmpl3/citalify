<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Acceso — Citalify</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700,800|outfit:600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-favicons />
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewbox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900">Verifica tu email</h1>
            <p class="text-slate-500 mt-2">Hemos enviado un código a <strong>{{ $email }}</strong></p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
            <form method="POST" action="{{ route('customer.verify.post') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                
                <div class="mb-6">
                    <label for="code" class="block text-sm font-semibold text-slate-700 mb-3 text-center">Introduce el código de 6 dígitos</label>
                    <input type="text" name="code" id="code" maxlength="6" autofocus required
                           class="w-full text-center text-3xl tracking-[0.25em] font-bold border-2 border-slate-200 rounded-xl py-4 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 focus:outline-none transition-all uppercase pl-[0.25em]"
                           placeholder="000000">
                    @error('code')
                        <p class="mt-2 text-sm text-red-500 text-center font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-200 transition-all flex items-center justify-center gap-2">
                    Verificar y acceder
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('customer.login') }}" class="text-sm text-slate-400 hover:text-emerald-600 font-medium transition-colors">
                    ¿No es tu correo? Cambiar
                </a>
            </div>
        </div>
    </div>

</body>
</html>
