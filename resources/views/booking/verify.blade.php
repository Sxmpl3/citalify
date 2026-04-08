<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Reserva | {{ $business->business_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewbox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-slate-900">Verifica tu email</h1>
                <p class="text-slate-500 mt-2">Hemos enviado un código a <strong>{{ $pendingBooking->customer_email }}</strong></p>
            </div>

            <form action="{{ route('booking.verify.post', [$business->business_slug, $pendingBooking->id]) }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="code" class="block text-sm font-semibold text-slate-700 mb-2 text-center">Código de 6 dígitos</label>
                    <input type="text" name="code" id="code" maxlength="6" autofocus required
                        class="w-full text-center text-3xl tracking-[0.5em] font-bold border-2 border-slate-200 rounded-xl py-4 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 focus:outline-none transition-all uppercase pl-[0.25em]"
                        placeholder="000000">
                    
                    @error('code')
                        <p class="text-red-500 text-sm mt-2 text-center font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-200 transition-all flex items-center justify-center gap-2">
                    Confirmar Reserva
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </button>
            </form>

            <div class="mt-8 p-4 bg-amber-50 rounded-xl border border-amber-100 flex items-start gap-3">
                <div class="shrink-0 text-amber-500 mt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <p class="text-amber-800 text-sm font-medium leading-normal">
                    ¿No recibes el código? Si no lo encuentras en unos segundos, revisa tu carpeta de <strong>correo no deseado</strong>.
                </p>
            </div>
        </div>
        
        <div class="bg-slate-50 px-8 py-4 text-center">
            <p class="text-slate-400 text-xs uppercase tracking-wider font-bold">Reserva segura a través de Citalify</p>
        </div>
    </div>
</body>
</html>
