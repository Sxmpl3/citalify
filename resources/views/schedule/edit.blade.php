<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800 leading-tight">
                    Editar Horario Semanal
                </h2>
                <p class="text-sm text-slate-400 mt-0.5">
                    Modifica las horas de apertura, cierre y descansos de cada día.
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
        $dayNames = [0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'];
        $allDays  = [1, 2, 3, 4, 5, 6, 0]; // Lunes → Domingo
        $indexed  = $schedules->keyBy('day_of_week');
    @endphp

    <div class="py-8"
         x-data="{
             days: [
                 @foreach($allDays as $d)
                 {
                     day:        {{ $d }},
                     label:      '{{ $dayNames[$d] }}',
                     active:     {{ $indexed->has($d) ? 'true' : 'false' }},
                     open:       '{{ $indexed->has($d) ? $indexed->get($d)->open_time : '09:00' }}',
                     close:      '{{ $indexed->has($d) ? $indexed->get($d)->close_time : '19:00' }}',
                     hasBreak:   {{ ($indexed->has($d) && $indexed->get($d)->break_start) ? 'true' : 'false' }},
                     breakStart: '{{ ($indexed->has($d) && $indexed->get($d)->break_start) ? $indexed->get($d)->break_start : '14:00' }}',
                     breakEnd:   '{{ ($indexed->has($d) && $indexed->get($d)->break_end)   ? $indexed->get($d)->break_end   : '15:00' }}',
                 },
                 @endforeach
             ],
             get activeDays() { return this.days.filter(d => d.active); },
             handleSubmit() {
                 const form = this.$refs.scheduleForm;
                 form.querySelectorAll('input[data-dyn]').forEach(e => e.remove());

                 const add = (name, val) => {
                     const input = document.createElement('input');
                     input.type  = 'hidden';
                     input.name  = name;
                     input.value = val;
                     input.dataset.dyn = '1';
                     form.appendChild(input);
                 };

                 let error = false;
                 let idx   = 0;

                 this.activeDays.forEach(d => {
                     if (d.open >= d.close) {
                         alert('El horario de ' + d.label + ' es inválido (apertura >= cierre).');
                         error = true;
                         return;
                     }
                     if (d.hasBreak && d.breakStart >= d.breakEnd) {
                         alert('El descanso de ' + d.label + ' debe terminar después de empezar.');
                         error = true;
                         return;
                     }
                     add('schedules[' + idx + '][day]',   d.day);
                     add('schedules[' + idx + '][open]',  d.open);
                     add('schedules[' + idx + '][close]', d.close);
                     if (d.hasBreak) {
                         add('schedules[' + idx + '][break_start]', d.breakStart);
                         add('schedules[' + idx + '][break_end]',   d.breakEnd);
                     }
                     idx++;
                 });

                 if (error) return;
                 if (this.activeDays.length === 0) {
                     alert('Debes tener al menos un día activo.');
                     return;
                 }

                 form.submit();
             }
         }"
         x-cloak>
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Alerts --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-4 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
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

            {{-- Main card --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-50 bg-slate-50/50">
                    <h3 class="font-display font-bold text-slate-800 text-lg">Horario semanal</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Activa los días que abres y configura las horas. Los días desactivados aparecerán como <strong>cerrado</strong> para tus clientes.</p>
                </div>

                <form x-ref="scheduleForm"
                      method="POST"
                      action="{{ route('schedule.update') }}"
                      @submit.prevent="handleSubmit()"
                      class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-3">
                        <template x-for="d in days" :key="d.day">
                            <div class="flex flex-col gap-2 p-4 rounded-2xl border transition-all"
                                 :class="d.active ? 'border-emerald-200 bg-emerald-50/60' : 'border-slate-200 bg-slate-50'">

                                {{-- Row principal --}}
                                <div class="flex flex-col md:flex-row items-start md:items-center gap-4">

                                    {{-- Titulo día + Toggle --}}
                                    <div class="flex items-center justify-between w-full md:w-32 shrink-0">
                                        <label class="flex items-center cursor-pointer gap-3 select-none">
                                            <div class="relative">
                                                <input type="checkbox" x-model="d.active" class="sr-only">
                                                <div class="w-10 h-5 rounded-full transition-colors"
                                                     :class="d.active ? 'bg-emerald-600' : 'bg-slate-300'"></div>
                                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                                     :class="d.active ? 'translate-x-5' : 'translate-x-0'"></div>
                                            </div>
                                            <span class="text-base font-bold capitalize"
                                                  :class="d.active ? 'text-slate-800' : 'text-slate-400'"
                                                  x-text="d.label"></span>
                                        </label>
                                        
                                        <template x-if="!d.active">
                                            <span class="text-xs text-slate-400 font-medium italic md:hidden">Cerrado</span>
                                        </template>
                                    </div>

                                    {{-- Horas y Descanso --}}
                                    <template x-if="d.active">
                                        <div class="w-full flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 flex-1">
                                            {{-- Bloque Horas --}}
                                            <div class="grid grid-cols-2 sm:flex sm:items-center gap-3 flex-1">
                                                <div class="space-y-1">
                                                    <span class="text-[10px] sm:hidden font-bold text-slate-400 uppercase tracking-widest pl-1">Apertura</span>
                                                    <input type="time" x-model="d.open" 
                                                           class="w-full sm:w-auto rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white py-2">
                                                </div>
                                                <div class="space-y-1">
                                                    <span class="text-[10px] sm:hidden font-bold text-slate-400 uppercase tracking-widest pl-1">Cierre</span>
                                                    <input type="time" x-model="d.close" 
                                                           class="w-full sm:w-auto rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white py-2">
                                                </div>
                                            </div>

                                            {{-- Toggle Descanso --}}
                                            <label class="flex items-center gap-3 cursor-pointer select-none py-2 px-3 sm:px-0 bg-slate-50 sm:bg-transparent rounded-xl sm:rounded-none border border-slate-100 sm:border-0 shadow-sm sm:shadow-none">
                                                <div class="relative">
                                                    <input type="checkbox" x-model="d.hasBreak" class="sr-only">
                                                    <div class="w-8 h-4 rounded-full transition-colors"
                                                         :class="d.hasBreak ? 'bg-amber-500' : 'bg-slate-300'"></div>
                                                    <div class="absolute top-0.5 left-0.5 w-3 h-3 bg-white rounded-full shadow transition-transform"
                                                         :class="d.hasBreak ? 'translate-x-4' : 'translate-x-0'"></div>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-bold" :class="d.hasBreak ? 'text-amber-600' : 'text-slate-500'">Descanso</span>
                                                    <span class="text-[9px] text-slate-400 leading-none sm:hidden" x-text="d.hasBreak ? 'Activado' : 'Desactivado'"></span>
                                                </div>
                                            </label>
                                        </div>
                                    </template>

                                    <template x-if="!d.active">
                                        <span class="hidden md:inline-block text-sm text-slate-400 font-medium italic ml-1">Cerrado</span>
                                    </template>
                                </div>

                                {{-- Franja de descanso extensible --}}
                                <div x-show="d.active && d.hasBreak"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="mt-2 pl-0 md:pl-36">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 p-3 bg-amber-50/50 border border-amber-100 rounded-2xl shadow-inner-sm">
                                        <div class="grid grid-cols-2 gap-3 w-full sm:w-auto">
                                            <div class="space-y-1">
                                                <span class="text-[9px] font-bold text-amber-500 uppercase tracking-widest pl-1">Desde</span>
                                                <input type="time" x-model="d.breakStart"
                                                       class="w-full sm:w-auto rounded-xl border-amber-200 text-sm focus:border-amber-500 focus:ring-amber-500 bg-white text-amber-700 py-1.5 px-3">
                                            </div>
                                            <div class="space-y-1">
                                                <span class="text-[9px] font-bold text-amber-500 uppercase tracking-widest pl-1">Hasta</span>
                                                <input type="time" x-model="d.breakEnd"
                                                       class="w-full sm:w-auto rounded-xl border-amber-200 text-sm focus:border-amber-500 focus:ring-amber-500 bg-white text-amber-700 py-1.5 px-3">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>       </template>
                    </div>

                    {{-- Info helper --}}
                    <p class="text-xs text-slate-400 mt-4">
                        Necesitas al menos un día activo. Los cambios afectarán a las nuevas reservas.
                    </p>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-6 border-t border-slate-100">
                        <a href="{{ route('dashboard') }}"
                           class="flex-1 text-center py-3 px-6 border-2 border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all">
                            Cancelar
                        </a>
                        <button type="submit"
                                :disabled="activeDays.length === 0"
                                class="flex-1 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed text-white font-semibold py-3 px-6 rounded-xl shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>

            {{-- Aviso --}}
            <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 text-sm text-amber-800 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <div>
                    <p class="font-semibold mb-0.5">Los cambios afectan a futuras reservas</p>
                    <p class="text-amber-700 text-xs">Las citas ya existentes <strong>no se cancelarán</strong> automáticamente. Si cierras un día con citas activas, revísalas manualmente en el dashboard.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
