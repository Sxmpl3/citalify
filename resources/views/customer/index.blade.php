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
<body class="font-sans antialiased bg-slate-50 min-h-screen">

    {{-- Header --}}
    <header class="bg-white/95 backdrop-blur-sm border-b border-slate-100 shadow-sm sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <img src="{{ asset('img/logo.png') }}" alt="Citalify Logo" class="h-9 w-auto rounded-xl">
                <span class="text-lg font-display font-bold text-slate-800">citalify</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right mr-2">
                    <p class="text-xs text-slate-400 font-medium">Identificado como</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $email }}</p>
                </div>
                <form action="{{ route('customer.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-slate-400 hover:text-red-500 transition-colors uppercase tracking-wider">Salir</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
        
        <div class="flex items-end justify-between mb-8">
            <div>
                <h1 class="text-3xl font-display font-bold text-slate-900 leading-tight">Mis Citas</h1>
                <p class="text-slate-500 mt-1">Todas tus reservas gestionadas a través de Citalify.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm text-emerald-700 font-medium flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if($bookings->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-100 border-dashed p-12 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800">No tienes citas aún</h3>
                <p class="text-slate-400 mt-2">Cuando reserves en cualquier negocio de la plataforma, aparecerán aquí.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($bookings as $booking)
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                        
                        {{-- Badge Estado --}}
                        <div class="absolute top-0 right-0 px-4 py-1.5 rounded-bl-xl text-[10px] font-bold uppercase tracking-widest
                            {{ $booking->status === 'confirmed' ? 'bg-emerald-100 text-emerald-600' : 
                               ($booking->status === 'cancelled' ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-500') }}">
                            {{ $booking->status === 'confirmed' ? 'Confirmada' : 
                               ($booking->status === 'cancelled' ? 'Cancelada' : 'Pendiente') }}
                        </div>

                        <div class="flex items-start gap-4 mb-5">
                            <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 shrink-0 border border-slate-100">
                                <img src="{{ asset('img/logo.png') }}" class="w-8 h-auto opacity-100" alt="">
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-slate-900 truncate">{{ $booking->user->business_name }}</h3>
                                <p class="text-sm text-slate-500 font-medium truncate">{{ $booking->service->name }}</p>
                            </div>
                        </div>

                        <div class="space-y-3 mb-6 bg-slate-50 rounded-xl p-4">
                            @php $tz = $booking->user->timezone ?? 'Europe/Madrid'; @endphp
                            <div class="flex items-center gap-3 text-sm text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="font-semibold">{{ $booking->starts_at->setTimezone($tz)->translatedFormat('l, d \d\e F') }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-semibold">
                                    {{ $booking->starts_at->setTimezone($tz)->format('H:i') }} — {{ $booking->ends_at->setTimezone($tz)->format('H:i') }}
                                </span>
                            </div>
                        </div>

                        @if($booking->status !== 'cancelled' && $booking->starts_at->isFuture())
                            <div class="flex gap-2">
                                <a href="{{ route('customer.reschedule', $booking) }}"
                                   class="flex-1 py-2 text-center text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                                    REPROGRAMAR
                                </a>
                                <form action="{{ route('customer.cancel', $booking) }}" method="POST" class="flex-1" 
                                      onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta reserva? Esta acción no se puede deshacer.')">
                                    @csrf
                                    <button type="submit" class="w-full py-2 text-xs font-bold text-red-600 bg-white border border-red-100 rounded-lg hover:bg-red-50 transition-colors">
                                        CANCELAR
                                    </button>
                                </form>
                            </div>
                        @else
                           <div class="py-2 text-center text-[10px] font-bold text-slate-300 uppercase tracking-widest border border-dashed border-slate-100 rounded-lg">
                               Reserva finalizada o cancelada
                           </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <footer class="mt-16 pt-8 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-400 font-medium tracking-wide uppercase">Citalify — Tu tiempo, gestionado</p>
        </footer>

    </main>

</body>
</html>
