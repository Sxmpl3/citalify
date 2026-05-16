<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800 leading-tight">
                    {{ auth()->user()->business_name ?? auth()->user()->name }}
                </h2>
                <p class="text-sm text-slate-400 mt-0.5">
                    {{ now(auth()->user()->timezone ?? 'Europe/Madrid')->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="ownerAgenda('{{ auth()->user()->business_slug }}', '{{ auth()->user()->schedule_type }}')"
        x-cloak>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div
                    class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-4 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 text-sm font-medium">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Enlace de reservas --}}
            @if(auth()->user()->business_slug)
                <div
                    class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-slate-800 text-sm">Tu enlace de reservas</p>
                            <p class="text-slate-500 text-xs mt-0.5">Comparte este enlace para que tus clientes reserven
                                online.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 w-full md:w-auto" x-data="{ copied: false }">
                        <div class="flex items-center flex-1 md:flex-initial">
                            <input type="text" readonly value="citalify.es/{{ auth()->user()->business_slug }}"
                                class="text-xs font-mono bg-white border border-slate-200 text-slate-600 px-3 py-2 md:py-2.5 rounded-l-lg w-full md:w-64 focus:outline-none focus:ring-0">
                            <button
                                @click="navigator.clipboard.writeText('https://citalify.es/{{ auth()->user()->business_slug }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="bg-white hover:bg-slate-50 border border-l-0 border-slate-200 text-slate-600 px-4 py-2 md:py-2.5 text-xs font-medium rounded-r-lg transition-colors flex items-center gap-1.5 focus:outline-none whitespace-nowrap">
                                <span x-show="!copied">Copiar</span>
                                <span x-show="copied" class="text-emerald-600" style="display: none;">¡Copiado!</span>
                            </button>
                        </div>
                        <a href="{{ route('booking.show', auth()->user()->business_slug) }}" target="_blank"
                            class="p-2 md:p-2.5 text-slate-400 hover:text-emerald-600 bg-white border border-slate-200 rounded-lg hover:border-emerald-200 transition-colors shrink-0"
                            title="Abrir web">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Hoy --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-slate-500">Citas hoy</p>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-slate-800">{{ $stats['today'] }}</p>
                </div>
                {{-- Pendientes --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-slate-500">Pendientes</p>
                        <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-slate-800">{{ $stats['pending'] }}</p>
                </div>
                {{-- Esta semana --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-slate-500">Esta semana</p>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-slate-800">{{ $stats['week'] }}</p>
                </div>
                {{-- Este mes --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-slate-500">Este mes</p>
                        <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-slate-800">{{ $stats['month'] }}</p>
                </div>
            </div>

            {{-- Citas Hoy --}}
            <div class="mt-8 mb-6">
                <h2 class="font-display font-bold text-lg text-slate-800 mb-4">Agenda de hoy</h2>

                @if($todayBookings->isEmpty())
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center">
                        <div class="w-4 h-4 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-600">No hay ninguna cita pendiente para hoy.</p>
                        <p class="text-xs text-slate-400 mt-1">Disfruta de tu tiempo libre o revisa los próximos
                            {{ auth()->user()->booking_days_ahead }} días abajo.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php $tz = auth()->user()->timezone ?? 'Europe/Madrid'; @endphp
                        @foreach($todayBookings as $booking)
                            <div
                                class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-4 relative overflow-hidden group">
                                {{-- Time --}}
                                <div class="text-center w-14 shrink-0 transition-transform group-hover:scale-105">
                                    <p class="text-lg font-bold text-slate-700 leading-none">
                                        {{ \Carbon\Carbon::parse($booking->starts_at)->setTimezone($tz)->format('H:i') }}</p>
                                    <p class="text-xs text-slate-400 mt-1.5">
                                        {{ \Carbon\Carbon::parse($booking->ends_at)->setTimezone($tz)->format('H:i') }}</p>
                                </div>

                                {{-- Separator Bar --}}
                                <div class="w-1 h-10 bg-emerald-400 rounded-full shrink-0"></div>

                                {{-- Details --}}
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-800 truncate">{{ $booking->customer_name }}</p>
                                    <p class="text-sm text-slate-500 truncate">{{ $booking->service->name ?? 'Servicio' }}</p>
                                    @if($booking->notes)
                                        <p class="text-xs text-slate-400 mt-1 truncate">"{{ $booking->notes }}"</p>
                                    @endif
                                </div>
                                <div class="shrink-0 flex items-center gap-3">
                                    @if($booking->status === 'pending')
                                        <form action="{{ route('dashboard.bookings.confirm', $booking) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="text-xs text-emerald-600 hover:text-emerald-700 underline underline-offset-2 font-bold bg-emerald-50 px-2.5 py-1 rounded-lg transition-colors">
                                                Confirmar
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" @click.stop="openCancel({
                                                id: {{ $booking->id }},
                                                customer_name: {{ json_encode($booking->customer_name) }},
                                                customer_email: {{ json_encode($booking->customer_email ?? '') }}
                                            })"
                                        class="text-xs text-red-500 hover:text-red-700 underline underline-offset-2 transition-opacity font-medium bg-red-50 px-2 py-1 rounded">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- AGENDA INTERACTIVA --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-8">
                <div>
                    <h1 class="text-xl font-display font-bold text-slate-800">Vista rápida de tu agenda</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Vista de {{ auth()->user()->booking_days_ahead }} días. Haz
                        click en un día para ver y gestionar citas.</p>
                </div>
                <div class="flex items-center gap-3 self-start sm:self-auto">
                    @if(auth()->user()->schedule_type === 'normal')
                        <a href="{{ route('schedule.edit') }}"
                            class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all shadow-sm">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Configurar Horario
                        </a>
                        <a href="{{ route('vacations.index') }}"
                            class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-rose-600 bg-white border border-rose-200 rounded-xl hover:bg-rose-50 transition-all shadow-sm">
                            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Vacaciones
                        </a>
                    @endif
                    <button @click="openAddModal = true"
                        class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/20 px-4 py-2.5 text-sm font-medium rounded-xl shrink-0 transition-transform hover:scale-105">
                        + Nueva cita
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-4 text-xs text-slate-500 mb-2">
                <span class="flex items-center gap-1.5"><span
                        class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> Con citas</span>
                <span class="flex items-center gap-1.5"><span
                        class="w-3 h-3 rounded border-2 border-slate-200 inline-block"></span> Libre</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-slate-800 inline-block"></span>
                    Cerrado</span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 relative min-h-[140px]">
                <div x-show="loading"
                    class="flex justify-center absolute inset-0 items-center bg-white/60 z-10 rounded-2xl">
                    <svg class="animate-spin w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                </div>

                <div class="grid grid-cols-7 gap-1.5 sm:gap-2">
                    <template x-for="day in days" :key="day.date">
                        <button type="button" @click="selectDay(day)"
                            class="aspect-square rounded-xl flex flex-col items-center justify-center transition-all select-none relative"
                            :class="{
                                'bg-emerald-500 hover:bg-emerald-400 text-white shadow-sm': day.count > 0 && selectedDate !== day.date,
                                'bg-emerald-600 text-white ring-2 ring-emerald-300 ring-offset-1 scale-105': day.count > 0 && selectedDate === day.date,
                                'bg-white border-2 border-slate-200 hover:border-emerald-300 text-slate-600': day.count === 0 && day.status === 'open' && selectedDate !== day.date,
                                'bg-emerald-50 border-2 border-emerald-400 text-emerald-700': day.count === 0 && day.status === 'open' && selectedDate === day.date,
                                'bg-slate-800 text-slate-500 cursor-default': day.status === 'closed',
                            }">
                            <span class="text-xs font-medium leading-none mb-0.5 opacity-80"
                                x-text="dayName(day.date)"></span>
                            <span class="text-base sm:text-lg font-bold leading-none" x-text="dayNum(day.date)"></span>
                            <template x-if="day.count > 0">
                                <span
                                    class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-white text-emerald-600 text-xs font-bold flex items-center justify-center shadow-sm ring-1 ring-emerald-100"
                                    x-text="day.count"></span>
                            </template>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Detalle del día seleccionado --}}
            <template x-if="selectedDate">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden relative">
                    <div class="flex items-center justify-between px-5 py-3 border-b border-slate-50">
                        <div class="flex items-center gap-3">
                            <h2 class="font-display font-bold text-slate-800 text-base capitalize"
                                x-text="formatDate(selectedDate)"></h2>
                            <template x-if="scheduleType === 'custom'">
                                <button @click="openConfig()"
                                    class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-2.5 py-1 rounded-md font-medium transition-colors">
                                    Configurar Horario
                                </button>
                            </template>
                        </div>
                        <button type="button" @click="selectedDate = null; bookings = []"
                            class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div x-show="loadingDay" class="flex justify-center py-10">
                        <svg class="animate-spin w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                    </div>

                    <div x-show="!loadingDay && bookings.length === 0"
                        class="flex flex-col items-center py-10 text-center px-6">
                        <p class="text-sm font-medium text-slate-500">Sin citas este día</p>
                        <p class="text-xs text-slate-400 mt-1">Los clientes pueden reservar en tu página pública o
                            puedes crear la cita manualmente.</p>
                    </div>

                    <div x-show="!loadingDay && bookings.length > 0" class="divide-y divide-slate-50">
                        <template x-for="b in bookings" :key="b.id">
                            <div
                                class="px-4 sm:px-5 py-4 hover:bg-slate-50/60 transition-colors flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex items-center gap-4 flex-1 min-w-0">
                                    <div class="text-center w-14 shrink-0">
                                        <p class="text-base font-bold text-slate-700 tabular-nums leading-none"
                                            x-text="b.starts_at"></p>
                                        <p class="text-xs text-slate-400 tabular-nums" x-text="b.ends_at"></p>
                                    </div>
                                    <div class="w-1 h-10 rounded-full bg-emerald-400 shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-slate-800 truncate" x-text="b.customer_name"></p>
                                        <p class="text-sm text-slate-500 truncate" x-text="b.service"></p>
                                        <template x-if="b.customer_email">
                                            <p class="text-xs text-slate-400 truncate"
                                                x-text="'Email: ' + b.customer_email"></p>
                                        </template>
                                        <template x-if="b.notes">
                                            <p class="text-xs text-slate-400 mt-0.5 truncate"
                                                x-text="'Nota: ' + b.notes"></p>
                                        </template>
                                    </div>
                                </div>
                                <div
                                    class="flex sm:flex-col items-center sm:items-end gap-3 sm:gap-2 pl-[4.5rem] sm:pl-0">
                                    <span x-show="b.status === 'confirmed'"
                                        class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700">Confirmada</span>
                                    <span x-show="b.status === 'pending'"
                                        class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700">Pendiente</span>

                                    <form x-show="b.status === 'pending'"
                                        :action="'{{ url('dashboard/citas') }}/' + b.id + '/confirm'" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="text-xs text-emerald-600 hover:text-emerald-700 underline underline-offset-2 font-bold bg-emerald-50 px-2 py-1 rounded-lg">
                                            Confirmar
                                        </button>
                                    </form>

                                    <button type="button" @click.stop="console.log('Button clicked', b); openCancel(b)"
                                        class="text-xs text-red-500 hover:text-red-700 underline underline-offset-2 font-medium">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Modales --}}
            <!-- Modal Configurar Horario Especial -->
            <div x-show="openConfigModal" style="display: none;"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4"
                x-transition.opacity>
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden"
                    @click.outside="openConfigModal = false" x-transition>
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-slate-800">Horario del Día</h3>
                        <button @click="openConfigModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('dashboard.schedule.update') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="date" :value="selectedDate">

                        <p class="text-sm font-medium text-slate-600 mb-2 capitalize" x-text="formatDate(selectedDate)">
                        </p>

                        <label class="flex items-center cursor-pointer gap-2 select-none mb-4">
                            <div class="relative">
                                <input type="checkbox" name="custom_open_checkbox" value="1" x-model="modalOpen"
                                    class="sr-only">
                                <input type="hidden" name="is_closed" :value="modalOpen ? '0' : '1'">
                                <div class="w-10 h-5 rounded-full transition-colors"
                                    :class="modalOpen ? 'bg-emerald-600' : 'bg-slate-300'"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                    :class="modalOpen ? 'translate-x-5' : 'translate-x-0'"></div>
                            </div>
                            <span class="text-sm font-medium" :class="modalOpen ? 'text-emerald-700' : 'text-gray-400'"
                                x-text="modalOpen ? 'Abierto' : 'Cerrado'"></span>
                        </label>

                        <div x-show="modalOpen" class="space-y-3">
                            <div class="flex items-center gap-2">
                                <input type="time" name="open_time" x-model="modalOpenTime"
                                    class="rounded-xl border-slate-300 text-sm focus:border-emerald-500 w-full">
                                <span class="text-gray-400 text-sm">a</span>
                                <input type="time" name="close_time" x-model="modalCloseTime"
                                    class="rounded-xl border-slate-300 text-sm focus:border-emerald-500 w-full">
                            </div>

                            <label class="flex items-center gap-3 cursor-pointer select-none py-2 px-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="relative">
                                    <input type="checkbox" x-model="modalHasBreak" class="sr-only">
                                    <div class="w-8 h-4 rounded-full transition-colors"
                                         :class="modalHasBreak ? 'bg-amber-500' : 'bg-slate-300'"></div>
                                    <div class="absolute top-0.5 left-0.5 w-3 h-3 bg-white rounded-full shadow transition-transform"
                                         :class="modalHasBreak ? 'translate-x-4' : 'translate-x-0'"></div>
                                </div>
                                <span class="text-xs font-bold" :class="modalHasBreak ? 'text-amber-600' : 'text-slate-500'">Añadir descanso</span>
                            </label>

                            <div x-show="modalHasBreak" class="flex items-center gap-2 pl-1">
                                <span class="text-xs text-amber-600 font-medium">Descanso:</span>
                                <input type="time" name="break_start" x-model="modalBreakStart"
                                    class="rounded-xl border-amber-200 text-sm focus:border-amber-500 w-full text-amber-700">
                                <span class="text-gray-400 text-sm">a</span>
                                <input type="time" name="break_end" x-model="modalBreakEnd"
                                    class="rounded-xl border-amber-200 text-sm focus:border-amber-500 w-full text-amber-700">
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end gap-3 mt-6">
                            <button type="button" @click="openConfigModal = false"
                                class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">Cancelar</button>
                            <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 text-sm font-medium rounded-xl shadow-md transition-all">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Añadir Cita Manual -->
            <div x-show="openAddModal" class="fixed inset-0 z-[60] overflow-y-auto"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">

                {{-- Overlay --}}
                <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-md" @click="openAddModal = false"></div>

                {{-- Scroll Container --}}
                <div class="flex items-start sm:items-center justify-center min-h-full p-4">
                    {{-- Card --}}
                    <div class="bg-white rounded-[32px] shadow-2xl w-full max-w-lg overflow-hidden relative border border-white/20 my-8 shadow-emerald-900/10"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        @click.outside="openAddModal = false">

                        {{-- Header --}}
                        <div
                            class="px-8 py-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                            <div>
                                <h3 class="font-display font-bold text-xl text-slate-800">Nueva Cita</h3>
                                <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest font-semibold">Registro
                                    manual</p>
                            </div>
                            <button @click="openAddModal = false"
                                class="w-10 h-10 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form action="{{ route('dashboard.bookings.store') }}" method="POST" class="p-8 space-y-5">
                            @csrf
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1.5 pl-1">Nombre
                                    o Título <span class="text-red-500">*</span></label>
                                <input type="text" name="customer_name" required placeholder="Ej: Cliente Regular"
                                    class="w-full rounded-2xl border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 transition-all">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1.5 pl-1">Servicio
                                    <span class="text-red-500">*</span></label>
                                <select name="service_id" required x-model="addModalServiceId"
                                    class="w-full rounded-2xl border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 transition-all">
                                    <option value="">Selecciona un servicio</option>
                                    @foreach($services as $svc)
                                        <option value="{{ $svc->id }}">{{ $svc->name }} ({{ $svc->duration_minutes }} min)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <input type="hidden" name="date" x-model="addModalDate">
                                <input type="hidden" name="time" x-model="addModalTime">

                                {{-- Calendario de 14 días --}}
                                <div x-show="addModalServiceId">
                                    <label
                                        class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 pl-1">Selecciona
                                        un día <span class="text-red-500">*</span></label>

                                    <div x-show="loadingCalendarAdd" class="flex justify-center py-8">
                                        <svg class="animate-spin w-8 h-8 text-emerald-500" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                    </div>

                                    <div x-show="!loadingCalendarAdd && addModalDays.length > 0"
                                        class="grid grid-cols-7 gap-1.5">
                                        <template x-for="day in addModalDays" :key="day.date">
                                            <button type="button" @click="addModalDate = day.date; addModalTime = ''"
                                                :disabled="day.status !== 'available'"
                                                class="aspect-square rounded-xl flex flex-col items-center justify-center transition-all select-none text-[10px]"
                                                :class="{
                                                    'bg-emerald-500 text-white cursor-pointer hover:bg-emerald-400': day.status === 'available' && addModalDate !== day.date,
                                                    'bg-emerald-600 text-white ring-2 ring-emerald-300 ring-offset-1 scale-105 shadow-md': day.status === 'available' && addModalDate === day.date,
                                                    'bg-slate-800 text-slate-500 cursor-default': day.status === 'closed',
                                                    'bg-red-400 text-white shrink-0 cursor-default opacity-50': day.status === 'full',
                                                }">
                                                <span class="font-medium opacity-80" x-text="dayName(day.date)"></span>
                                                <span class="text-xs font-bold" x-text="dayNum(day.date)"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                {{-- Slots de hora --}}
                                <div x-show="addModalDate" class="pt-2">
                                    <label
                                        class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 pl-1">Huecos
                                        para el <span x-text="formatDate(addModalDate)"
                                            class="text-emerald-600"></span></label>

                                    <div x-show="loadingSlots" class="flex justify-center py-4">
                                        <svg class="animate-spin w-5 h-5 text-emerald-500" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                    </div>

                                    <div x-show="!loadingSlots && addModalSlots.length === 0"
                                        class="text-center py-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                        <p class="text-[11px] text-slate-400">No hay disponibilidad automática para esta
                                            fecha</p>
                                        <p class="text-[10px] text-slate-300 mt-1">Usa el modo "Forzar" (próximamente) o
                                            cambia de día</p>
                                    </div>

                                    <div x-show="!loadingSlots && addModalSlots.length > 0"
                                        class="grid grid-cols-4 gap-2 max-h-40 overflow-y-auto p-1 custom-scrollbar">
                                        <template x-for="slot in addModalSlots" :key="slot">
                                            <button type="button" @click="addModalTime = slot"
                                                class="py-2.5 rounded-xl border-2 text-[11px] font-bold transition-all tabular-nums"
                                                :class="addModalTime === slot 
                                                    ? 'bg-emerald-600 border-emerald-600 text-white shadow-md' 
                                                    : 'border-slate-100 text-slate-600 hover:border-emerald-200 hover:bg-emerald-50/50'">
                                                <span x-text="slot"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Datos adicionales opcionales --}}
                            <div x-show="addModalTime" x-transition class="pt-4 border-t border-slate-50 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 pl-1">Teléfono</label>
                                        <input type="tel" name="customer_phone" placeholder="Ej: 600 000 000"
                                            class="w-full rounded-2xl border-slate-200 text-sm shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 transition-all">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 pl-1">Email</label>
                                        <input type="email" name="customer_email" placeholder="cliente@email.com"
                                            class="w-full rounded-2xl border-slate-200 text-sm shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 transition-all">
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <label
                                        class="text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Notas</label>
                                    <textarea name="notes" rows="2" placeholder="Notas adicionales..."
                                        class="w-full rounded-xl border-slate-200 bg-slate-50/50 py-2.5 text-sm focus:border-emerald-500 focus:ring-0 transition-all resize-none"></textarea>
                                </div>
                            </div>

                            {{-- Footer Actions --}}
                            <div class="mt-10 flex flex-col sm:flex-row items-center gap-3">
                                <button type="submit" :disabled="!addModalTime"
                                    class="w-full sm:flex-[2] bg-emerald-600 hover:bg-emerald-700 disabled:opacity-30 disabled:cursor-not-allowed text-white py-4 text-sm font-bold rounded-2xl shadow-xl shadow-emerald-200 transition-all active:scale-95">
                                    Confirmar y Agendar
                                </button>
                                <button type="button" @click="openAddModal = false"
                                    class="w-full sm:flex-1 py-4 text-sm font-bold bg-red-500 hover:bg-red-600 text-white rounded-2xl shadow-lg shadow-red-200 transition-all active:scale-95">
                                    Descartar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

                <!-- Modal Cancelar -->
                <div x-show="openCancelModal" style="display: none;"
                    class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4"
                    x-transition.opacity>
                    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden"
                        @click.outside="openCancelModal = false" x-transition>
                        <div class="p-6">
                            <div
                                class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center mb-4 mx-auto">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg text-slate-800 text-center mb-2">¿Cancelar Cita?</h3>
                            <p class="text-sm text-slate-500 text-center mb-6">Vas a cancelar la cita de <strong
                                    x-text="bookingToCancel ? bookingToCancel.customer_name : ''"></strong>. Visualmente el bloque volverá a
                                estar disponible.</p>

                            <form :action="'{{ url('dashboard/citas') }}/' + (bookingToCancel ? bookingToCancel.id : '')" method="POST">
                                @csrf
                                @method('DELETE')

                                <template x-if="bookingToCancel && bookingToCancel.customer_email">
                                    <label
                                        class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 mb-6 cursor-pointer hover:bg-slate-100 transition-colors">
                                        <input type="checkbox" name="blacklist" value="1"
                                            class="mt-0.5 rounded border-slate-300 text-red-600 focus:ring-red-500">
                                        <span class="text-sm text-slate-600 leading-tight">Añadir a lista negra (Si
                                            llega a 3, el email será bloqueado).</span>
                                    </label>
                                </template>

                                <div class="flex justify-between gap-3">
                                    <button type="button" @click="openCancelModal = false"
                                        class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Atrás</button>
                                    <button type="submit"
                                        class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 text-sm font-medium rounded-xl shadow-sm transition-colors">Sí,
                                        cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

        </div>

        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('ownerAgenda', (slug, type) => ({
                        slug: slug,
                        scheduleType: type,
                        days: [],
                        loading: true,
                        selectedDate: null,
                        selectedDayRef: null,
                        bookings: [],
                        loadingDay: false,
                        openAddModal: false,
                        openConfigModal: false,
                        openCancelModal: false,
                        modalOpen: true,
                        modalOpenTime: '09:00',
                        modalCloseTime: '19:00',
                        modalHasBreak: false,
                        modalBreakStart: '14:00',
                        modalBreakEnd: '15:00',
                        bookingToCancel: null,

                        // Nueva Cita Manual
                        addModalServiceId: '',
                        addModalDate: '',
                        addModalTime: '',
                        addModalSlots: [],
                        addModalDays: [],
                        loadingSlots: false,
                        loadingCalendarAdd: false,

                        async init() {
                            await this.loadCalendar();
                            this.$watch('addModalDate', () => this.loadAddModalSlots());
                            this.$watch('addModalServiceId', () => this.loadAddModalCalendar());
                        },

                        openCancel(bookingData) {
                            console.log('openCancel function called with data:', bookingData);
                            this.bookingToCancel = bookingData;
                            this.openCancelModal = true;
                            console.log('openCancelModal is now:', this.openCancelModal);
                        },

                        async loadAddModalCalendar() {
                            if (!this.addModalServiceId) {
                                this.addModalDays = [];
                                return;
                            }
                            this.loadingCalendarAdd = true;
                            this.addModalDate = '';
                            this.addModalTime = '';
                            this.addModalSlots = [];
                            try {
                                const res = await fetch('/' + this.slug + '/calendario?service_id=' + this.addModalServiceId);
                                this.addModalDays = await res.json();
                            } catch (_) {
                                this.addModalDays = [];
                            }
                            this.loadingCalendarAdd = false;
                        },

                        async loadAddModalSlots() {
                            if (!this.addModalServiceId || !this.addModalDate) {
                                this.addModalSlots = [];
                                return;
                            }
                            this.loadingSlots = true;
                            try {
                                const res = await fetch('/' + this.slug + '/disponibilidad?service_id=' + this.addModalServiceId + '&date=' + this.addModalDate);
                                this.addModalSlots = await res.json();
                            } catch (_) {
                                this.addModalSlots = [];
                            }
                            this.loadingSlots = false;
                        },
                        async loadCalendar() {
                            this.loading = true;
                            try {
                                const res = await fetch('/' + this.slug + '/agenda-propietario');
                                this.days = await res.json();
                            } catch (_) { }
                            this.loading = false;
                        },
                        async selectDay(day) {
                            if (this.selectedDate === day.date) {
                                this.selectedDate = null;
                                this.selectedDayRef = null;
                                this.bookings = [];
                                return;
                            }
                            this.selectedDate = day.date;
                            this.selectedDayRef = day;
                            this.loadingDay = true;
                            this.bookings = [];
                            try {
                                const res = await fetch('/' + this.slug + '/dia?date=' + day.date);
                                this.bookings = await res.json();
                            } catch (_) { }
                            this.loadingDay = false;
                        },
                        openConfig() {
                            if (this.selectedDayRef) {
                                this.modalOpen = this.selectedDayRef.status === 'open';
                                this.modalOpenTime = this.selectedDayRef.open_time || '09:00';
                                this.modalCloseTime = this.selectedDayRef.close_time || '19:00';
                                this.modalHasBreak = !!(this.selectedDayRef.break_start && this.selectedDayRef.break_end);
                                this.modalBreakStart = this.selectedDayRef.break_start || '14:00';
                                this.modalBreakEnd = this.selectedDayRef.break_end || '15:00';
                            }
                            this.openConfigModal = true;
                        },
                        dayName(dateStr) {
                            return ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'][new Date(dateStr + 'T12:00:00').getDay()];
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
                    }));
                });
            </script>
        @endpush
</x-app-layout>