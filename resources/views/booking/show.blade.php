<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservar cita — {{ $business->business_name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700,800|outfit:600,700,800&display=swap" rel="stylesheet" />

    {{-- Registrar componente Alpine ANTES de que Alpine arranque (alpine:init) --}}
    <script>
        window.__booking = {
            slug:     @json($business->business_slug),
            services: @json($services),
            old:      @json(['service_id' => old('service_id'), 'date' => old('date'), 'time' => old('time')]),
        };

        document.addEventListener('alpine:init', () => {
            Alpine.data('bookingWizard', () => {
                const { slug, services, old: prev } = window.__booking;

                const restored = prev.service_id
                    ? (services.find(s => s.id == prev.service_id) ?? null)
                    : null;

                return {
                    slug,
                    services,

                    // Pasos: 1=servicio, 2=calendario+hora, 3=datos
                    step: restored ? 3 : 1,

                    selectedService: restored,
                    days:            [],
                    loadingCalendar: false,

                    selectedDate: prev.date  || null,
                    slots:        [],
                    loadingSlots: false,
                    selectedTime: prev.time  || null,

                    // Si sólo hay 1 servicio, ir directo al calendario
                    async init() {
                        if (services.length === 1 && !restored) {
                            this.selectService(services[0]);
                        }
                    },

                    async selectService(svc) {
                        this.selectedService = svc;
                        this.selectedDate    = null;
                        this.selectedTime    = null;
                        this.slots           = [];
                        this.step            = 2;
                        await this.loadCalendar();
                    },

                    async loadCalendar() {
                        this.loadingCalendar = true;
                        this.days = [];
                        try {
                            const res = await fetch('/' + slug + '/calendario?service_id=' + this.selectedService.id);
                            this.days = await res.json();
                        } catch (_) {}
                        this.loadingCalendar = false;
                    },

                    async selectDay(day) {
                        if (day.status !== 'available') return;
                        // Toggle si vuelven a hacer click en el mismo día
                        if (this.selectedDate === day.date) {
                            this.selectedDate = null;
                            this.selectedTime = null;
                            this.slots        = [];
                            return;
                        }
                        this.selectedDate = day.date;
                        this.selectedTime = null;
                        this.loadingSlots = true;
                        this.slots        = [];
                        try {
                            const res = await fetch(
                                '/' + slug + '/disponibilidad'
                                + '?service_id=' + this.selectedService.id
                                + '&date='       + day.date
                            );
                            this.slots = await res.json();
                        } catch (_) {}
                        this.loadingSlots = false;
                    },

                    // Helpers de fecha
                    dayName(dateStr) {
                        return ['Do','Lu','Ma','Mi','Ju','Vi','Sá'][new Date(dateStr + 'T12:00:00').getDay()];
                    },
                    dayNum(dateStr) {
                        return new Date(dateStr + 'T12:00:00').getDate();
                    },
                    monthLabel(dateStr) {
                        return new Date(dateStr + 'T12:00:00')
                            .toLocaleDateString('es-ES', { month: 'short' })
                            .replace('.','');
                    },
                    formatDate(dateStr) {
                        if (!dateStr) return '';
                        return new Date(dateStr + 'T12:00:00')
                            .toLocaleDateString('es-ES', { weekday:'long', day:'numeric', month:'long' });
                    },
                    price(svc) {
                        return parseFloat(svc.price) > 0
                            ? parseFloat(svc.price).toFixed(2) + ' €'
                            : 'Gratis';
                    },
                };
            });
        });
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-favicons />
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen">

{{-- Header --}}
<header class="bg-white/95 backdrop-blur-sm border-b border-slate-100 shadow-sm">
    <div class="max-w-lg mx-auto px-4 py-4 flex items-center gap-3">
        <img src="{{ asset('img/logo.png') }}" alt="Citalify Logo" class="h-9 w-auto rounded-xl">
        <div>
            <p class="font-display font-bold text-slate-800 leading-tight">{{ $business->business_name }}</p>
            @if($business->address)
                <p class="text-xs text-slate-400">{{ $business->address }}</p>
            @endif
        </div>
    </div>
</header>

<main class="max-w-lg mx-auto px-4 py-8 pb-16" x-data="bookingWizard" x-cloak>

    {{-- Errores de servidor --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl text-sm text-red-600 space-y-1">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Indicador de pasos --}}
    <div class="flex items-center gap-2 mb-6">
        @foreach(['Servicio', 'Fecha y hora', 'Tus datos'] as $i => $label)
            <div class="flex items-center {{ $i < 2 ? 'flex-1' : '' }}">
                <div class="flex items-center gap-1.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold transition-colors"
                         :class="step > {{ $i + 1 }}
                             ? 'bg-emerald-100 text-emerald-600'
                             : (step === {{ $i + 1 }} ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-400')">
                        <span x-show="step <= {{ $i + 1 }}">{{ $i + 1 }}</span>
                        <span x-show="step >  {{ $i + 1 }}">&#10003;</span>
                    </div>
                    <span class="text-xs font-medium hidden sm:inline transition-colors"
                          :class="step >= {{ $i + 1 }} ? 'text-slate-600' : 'text-slate-400'">{{ $label }}</span>
                </div>
                @if($i < 2)
                    <div class="flex-1 h-px mx-2 transition-colors"
                         :class="step > {{ $i + 1 }} ? 'bg-emerald-300' : 'bg-slate-200'"></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════
         PASO 1 — Elegir servicio
    ══════════════════════════════════════ --}}
    <div x-show="step === 1"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h1 class="text-xl font-display font-bold text-slate-800 mb-1">¿Qué servicio necesitas?</h1>
            <p class="text-sm text-slate-500 mb-5">Elige el servicio para ver la disponibilidad.</p>

            @if($services->isEmpty())
                <p class="text-center text-slate-400 text-sm py-8">Este negocio no tiene servicios disponibles aún.</p>
            @else
                <div class="space-y-3">
                    <template x-for="svc in services" :key="svc.id">
                        <button type="button" @click="selectService(svc)"
                                class="w-full text-left flex items-center justify-between p-4 rounded-xl border-2 border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/30 transition-all group">
                            <div>
                                <p class="font-semibold text-slate-800 group-hover:text-emerald-700 transition-colors" x-text="svc.name"></p>
                                <p class="text-sm text-slate-400 mt-0.5" x-text="svc.duration_minutes + ' min'"></p>
                            </div>
                            <div class="text-right shrink-0 ml-4">
                                <p class="font-bold text-slate-700" x-text="price(svc)"></p> 
                            </div>
                        </button>
                    </template>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════
         PASO 2 — Calendario + hora
    ══════════════════════════════════════ --}}
    <div x-show="step === 2"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

            {{-- Servicio seleccionado (chip) --}}
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h1 class="text-xl font-display font-bold text-slate-800 leading-tight">Elige un día</h1>
                    <p class="text-sm text-slate-500 mt-0.5">
                        <span x-text="selectedService?.name"></span>
                        &mdash; <span x-text="selectedService?.duration_minutes + ' min'"></span>
                    </p>
                </div>
                <button type="button" @click="step = 1; days = []"
                        class="text-xs text-slate-400 hover:text-emerald-600 font-medium transition-colors">
                    Cambiar
                </button>
            </div>

            {{-- Leyenda --}}
            <div class="flex items-center gap-4 mb-4 text-xs text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> Disponible</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-400 inline-block"></span> Completo</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-slate-800 inline-block"></span> Cerrado</span>
            </div>

            {{-- Calendario 14 días --}}
            <div x-show="loadingCalendar" class="flex justify-center py-12">
                <svg class="animate-spin w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>

            <div x-show="!loadingCalendar && days.length > 0" class="grid grid-cols-7 gap-1.5">
                <template x-for="day in days" :key="day.date">
                    <button
                        type="button"
                        @click="selectDay(day)"
                        :disabled="day.status !== 'available'"
                        class="aspect-square rounded-xl flex flex-col items-center justify-center transition-all select-none"
                        :class="{
                            'bg-emerald-500 hover:bg-emerald-400 text-white cursor-pointer shadow-sm':            day.status === 'available' && selectedDate !== day.date,
                            'bg-emerald-600 text-white ring-2 ring-emerald-300 ring-offset-1 scale-105 shadow-md': day.status === 'available' && selectedDate === day.date,
                            'bg-slate-800 text-slate-500 cursor-default':                                           day.status === 'closed',
                            'bg-red-400 text-white/80 cursor-default':                                             day.status === 'full',
                        }"
                    >
                        <span class="text-xs font-medium leading-none mb-0.5 opacity-80" x-text="dayName(day.date)"></span>
                        <span class="text-base font-bold leading-none"    x-text="dayNum(day.date)"></span>
                    </button>
                </template>
            </div>

            {{-- Slots de hora --}}
            <template x-if="selectedDate">
                <div class="mt-5 pt-5 border-t border-slate-100">
                    <p class="text-sm font-semibold text-slate-700 mb-3 capitalize" x-text="'Horas disponibles — ' + formatDate(selectedDate)"></p>

                    <div x-show="loadingSlots" class="flex justify-center py-6">
                        <svg class="animate-spin w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>

                    <div x-show="!loadingSlots && slots.length === 0"
                         class="text-center py-4 text-sm text-slate-400">
                        No quedan horas disponibles este día.
                    </div>

                    <div x-show="!loadingSlots && slots.length > 0" class="grid grid-cols-4 gap-2">
                        <template x-for="slot in slots" :key="slot">
                            <button
                                type="button"
                                @click="selectedTime = slot"
                                class="py-2.5 rounded-xl border-2 text-sm font-semibold tabular-nums transition-all"
                                :class="selectedTime === slot
                                    ? 'bg-emerald-500 border-emerald-500 text-white shadow-sm'
                                    : 'border-slate-200 text-slate-700 hover:border-emerald-400 hover:text-emerald-700'"
                                x-text="slot"
                            ></button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Botones --}}
            <div class="mt-6 flex gap-3">
                <button type="button" @click="step = 1; days = []"
                        class="flex-1 py-3 border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-colors text-sm">
                    &larr; Atrás
                </button>
                <button type="button" @click="step = 3"
                        x-show="selectedTime"
                        class="flex-1 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white font-semibold py-3 rounded-xl shadow-md transition-all text-sm">
                    Continuar &rarr;
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         PASO 3 — Datos del cliente
    ══════════════════════════════════════ --}}
    <div x-show="step === 3"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h1 class="text-xl font-display font-bold text-slate-800 mb-1">Tus datos</h1>
            <p class="text-sm text-slate-500 mb-5">Casi listo. Confirma tu reserva.</p>

            {{-- Resumen --}}
            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-slate-800 text-sm truncate" x-text="selectedService?.name"></p>
                    <p class="text-sm text-slate-500 capitalize truncate" x-text="formatDate(selectedDate) + ' a las ' + selectedTime"></p>
                </div>
                <button type="button" @click="step = 2"
                        class="ml-auto text-xs text-slate-400 hover:text-emerald-600 font-medium shrink-0 transition-colors">
                    Cambiar
                </button>
            </div>

            <form method="POST" action="{{ route('booking.store', $business->business_slug) }}">
                @csrf
                <input type="hidden" name="service_id" :value="selectedService?.id">
                <input type="hidden" name="date"       :value="selectedDate">
                <input type="hidden" name="time"       :value="selectedTime">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Tu nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                           placeholder="Ej: Ana García" required autocomplete="name"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('customer_name') border-red-400 @enderror">
                    @error('customer_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Teléfono <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}"
                           placeholder="Ej: 612 345 678" required autocomplete="tel"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('customer_phone') border-red-400 @enderror">
                    @error('customer_phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                           placeholder="tu@email.com" required autocomplete="email"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('customer_email') border-red-400 @enderror">
                    @error('customer_email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Notas <span class="text-slate-400 text-xs font-normal">(opcional)</span>
                    </label>
                    <textarea name="notes" rows="2" placeholder="Algún comentario para el negocio..."
                              class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('notes') }}</textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="step = 2"
                            class="flex-1 py-3 border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-colors text-sm">
                        &larr; Atrás
                    </button>
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white font-semibold py-3 rounded-xl shadow-md shadow-emerald-900/20 transition-all text-sm">
                        Confirmar reserva
                    </button>
                </div>
            </form>
        </div>
    </div>

    <p class="text-center text-xs text-slate-300 mt-8">
        <a href="{{ route('customer.login') }}" class="text-slate-400 font-medium hover:text-emerald-500 transition-colors">Gestionar mis reservas</a>
        <span class="mx-2">&bull;</span>
        Reservas por <a href="/" class="text-emerald-500 font-medium hover:text-emerald-700">citalify</a>
    </p>

</main>

</body>
</html>

