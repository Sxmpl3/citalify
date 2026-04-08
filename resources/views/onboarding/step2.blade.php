<x-onboarding-layout :currentStep="2">

    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-5 sm:p-8"
         x-data="{
             services: [{ name: '', duration_minutes: 30, price: '' }],
             addService() { this.services.push({ name: '', duration_minutes: 30, price: '' }) },
             removeService(i) { if (this.services.length > 1) this.services.splice(i, 1) }
         }">

        <h1 class="text-2xl font-bold text-gray-900 mb-1">¿Qué servicios ofreces?</h1>
        <p class="text-gray-500 mb-6">Añade los servicios que tus clientes podrán reservar.</p>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('onboarding.store2') }}">
            @csrf

            {{-- Lista de servicios --}}
            <div class="space-y-4 mb-4">
                <template x-for="(svc, i) in services" :key="i">
                    <!-- Fila de servicio: apilada en móvil, horizontal en sm+ -->
                    <div class="relative flex flex-col sm:flex-row gap-4 p-5 bg-slate-50 rounded-2xl border border-slate-200 transition-all">
                        
                        {{-- Eliminar (Posicionado arriba a la derecha en móvil) --}}
                        <div class="absolute top-3 right-3 sm:static sm:order-last sm:pt-7">
                            <button
                                type="button"
                                x-on:click="removeService(i)"
                                x-show="services.length > 1"
                                class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
                                title="Eliminar"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Nombre --}}
                        <div class="flex-1 min-w-0">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nombre del servicio</label>
                            <input
                                type="text"
                                :name="`services[${i}][name]`"
                                x-model="svc.name"
                                placeholder="Ej: Corte de pelo"
                                required
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white shadow-sm"
                            >
                        </div>

                        {{-- Contenedor para Duración y Precio (para que queden juntos en móvil) --}}
                        <div class="flex gap-4 w-full sm:w-auto">
                            {{-- Duración --}}
                            <div class="flex-1 sm:w-32">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Duración</label>
                                <select
                                    :name="`services[${i}][duration_minutes]`"
                                    x-model="svc.duration_minutes"
                                    class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white shadow-sm"
                                >
                                    <option value="15">15 min</option>
                                    <option value="30">30 min</option>
                                    <option value="45">45 min</option>
                                    <option value="60">1 hora</option>
                                    <option value="90">1h 30min</option>
                                    <option value="120">2 horas</option>
                                </select>
                            </div>

                            {{-- Precio --}}
                            <div class="flex-1 sm:w-28">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Precio (€)</label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        :name="`services[${i}][price]`"
                                        x-model="svc.price"
                                        placeholder="0"
                                        min="0"
                                        step="0.50"
                                        required
                                        class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white shadow-sm pr-8"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Añadir servicio --}}
            <button
                type="button"
                x-on:click="addService()"
                class="w-full py-2 border-2 border-dashed border-slate-300 hover:border-emerald-400 text-slate-500 hover:text-emerald-600 rounded-xl text-sm font-medium transition-colors mb-8"
            >
                + Añadir otro servicio
            </button>

            <div class="flex gap-3">
                <a href="{{ route('onboarding.step', 1) }}" class="flex-1 text-center py-3 px-6 border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 hover:border-emerald-400 transition-colors">
                    &larr; Atrás
                </a>
                <button type="submit" class="flex-1 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white font-semibold py-3 px-6 rounded-xl shadow-md transition-all">
                    Continuar &rarr;
                </button>
            </div>
        </form>
    </div>

</x-onboarding-layout>
