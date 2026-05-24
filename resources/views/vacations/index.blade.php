<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800 leading-tight">
                    Vacaciones
                </h2>
                <p class="text-sm text-slate-400 mt-0.5">
                    Marca días completos o franjas horarias en las que el negocio estará cerrado.
                </p>
            </div>
            <a href="{{ route('dashboard') }}"
               class="text-sm text-slate-500 hover:text-slate-800 flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al dashboard
            </a>
        </div>
    </x-slot>

    @php
        $tz = auth()->user()->timezone ?? 'Europe/Madrid';
        $today = \Carbon\Carbon::today($tz);
        $endOfYear = \Carbon\Carbon::create($today->year, 12, 31, 0, 0, 0, $tz);

        $months = [];
        $cursor = $today->copy()->startOfMonth();
        while ($cursor->lte($endOfYear)) {
            $months[] = $cursor->copy();
            $cursor->addMonth();
        }

        $dayLabels = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
        $monthLabels = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        // Build a JS-ready map: date -> [{id, full, start, end, label}, ...]
        $vacationMap = [];
        foreach ($byDate as $dateStr => $items) {
            $vacationMap[$dateStr] = $items->map(function ($v) {
                return [
                    'id'    => $v->id,
                    'full'  => $v->isFullDay(),
                    'start' => $v->start_time ? \Carbon\Carbon::parse($v->start_time)->format('H:i') : null,
                    'end'   => $v->end_time   ? \Carbon\Carbon::parse($v->end_time)->format('H:i')   : null,
                ];
            })->values()->all();
        }
    @endphp

    <div class="py-8"
         x-data="vacationsModal(@js($vacationMap), '{{ route('vacations.store') }}', '{{ csrf_token() }}')">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Alerts --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-4 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 text-sm font-medium">
                    {{ session('error') }}
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

            {{-- Info --}}
            <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 text-sm text-amber-800 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <div>
                    <p class="font-semibold mb-0.5">Importante</p>
                    <p class="text-amber-700 text-xs">Al añadir vacaciones (día completo o franja), las citas existentes en ese rango se <strong>cancelarán automáticamente</strong> y se notificará por email a los clientes.</p>
                </div>
            </div>

            {{-- Leyenda --}}
            <div class="flex items-center gap-4 text-xs text-slate-500 flex-wrap">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-rose-500 inline-block"></span> Día completo</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-amber-400 inline-block"></span> Franja horaria</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded border-2 border-slate-200 inline-block"></span> Disponible</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-slate-100 inline-block"></span> Pasado</span>
            </div>

            {{-- Calendarios por mes --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($months as $month)
                    @php
                        $firstDay = $month->copy()->startOfMonth();
                        $lastDay  = $month->copy()->endOfMonth();
                        $startWeekday = ($firstDay->dayOfWeek + 6) % 7;
                        $daysInMonth  = $lastDay->day;
                    @endphp

                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-slate-50 bg-slate-50/50">
                            <h3 class="font-display font-bold text-slate-800 text-base">
                                {{ $monthLabels[$month->month] }} {{ $month->year }}
                            </h3>
                        </div>

                        <div class="p-4">
                            <div class="grid grid-cols-7 gap-1 mb-2">
                                @foreach($dayLabels as $dl)
                                    <div class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest py-1">{{ $dl }}</div>
                                @endforeach
                            </div>

                            <div class="grid grid-cols-7 gap-1">
                                @for($i = 0; $i < $startWeekday; $i++)
                                    <div></div>
                                @endfor

                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $date     = $month->copy()->day($d);
                                        $dateStr  = $date->toDateString();
                                        $isPast   = $date->lt($today);
                                        $items    = $byDate->get($dateStr, collect());
                                        $hasFull  = $items->contains(fn($v) => $v->isFullDay());
                                        $hasRange = $items->isNotEmpty() && !$hasFull;
                                    @endphp

                                    @if($isPast)
                                        <div class="aspect-square rounded-lg flex items-center justify-center bg-slate-50 text-slate-300 text-xs">
                                            {{ $d }}
                                        </div>
                                    @else
                                        <button type="button"
                                            @click="openModal('{{ $dateStr }}', '{{ $date->locale('es')->isoFormat('dddd D [de] MMMM') }}')"
                                            title="Gestionar {{ $date->format('d/m/Y') }}"
                                            @class([
                                                'aspect-square rounded-lg flex items-center justify-center text-xs font-bold shadow-sm transition-all hover:scale-105',
                                                'bg-rose-500 hover:bg-rose-600 text-white' => $hasFull,
                                                'bg-amber-400 hover:bg-amber-500 text-white' => $hasRange,
                                                'bg-white border-2 border-slate-200 hover:border-rose-300 hover:bg-rose-50 text-slate-600 hover:text-rose-600 font-medium' => !$hasFull && !$hasRange,
                                            ])>
                                            {{ $d }}
                                        </button>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Lista resumen --}}
            @if($vacations->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                        <h3 class="font-display font-bold text-slate-800 text-base">Vacaciones programadas</h3>
                        <span class="text-xs text-slate-500">{{ $vacations->count() }} {{ $vacations->count() === 1 ? 'entrada' : 'entradas' }}</span>
                    </div>
                    <div class="p-4 flex flex-wrap gap-2">
                        @foreach($vacations as $v)
                            @php
                                $parsed = \Carbon\Carbon::parse($v->date->toDateString());
                                $label  = $parsed->locale('es')->isoFormat('ddd D MMM');
                                if (!$v->isFullDay()) {
                                    $label .= ' · ' . \Carbon\Carbon::parse($v->start_time)->format('H:i')
                                            . '–' . \Carbon\Carbon::parse($v->end_time)->format('H:i');
                                }
                                $isFull = $v->isFullDay();
                            @endphp
                            <form method="POST" action="{{ route('vacations.destroy', $v->id) }}" class="inline-flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    @class([
                                        'group inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-medium transition-colors',
                                        'bg-rose-50 hover:bg-rose-100 text-rose-700' => $isFull,
                                        'bg-amber-50 hover:bg-amber-100 text-amber-700' => !$isFull,
                                    ])>
                                    {{ $label }}
                                    <svg class="w-3.5 h-3.5 opacity-50 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center">
                    <p class="text-sm font-medium text-slate-600">Aún no has marcado ningún día como vacaciones.</p>
                    <p class="text-xs text-slate-400 mt-1">Haz click en cualquier día del calendario para añadir un día completo o una franja horaria.</p>
                </div>
            @endif
        </div>

        {{-- Modal --}}
        <div x-show="open" x-cloak
             @keydown.escape.window="close()"
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition.opacity>
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="close()"></div>

            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto"
                 @click.stop
                 x-transition>
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-display font-bold text-slate-800 text-base capitalize" x-text="dateLabel"></h3>
                        <p class="text-xs text-slate-400 mt-0.5">Gestiona las vacaciones de este día</p>
                    </div>
                    <button type="button" @click="close()" class="text-slate-400 hover:text-slate-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Existing vacations for this day --}}
                    <template x-if="items.length > 0">
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Marcado actualmente</h4>
                            <div class="space-y-2">
                                <template x-for="item in items" :key="item.id">
                                    <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 px-3 py-2"
                                         :class="item.full ? 'bg-rose-50' : 'bg-amber-50'">
                                        <span class="text-sm font-medium"
                                              :class="item.full ? 'text-rose-700' : 'text-amber-700'">
                                            <template x-if="item.full">
                                                <span>Día completo</span>
                                            </template>
                                            <template x-if="!item.full">
                                                <span x-text="item.start + ' – ' + item.end"></span>
                                            </template>
                                        </span>
                                        <form method="POST" :action="`/vacaciones/${item.id}`" class="inline-flex">
                                            <input type="hidden" name="_token" :value="csrf">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit"
                                                class="text-xs font-semibold px-2 py-1 rounded-lg text-slate-500 hover:text-rose-700 hover:bg-white transition-colors">
                                                Quitar
                                            </button>
                                        </form>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Add form --}}
                    <template x-if="!hasFullDay">
                        <form method="POST" :action="storeUrl" class="space-y-4">
                            <input type="hidden" name="_token" :value="csrf">
                            <input type="hidden" name="date" :value="dateStr">

                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Añadir vacaciones</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="mode" value="full" x-model="mode" class="sr-only peer">
                                        <div class="text-center text-sm font-medium rounded-xl px-3 py-2 border-2 transition-all
                                            border-slate-200 text-slate-600
                                            peer-checked:border-rose-400 peer-checked:bg-rose-50 peer-checked:text-rose-700">
                                            Día completo
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="mode" value="range" x-model="mode" class="sr-only peer">
                                        <div class="text-center text-sm font-medium rounded-xl px-3 py-2 border-2 transition-all
                                            border-slate-200 text-slate-600
                                            peer-checked:border-amber-400 peer-checked:bg-amber-50 peer-checked:text-amber-700">
                                            Franja horaria
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div x-show="mode === 'range'" x-transition class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Desde</label>
                                    <input type="time" name="start_time" x-model="startTime" step="900"
                                           class="w-full rounded-xl border-slate-200 text-sm focus:border-amber-400 focus:ring-amber-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Hasta</label>
                                    <input type="time" name="end_time" x-model="endTime" step="900"
                                           class="w-full rounded-xl border-slate-200 text-sm focus:border-amber-400 focus:ring-amber-400">
                                </div>
                            </div>

                            <div x-show="rangeError" class="text-xs text-rose-600" x-text="rangeError"></div>

                            <button type="submit"
                                :disabled="!canSubmit"
                                class="w-full rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold py-2.5 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-text="mode === 'full' ? 'Marcar día completo' : 'Añadir franja'"></span>
                            </button>
                        </form>
                    </template>

                    <template x-if="hasFullDay">
                        <p class="text-xs text-slate-500 text-center">Este día está marcado como completo. Elimina la entrada para añadir franjas.</p>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <style>[x-cloak]{display:none!important}</style>

    <script>
        function vacationsModal(vacationMap, storeUrl, csrf) {
            return {
                open: false,
                dateStr: '',
                dateLabel: '',
                items: [],
                mode: 'full',
                startTime: '09:00',
                endTime: '13:00',
                storeUrl,
                csrf,
                map: vacationMap || {},
                openModal(dateStr, dateLabel) {
                    this.dateStr = dateStr;
                    this.dateLabel = dateLabel;
                    this.items = this.map[dateStr] || [];
                    this.mode = 'full';
                    this.startTime = '09:00';
                    this.endTime = '13:00';
                    this.open = true;
                },
                close() {
                    this.open = false;
                },
                get hasFullDay() {
                    return this.items.some(i => i.full);
                },
                get rangeError() {
                    if (this.mode !== 'range') return '';
                    if (!this.startTime || !this.endTime) return '';
                    if (this.endTime <= this.startTime) {
                        return 'La hora de fin debe ser posterior a la de inicio.';
                    }
                    return '';
                },
                get canSubmit() {
                    if (this.mode === 'full') return true;
                    return this.startTime && this.endTime && this.endTime > this.startTime;
                },
            };
        }
    </script>
</x-app-layout>
