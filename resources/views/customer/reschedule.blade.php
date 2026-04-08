<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reprogramar Cita — Citalify</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700,800|outfit:600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-favicons />
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('bookingWizard', () => ({
                slug: @json($booking->user->business_slug),
                selectedService: @json($booking->service),
                selectedDate: null,
                selectedTime: null,
                days: [],
                slots: [],
                loadingCalendar: false,
                loadingSlots: false,

                async init() {
                    console.log("Iniciando bookingWizard para el negocio:", this.slug);
                    if (!this.slug) {
                        console.error("No se encontró el slug del negocio.");
                        return;
                    }
                    await this.loadCalendar();
                },

                async loadCalendar() {
                    this.loadingCalendar = true;
                    this.days = [];
                    try {
                        const url = '/' + this.slug + '/calendario?service_id=' + this.selectedService.id;
                        console.log("Cargando calendario desde:", url);
                        const res = await fetch(url);
                        if (!res.ok) throw new Error("Error en la respuesta del servidor: " + res.status);
                        this.days = await res.json();
                    } catch (e) {
                        console.error("Error al cargar el calendario:", e);
                    }
                    this.loadingCalendar = false;
                },

                async selectDay(day) {
                    if (day.status !== 'available') return;
                    this.selectedDate = day.date;
                    this.selectedTime = null;
                    this.loadingSlots = true;
                    this.slots = [];
                    try {
                        const url = '/' + this.slug + '/disponibilidad?service_id=' + this.selectedService.id + '&date=' + day.date;
                        console.log("Cargando disponibilidad desde:", url);
                        const res = await fetch(url);
                        if (!res.ok) throw new Error("Error en la disponibilidad: " + res.status);
                        this.slots = await res.json();
                    } catch (e) {
                        console.error("Error al cargar slots:", e);
                    }
                    this.loadingSlots = false;
                },

                dayName(d) { return ['Do','Lu','Ma','Mi','Ju','Vi','Sá'][new Date(d + 'T12:00:00').getDay()]; },
                dayNum(d) { return new Date(d + 'T12:00:00').getDate(); },
                formatDate(d) { return new Date(d + 'T12:00:00').toLocaleDateString('es-ES', { weekday:'long', day:'numeric', month:'long' }); }
            }));
        });
    </script>
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen pb-12">

    <header class="bg-white/95 backdrop-blur-sm border-b border-slate-100 shadow-sm sticky top-0 z-50">
        <div class="max-w-lg mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('customer.index') }}" class="text-xs font-bold text-slate-400 hover:text-emerald-600 transition-colors flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                VOLVER
            </a>
            <div class="text-right">
                <p class="font-display font-bold text-slate-800 leading-none">Reprogramar cita</p>
                <p class="text-[10px] text-slate-400 uppercase tracking-tighter mt-1">{{ $booking->user->business_name }}</p>
            </div>
        </div>
    </header>

    <main class="max-w-lg mx-auto px-4 py-8" x-data="bookingWizard" x-cloak>


        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-6 sm:p-8">
            <div class="mb-8">
                <h2 class="text-xl font-bold text-slate-900">{{ $booking->service->name }}</h2>
                <p class="text-sm text-slate-500 mt-1">Selecciona la nueva fecha y hora para tu cita.</p>
            </div>

            {{-- Calendario --}}
            <div class="mb-8">
                <p class="text-sm font-semibold text-slate-700 mb-4 uppercase tracking-wider">1. Elige el día</p>
                <div x-show="loadingCalendar" class="flex justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div class="grid grid-cols-7 gap-1.5" x-show="!loadingCalendar">
                    <template x-for="day in days" :key="day.date">
                        <button type="button" @click="selectDay(day)" :disabled="day.status !== 'available'"
                                class="aspect-square rounded-xl flex flex-col items-center justify-center transition-all"
                                :class="{
                                    'bg-emerald-500 text-white': day.status === 'available' && selectedDate !== day.date,
                                    'bg-emerald-700 text-white ring-2 ring-emerald-300 ring-offset-1 scale-105': day.status === 'available' && selectedDate === day.date,
                                    'bg-slate-100 text-slate-300 cursor-not-allowed': day.status !== 'available'
                                }">
                            <span class="text-[10px] font-bold opacity-75" x-text="dayName(day.date)"></span>
                            <span class="text-sm font-black" x-text="dayNum(day.date)"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Horas --}}
            <div x-show="selectedDate" class="mb-8 x-transition">
                <p class="text-sm font-semibold text-slate-700 mb-4 uppercase tracking-wider">2. Elige la hora</p>
                <div x-show="loadingSlots" class="flex justify-center py-4">
                    <svg class="animate-spin h-6 w-6 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div class="grid grid-cols-4 gap-2" x-show="!loadingSlots">
                    <template x-for="slot in slots" :key="slot">
                        <button type="button" @click="selectedTime = slot"
                                class="py-3 rounded-xl border-2 text-sm font-bold transition-all"
                                :class="selectedTime === slot ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-slate-100 text-slate-600 hover:border-emerald-200'">
                            <span x-text="slot"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Formulario Final --}}
            <form action="{{ route('customer.reschedule.post', $booking) }}" method="POST" x-show="selectedTime">
                @csrf
                <input type="hidden" name="date" :value="selectedDate">
                <input type="hidden" name="time" :value="selectedTime">
                
                @php $tz = $booking->user->timezone ?? 'Europe/Madrid'; @endphp
                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 mb-6">
                    <p class="text-xs text-amber-800 font-medium leading-relaxed">
                        Al confirmar, tu cita actual del <span class="font-bold">{{ $booking->starts_at->setTimezone($tz)->translatedFormat('d \d\e F \a \l\a\s H:i') }}</span> se moverá al <span class="font-bold" x-text="formatDate(selectedDate) + ' a las ' + selectedTime"></span>.
                    </p>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-emerald-100 transition-all">
                    Confirmar cambio
                </button>
            </form>
        </div>
    </main>

</body>
</html>
