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

    <div class="py-8" x-data="ownerAgenda('{{ auth()->user()->business_slug }}', '{{ auth()->user()->schedule_type }}')" x-cloak>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-4 text-sm font-medium">
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

            {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Hoy --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-slate-500">Citas hoy</p>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-slate-800">{{ $stats['today'] }}</p>
                </div>
                {{-- Pendientes --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-slate-500">Pendientes</p>
                        <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-slate-800">{{ $stats['pending'] }}</p>
                </div>
                {{-- Esta semana --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-slate-500">Esta semana</p>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-slate-800">{{ $stats['week'] }}</p>
                </div>
                {{-- Este mes --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-slate-500">Este mes</p>
                        <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
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
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-sm font-medium text-slate-600">No hay ninguna cita pendiente para hoy.</p>
                        <p class="text-xs text-slate-400 mt-1">Disfruta de tu tiempo libre o revisa los próximos {{ auth()->user()->booking_days_ahead }} días abajo.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($todayBookings as $booking)
                            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex gap-4 items-start relative overflow-hidden group">
                                <div class="text-center w-12 shrink-0 pt-0.5">
                                    <p class="text-lg font-bold text-slate-700 leading-none">{{ \Carbon\Carbon::parse($booking->starts_at)->format('H:i') }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($booking->ends_at)->format('H:i') }}</p>
                                </div>
                                <div class="w-1 h-full absolute left-16 top-0 bg-emerald-400 shrink-0"></div>
                                <div class="flex-1 min-w-0 pl-3">
                                    <p class="font-semibold text-slate-800 truncate">{{ $booking->customer_name }}</p>
                                    <p class="text-sm text-slate-500 truncate">{{ $booking->service->name ?? 'Servicio' }}</p>
                                    @if($booking->notes)
                                        <p class="text-xs text-slate-400 mt-1 truncate">"{{ $booking->notes }}"</p>
                                    @endif
                                </div>
                                <div class="shrink-0 flex items-center">
                                    <button @click="bookingToCancel = {
                                        id: {{ $booking->id }},
                                        customer_name: '{{ addslashes($booking->customer_name) }}',
                                        customer_email: '{{ addslashes($booking->customer_email ?? '') }}'
                                    }" class="text-xs text-red-500 hover:text-red-700 underline underline-offset-2 opacity-0 group-hover:opacity-100 transition-opacity font-medium bg-red-50 px-2 py-1 rounded">
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
                    <p class="text-sm text-slate-500 mt-0.5">Vista de {{ auth()->user()->booking_days_ahead }} días. Haz click en un día para ver y gestionar citas.</p>
                </div>
                <button @click="openAddModal = true" class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/20 px-4 py-2.5 text-sm font-medium rounded-xl shrink-0 transition-transform hover:scale-105 self-start sm:self-auto">
                    + Nueva cita
                </button>
            </div>

            <div class="flex items-center gap-4 text-xs text-slate-500 mb-2">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> Con citas</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded border-2 border-slate-200 inline-block"></span> Libre</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-slate-800 inline-block"></span> Cerrado</span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 relative min-h-[140px]">
                <div x-show="loading" class="flex justify-center absolute inset-0 items-center bg-white/60 z-10 rounded-2xl">
                    <svg class="animate-spin w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </div>
                
                <div class="grid grid-cols-7 gap-1.5 sm:gap-2">
                    <template x-for="day in days" :key="day.date">
                        <button
                            type="button"
                            @click="selectDay(day)"
                            class="aspect-square rounded-xl flex flex-col items-center justify-center transition-all select-none relative"
                            :class="{
                                'bg-emerald-500 hover:bg-emerald-400 text-white shadow-sm': day.count > 0 && selectedDate !== day.date,
                                'bg-emerald-600 text-white ring-2 ring-emerald-300 ring-offset-1 scale-105': day.count > 0 && selectedDate === day.date,
                                'bg-white border-2 border-slate-200 hover:border-emerald-300 text-slate-600': day.count === 0 && day.status === 'open' && selectedDate !== day.date,
                                'bg-emerald-50 border-2 border-emerald-400 text-emerald-700': day.count === 0 && day.status === 'open' && selectedDate === day.date,
                                'bg-slate-800 text-slate-500 cursor-default': day.status === 'closed',
                            }"
                        >
                            <span class="text-xs font-medium leading-none mb-0.5 opacity-80" x-text="dayName(day.date)"></span>
                            <span class="text-base sm:text-lg font-bold leading-none" x-text="dayNum(day.date)"></span>
                            <template x-if="day.count > 0">
                                <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-white text-emerald-600 text-xs font-bold flex items-center justify-center shadow-sm ring-1 ring-emerald-100" x-text="day.count"></span>
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
                            <h2 class="font-display font-bold text-slate-800 text-base capitalize" x-text="formatDate(selectedDate)"></h2>
                            <template x-if="scheduleType === 'custom'">
                                <button @click="openConfig()" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-2.5 py-1 rounded-md font-medium transition-colors">
                                    Configurar Horario
                                </button>
                            </template>
                        </div>
                        <button type="button" @click="selectedDate = null; bookings = []" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div x-show="loadingDay" class="flex justify-center py-10">
                        <svg class="animate-spin w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </div>

                    <div x-show="!loadingDay && bookings.length === 0" class="flex flex-col items-center py-10 text-center px-6">
                        <p class="text-sm font-medium text-slate-500">Sin citas este día</p>
                        <p class="text-xs text-slate-400 mt-1">Los clientes pueden reservar en tu página pública o puedes crear la cita manualmente.</p>
                    </div>

                    <div x-show="!loadingDay && bookings.length > 0" class="divide-y divide-slate-50">
                        <template x-for="b in bookings" :key="b.id">
                            <div class="px-4 sm:px-5 py-4 hover:bg-slate-50/60 transition-colors flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex items-start gap-4 flex-1 min-w-0">
                                    <div class="text-center w-14 shrink-0 pt-0.5">
                                        <p class="text-base font-bold text-slate-700 tabular-nums leading-none" x-text="b.starts_at"></p>
                                        <p class="text-xs text-slate-400 tabular-nums" x-text="b.ends_at"></p>
                                    </div>
                                    <div class="w-1 h-10 rounded-full bg-emerald-400 shrink-0 mt-0.5"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-slate-800 truncate" x-text="b.customer_name"></p>
                                        <p class="text-sm text-slate-500 truncate" x-text="b.service"></p>
                                        <template x-if="b.customer_email">
                                            <p class="text-xs text-slate-400 truncate" x-text="'Email: ' + b.customer_email"></p>
                                        </template>
                                        <template x-if="b.notes">
                                            <p class="text-xs text-slate-400 mt-0.5 truncate" x-text="'Nota: ' + b.notes"></p>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex sm:flex-col items-center sm:items-end gap-3 sm:gap-2 pl-[4.5rem] sm:pl-0">
                                    <span x-show="b.status === 'confirmed'" class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700">Confirmada</span>
                                    <span x-show="b.status === 'pending'" class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700">Pendiente</span>
                                    
                                    <button @click="bookingToCancel = b" class="text-xs text-red-500 hover:text-red-700 underline underline-offset-2 font-medium">
                                        Cancelar cita
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Modales --}}
            <!-- Modal Configurar Horario Especial -->
            <div x-show="openConfigModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-transition.opacity>
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden" @click.outside="openConfigModal = false" x-transition>
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-slate-800">Horario del Día</h3>
                        <button @click="openConfigModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form action="{{ route('dashboard.schedule.update') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="date" :value="selectedDate">
                        
                        <p class="text-sm font-medium text-slate-600 mb-2 capitalize" x-text="formatDate(selectedDate)"></p>
                        
                        <label class="flex items-center cursor-pointer gap-2 select-none mb-4">
                            <div class="relative">
                                <input type="checkbox" name="custom_open_checkbox" value="1" x-model="modalOpen" class="sr-only">
                                <input type="hidden" name="is_closed" :value="modalOpen ? '0' : '1'">
                                <div class="w-10 h-5 rounded-full transition-colors" :class="modalOpen ? 'bg-emerald-600' : 'bg-slate-300'"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform" :class="modalOpen ? 'translate-x-5' : 'translate-x-0'"></div>
                            </div>
                            <span class="text-sm font-medium" :class="modalOpen ? 'text-emerald-700' : 'text-gray-400'" x-text="modalOpen ? 'Abierto' : 'Cerrado'"></span>
                        </label>

                        <div x-show="modalOpen" class="flex items-center gap-2">
                            <input type="time" name="open_time" x-model="modalOpenTime" class="rounded-xl border-slate-300 text-sm focus:border-emerald-500 w-full">
                            <span class="text-gray-400 text-sm">a</span>
                            <input type="time" name="close_time" x-model="modalCloseTime" class="rounded-xl border-slate-300 text-sm focus:border-emerald-500 w-full">
                        </div>

                        <div class="pt-2 flex justify-end gap-3 mt-6">
                            <button type="button" @click="openConfigModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">Cancelar</button>
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 text-sm font-medium rounded-xl shadow-md transition-all">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Añadir -->
            <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-transition.opacity>
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.outside="openAddModal = false" x-transition>
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-slate-800">Añadir Cita Manual</h3>
                        <button @click="openAddModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form action="{{ route('dashboard.bookings.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nombre o Título <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Fecha <span class="text-red-500">*</span></label>
                                <input type="date" name="date" required :value="selectedDate || ''" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Hora inicio <span class="text-red-500">*</span></label>
                                <input type="time" name="time" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Servicio Asignado <span class="text-red-500">*</span></label>
                            <select name="service_id" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id }}">{{ $svc->name }} ({{ $svc->duration_minutes }} min)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="pt-2 flex justify-end gap-3 mt-6">
                            <button type="button" @click="openAddModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">Cancelar</button>
                            <button type="submit" class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white px-5 py-2.5 text-sm font-medium rounded-xl shadow-md transition-all">Crear Cita</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Cancelar -->
            <div x-show="bookingToCancel" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-transition.opacity>
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden" @click.outside="bookingToCancel = null" x-transition>
                    <div class="p-6">
                        <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 class="font-bold text-lg text-slate-800 text-center mb-2">¿Cancelar Cita?</h3>
                        <p class="text-sm text-slate-500 text-center mb-6">Vas a cancelar la cita de <strong x-text="bookingToCancel?.customer_name"></strong>. Visualmente el bloque volverá a estar disponible.</p>
                        
                        <form :action="'{{ url('dashboard/citas') }}/' + bookingToCancel?.id" method="POST">
                            @csrf
                            @method('DELETE')
                            
                            <template x-if="bookingToCancel?.customer_email">
                                <label class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 mb-6 cursor-pointer hover:bg-slate-100 transition-colors">
                                    <input type="checkbox" name="blacklist" value="1" class="mt-0.5 rounded border-slate-300 text-red-600 focus:ring-red-500">
                                    <span class="text-sm text-slate-600 leading-tight">Añadir a lista negra (Si llega a 3, el email será bloqueado).</span>
                                </label>
                            </template>

                            <div class="flex justify-between gap-3">
                                <button type="button" @click="bookingToCancel = null" class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Atrás</button>
                                <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 text-sm font-medium rounded-xl shadow-sm transition-colors">Sí, cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tarjeta página pública rediseñada --}}
            @if(auth()->user()->business_slug)
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 mt-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <div>
                            <p class="font-medium text-slate-800 text-sm">Tu enlace de reservas</p>
                            <p class="text-slate-500 text-xs mt-0.5">Comparte este enlace para que tus clientes reserven online.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 w-full md:w-auto" x-data="{ copied: false }">
                        <div class="flex items-center flex-1">
                            <input type="text" readonly value="citalify.es/{{ auth()->user()->business_slug }}" 
                                   class="text-xs font-mono bg-white border border-slate-200 text-slate-600 px-3 py-2 md:py-2.5 rounded-l-lg w-full md:w-64 focus:outline-none focus:ring-0">
                            <button @click="navigator.clipboard.writeText('https://citalify.es/{{ auth()->user()->business_slug }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                    class="bg-white hover:bg-slate-50 border border-l-0 border-slate-200 text-slate-600 px-4 py-2 md:py-2.5 text-xs font-medium rounded-r-lg transition-colors flex items-center gap-1.5 focus:outline-none">
                                <span x-show="!copied">Copiar</span>
                                <span x-show="copied" class="text-emerald-600" style="display: none;">¡Copiado!</span>
                            </button>
                        </div>
                        <a href="{{ route('booking.show', auth()->user()->business_slug) }}" target="_blank" 
                           class="p-2 md:p-2.5 text-slate-400 hover:text-emerald-600 bg-white border border-slate-200 rounded-lg hover:border-emerald-200 transition-colors" title="Abrir web">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>
            @endif

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
                modalOpen: true,
                modalOpenTime: '09:00',
                modalCloseTime: '19:00',
                bookingToCancel: null,

                async init() {
                    await this.loadCalendar();
                },
                async loadCalendar() {
                    this.loading = true;
                    try {
                        const res = await fetch('/' + this.slug + '/agenda-propietario');
                        this.days = await res.json();
                    } catch (_) {}
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
                    } catch (_) {}
                    this.loadingDay = false;
                },
                openConfig() {
                    if (this.selectedDayRef) {
                        this.modalOpen = this.selectedDayRef.status === 'open';
                        this.modalOpenTime = this.selectedDayRef.open_time || '09:00';
                        this.modalCloseTime = this.selectedDayRef.close_time || '19:00';
                    }
                    this.openConfigModal = true;
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
            }));
        });
    </script>
    @endpush
</x-app-layout>

