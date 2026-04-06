<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi agenda — {{ $business->business_name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700,800|outfit:600,700,800&display=swap" rel="stylesheet" />

    <script>
        window.__ownerData = { slug: @json($business->business_slug) };

        document.addEventListener('alpine:init', () => {
            Alpine.data('ownerAgenda', () => {
                const { slug } = window.__ownerData;
                return {
                    slug,
                    days:         [],
                    loading:      true,
                    selectedDate: null,
                    bookings:     [],
                    loadingDay:   false,

                    async init() {
                        await this.loadCalendar();
                    },

                    async loadCalendar() {
                        this.loading = true;
                        try {
                            const res = await fetch('/' + slug + '/agenda-propietario');
                            this.days = await res.json();
                        } catch (_) {}
                        this.loading = false;
                    },

                    async selectDay(day) {
                        if (this.selectedDate === day.date) {
                            this.selectedDate = null;
                            this.bookings     = [];
                            return;
                        }
                        this.selectedDate = day.date;
                        this.loadingDay   = true;
                        this.bookings     = [];
                        try {
                            const res   = await fetch('/' + slug + '/dia?date=' + day.date);
                            this.bookings = await res.json();
                        } catch (_) {}
                        this.loadingDay = false;
                    },

                    dayName(dateStr) {
                        return ['Do','Lu','Ma','Mi','Ju','Vi','Sá'][new Date(dateStr + 'T12:00:00').getDay()];
                    },
                    dayNum(dateStr) {
                        return new Date(dateStr + 'T12:00:00').getDate();
                    },
                    formatDate(dateStr) {
                        if (!dateStr) return '';
                        return new Date(dateStr + 'T12:00:00').toLocaleDateString('es-ES', {
                            weekday: 'long', day: 'numeric', month: 'long'
                        });
                    },
                };
            });
        });
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen">

{{-- Header --}}
<header class="bg-white/95 backdrop-blur-sm border-b border-slate-100 shadow-sm">
    <div class="max-w-lg mx-auto px-4 py-4 flex items-center gap-3">
        <img src="{{ asset('img/logo.png') }}" alt="Citalify Logo" class="h-9 w-auto rounded-xl">
        <div class="flex-1 min-w-0">
            <p class="font-display font-bold text-slate-800 leading-tight truncate">{{ $business->business_name }}</p>
            @if($business->address)
                <p class="text-xs text-slate-400 truncate">{{ $business->address }}</p>
            @endif
        </div>
        <a href="{{ route('dashboard') }}"
           class="text-xs font-medium text-slate-500 hover:text-emerald-600 transition-colors shrink-0">
            &larr; Panel
        </a>
    </div>
</header>

{{-- Banner propietario --}}
<div class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-xs font-medium text-center py-2 px-4">
    Estás viendo tu página como propietario &mdash;
    <a href="{{ route('dashboard') }}" class="underline underline-offset-2 hover:no-underline">Ir al dashboard</a>
</div>

<main class="max-w-lg mx-auto px-4 py-8 pb-16" x-data="ownerAgenda" x-cloak>

    {{-- Cargando --}}
    <div x-show="loading" class="flex justify-center py-20">
        <svg class="animate-spin w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
    </div>

    <div x-show="!loading">

        {{-- Título --}}
        <div class="mb-5">
            <h1 class="text-xl font-display font-bold text-slate-800">Tu agenda</h1>
            <p class="text-sm text-slate-500 mt-0.5">Próximos 14 días. Haz click en un día para ver las citas.</p>
        </div>

        {{-- Leyenda --}}
        <div class="flex items-center gap-4 mb-4 text-xs text-slate-500">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> Con citas
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded border-2 border-slate-200 inline-block"></span> Libre
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-slate-800 inline-block"></span> Cerrado
            </span>
        </div>

        {{-- Calendario 14 días --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-4">
            <div class="grid grid-cols-7 gap-1.5">
                <template x-for="day in days" :key="day.date">
                    <button
                        type="button"
                        @click="selectDay(day)"
                        :disabled="day.status === 'closed'"
                        class="aspect-square rounded-xl flex flex-col items-center justify-center transition-all select-none relative"
                        :class="{
                            'bg-emerald-500 hover:bg-emerald-400 text-white shadow-sm':            day.count > 0 && selectedDate !== day.date,
                            'bg-emerald-600 text-white ring-2 ring-emerald-300 ring-offset-1 scale-105': day.count > 0 && selectedDate === day.date,
                            'bg-white border-2 border-slate-200 hover:border-emerald-300 text-slate-600': day.count === 0 && day.status === 'open' && selectedDate !== day.date,
                            'bg-emerald-50 border-2 border-emerald-400 text-emerald-700':               day.count === 0 && day.status === 'open' && selectedDate === day.date,
                            'bg-slate-800 text-slate-500 cursor-default':                              day.status === 'closed',
                        }"
                    >
                        <span class="text-xs font-medium leading-none mb-0.5 opacity-75" x-text="dayName(day.date)"></span>
                        <span class="text-base font-bold leading-none" x-text="dayNum(day.date)"></span>
                        {{-- Badge con número de citas --}}
                        <template x-if="day.count > 0">
                            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-white text-emerald-600 text-xs font-bold flex items-center justify-center shadow-sm ring-1 ring-emerald-100"
                                  x-text="day.count"></span>
                        </template>
                    </button>
                </template>
            </div>
        </div>

        {{-- Detalle del día seleccionado --}}
        <template x-if="selectedDate">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-50">
                    <h2 class="font-display font-bold text-slate-800 text-base capitalize"
                        x-text="formatDate(selectedDate)"></h2>
                    <button type="button" @click="selectedDate = null; bookings = []"
                            class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Cargando citas del día --}}
                <div x-show="loadingDay" class="flex justify-center py-10">
                    <svg class="animate-spin w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </div>

                {{-- Sin citas --}}
                <div x-show="!loadingDay && bookings.length === 0"
                     class="flex flex-col items-center py-10 text-center px-6">
                    <p class="text-sm font-medium text-slate-500">Sin citas este día</p>
                    <p class="text-xs text-slate-400 mt-1">Los clientes pueden reservar en tu página pública.</p>
                </div>

                {{-- Lista de citas --}}
                <div x-show="!loadingDay && bookings.length > 0" class="divide-y divide-slate-50">
                    <template x-for="b in bookings" :key="b.id">
                        <div class="px-5 py-4 hover:bg-slate-50/60 transition-colors">
                            <div class="flex items-start gap-3">

                                {{-- Hora --}}
                                <div class="text-center w-14 shrink-0 pt-0.5">
                                    <p class="text-base font-bold text-slate-700 tabular-nums leading-none" x-text="b.starts_at"></p>
                                    <p class="text-xs text-slate-400 tabular-nums" x-text="b.ends_at"></p>
                                </div>

                                {{-- Barra color --}}
                                <div class="w-1 h-10 rounded-full bg-emerald-400 shrink-0 mt-0.5"></div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-800 truncate" x-text="b.customer_name"></p>
                                    <p class="text-sm text-slate-500 truncate" x-text="b.service"></p>
                                    <template x-if="b.notes">
                                        <p class="text-xs text-slate-400 mt-0.5 truncate" x-text="'Nota: ' + b.notes"></p>
                                    </template>
                                </div>

                                {{-- Estado + teléfono --}}
                                <div class="shrink-0 text-right">
                                    <span x-show="b.status === 'confirmed'"
                                          class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700">
                                        Confirmada
                                    </span>
                                    <span x-show="b.status === 'pending'"
                                          class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700">
                                        Pendiente
                                    </span>
                                    <a :href="'tel:' + b.customer_phone"
                                       class="block mt-1 text-xs text-slate-400 hover:text-emerald-600 transition-colors tabular-nums"
                                       x-text="b.customer_phone"></a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

    </div>

    <p class="text-center text-xs text-slate-300 mt-8">
        Gestionado con <a href="{{ route('dashboard') }}" class="text-emerald-500 font-medium hover:text-emerald-700">citalify</a>
    </p>

</main>

</body>
</html>

