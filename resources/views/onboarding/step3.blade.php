<x-onboarding-layout :currentStep="3">

    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-8"
         x-data="{
             days: [
                 { day: 1, label: 'Lunes',     active: true,  open: '09:00', close: '19:00' },
                 { day: 2, label: 'Martes',    active: true,  open: '09:00', close: '19:00' },
                 { day: 3, label: 'Miércoles', active: true,  open: '09:00', close: '19:00' },
                 { day: 4, label: 'Jueves',    active: true,  open: '09:00', close: '19:00' },
                 { day: 5, label: 'Viernes',   active: true,  open: '09:00', close: '19:00' },
                 { day: 6, label: 'Sábado',    active: false, open: '10:00', close: '14:00' },
                 { day: 0, label: 'Domingo',   active: false, open: '10:00', close: '14:00' },
             ],
             get activeDays() { return this.days.filter(d => d.active) },
             handleSubmit() {
                 const form = this.$refs.scheduleForm;
                 form.querySelectorAll('input[data-dyn]').forEach(e => e.remove());
                 let idx = 0;
                 this.activeDays.forEach(d => {
                     const add = (name, val) => {
                         const input = document.createElement('input');
                         input.type = 'hidden';
                         input.name = name;
                         input.value = val;
                         input.dataset.dyn = '1';
                         form.appendChild(input);
                     };
                     add('schedules[' + idx + '][day]', d.day);
                     add('schedules[' + idx + '][open]', d.open);
                     add('schedules[' + idx + '][close]', d.close);
                     idx++;
                 });
                 form.submit();
             }
         }">

        <h1 class="text-2xl font-bold text-gray-900 mb-1">¿Cuándo abres?</h1>
        <p class="text-gray-500 mb-6">Configura tu horario semanal. Podrás ajustarlo en cualquier momento.</p>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form x-ref="scheduleForm" method="POST" action="{{ route('onboarding.store3') }}" @submit.prevent="handleSubmit()">
            @csrf

            <div class="space-y-2 mb-8">
                <template x-for="d in days" :key="d.day">
                    <div class="flex items-center gap-4 p-3 rounded-lg border"
                         :class="d.active ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-slate-50'">

                        {{-- Toggle día --}}
                        <label class="flex items-center cursor-pointer w-28 gap-2 select-none">
                            <div class="relative">
                                <input type="checkbox" x-model="d.active" class="sr-only">
                                <div class="w-10 h-5 rounded-full transition-colors"
                                     :class="d.active ? 'bg-emerald-600' : 'bg-slate-300'"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                     :class="d.active ? 'translate-x-5' : 'translate-x-0'"></div>
                            </div>
                            <span class="text-sm font-medium" :class="d.active ? 'text-gray-800' : 'text-gray-400'"
                                  x-text="d.label"></span>
                        </label>

                        {{-- Horario --}}
                        <template x-if="d.active">
                            <div class="flex items-center gap-2 flex-1">
                                <input
                                    type="time"
                                    x-model="d.open"
                                    class="rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                <span class="text-gray-400 text-sm">a</span>
                                <input
                                    type="time"
                                    x-model="d.close"
                                    class="rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                >
                            </div>
                        </template>
                        <template x-if="!d.active">
                            <span class="text-sm text-gray-400 italic">Cerrado</span>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Validación mínima --}}
            <p class="text-xs text-gray-400 mb-6">Necesitas al menos un día con horario configurado.</p>

            <div class="flex gap-3">
                <a href="{{ route('onboarding.step', 2) }}" class="flex-1 text-center py-3 px-6 border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 hover:border-emerald-400 transition-colors">
                    &larr; Atrás
                </a>
                <button
                    type="submit"
                    :disabled="activeDays.length === 0"
                    class="flex-1 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed text-white font-semibold py-3 px-6 rounded-xl shadow-md transition-all"
                >
                    Finalizar configuración
                </button>
            </div>
        </form>
    </div>

</x-onboarding-layout>
