<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-bold text-xl text-slate-800 leading-tight">Mis servicios</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-4 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if($services->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-slate-600 mb-1">Sin servicios aún</p>
                    <p class="text-sm text-slate-400">Añade el primer servicio que ofrecerás a tus clientes.</p>
                </div>
            @else
                {{-- Lista de servicios --}}
                @foreach($services as $service)
                    <div
                        x-data="{
                            editing: false,
                            deleting: false,
                            name:             {{ json_encode($service->name) }},
                            duration_minutes: {{ $service->duration_minutes }},
                            price:            {{ $service->price }},
                            color:            {{ json_encode($service->color) }},
                            is_active:        {{ $service->is_active ? 'true' : 'false' }},
                        }"
                        class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
                    >
                        {{-- Vista normal --}}
                        <div x-show="!editing" class="flex items-center gap-4 px-5 py-4">
                            <div class="w-3 h-3 rounded-full shrink-0 ring-2 ring-white ring-offset-1"
                                 :style="'background-color:' + color"></div>

                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-800 truncate" x-text="name"></p>
                                <p class="text-sm text-slate-400 tabular-nums">
                                    <span x-text="duration_minutes + ' min'"></span>
                                    &middot;
                                    <span x-text="parseFloat(price) > 0 ? parseFloat(price).toFixed(2) + ' €' : 'Gratis'"></span>
                                </p>
                            </div>

                            <span x-show="is_active"
                                  class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">
                                Activo
                            </span>
                            <span x-show="!is_active"
                                  class="text-xs font-semibold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-lg">
                                Inactivo
                            </span>

                            <button type="button" @click="editing = true"
                                    class="text-sm text-slate-400 hover:text-emerald-600 font-medium transition-colors ml-1">
                                Editar
                            </button>
                        </div>

                        {{-- Formulario de edición --}}
                        <div x-show="editing" class="p-5 border-t border-slate-50">
                            <p class="text-sm font-semibold text-slate-600 mb-4">Editando servicio</p>

                            <form method="POST" action="{{ route('services.update', $service) }}">
                                @csrf
                                @method('PATCH')

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    {{-- Nombre --}}
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Nombre del servicio</label>
                                        <input type="text" name="name" x-model="name" required
                                               class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    </div>

                                    {{-- Duración --}}
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Duración</label>
                                        <select name="duration_minutes" x-model.number="duration_minutes"
                                                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                            <option value="15">15 min</option>
                                            <option value="30">30 min</option>
                                            <option value="45">45 min</option>
                                            <option value="60">1 hora</option>
                                            <option value="90">1h 30min</option>
                                            <option value="120">2 horas</option>
                                        </select>
                                    </div>

                                    {{-- Precio --}}
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Precio (€)</label>
                                        <input type="number" name="price" x-model="price"
                                               min="0" step="0.50" required
                                               class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    </div>

                                    {{-- Color --}}
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Color</label>
                                        <div class="flex items-center gap-2">
                                            <input type="color" name="color" x-model="color"
                                                   class="h-9 w-14 rounded-lg border-slate-300 cursor-pointer p-0.5">
                                            <span class="text-xs text-slate-400" x-text="color"></span>
                                            <input type="hidden" name="color" x-model="color">
                                        </div>
                                    </div>

                                    {{-- Estado --}}
                                    <div class="flex items-center gap-3">
                                        <label class="flex items-center gap-2 cursor-pointer select-none">
                                            <div class="relative">
                                                <input type="checkbox" name="is_active" value="1"
                                                       x-model="is_active" class="sr-only">
                                                <div class="w-10 h-5 rounded-full transition-colors"
                                                     :class="is_active ? 'bg-emerald-500' : 'bg-slate-300'"></div>
                                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                                     :class="is_active ? 'translate-x-5' : 'translate-x-0'"></div>
                                            </div>
                                            <span class="text-sm font-medium"
                                                  :class="is_active ? 'text-slate-700' : 'text-slate-400'"
                                                  x-text="is_active ? 'Activo' : 'Inactivo'"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                                    <button type="submit"
                                            class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white font-semibold text-sm py-2.5 px-5 rounded-xl shadow-sm transition-all">
                                        Guardar cambios
                                    </button>
                                    <button type="button" @click="editing = false"
                                            class="text-sm text-slate-500 hover:text-slate-700 font-medium transition-colors">
                                        Cancelar
                                    </button>

                                    {{-- Eliminar --}}
                                    <div class="ml-auto" x-data="{ confirm: false }">
                                        <button type="button" x-show="!confirm"
                                                @click="confirm = true"
                                                class="text-sm text-red-400 hover:text-red-600 font-medium transition-colors">
                                            Eliminar
                                        </button>
                                        <div x-show="confirm" class="flex items-center gap-2">
                                            <span class="text-xs text-red-500 font-medium">¿Seguro?</span>
                                            <form method="POST" action="{{ route('services.destroy', $service) }}" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="text-xs font-semibold text-white bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded-lg transition-colors">
                                                    Sí, eliminar
                                                </button>
                                            </form>
                                            <button type="button" @click="confirm = false"
                                                    class="text-xs text-slate-400 hover:text-slate-600 font-medium transition-colors">
                                                No
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- Añadir nuevo servicio --}}
            <div x-data="{ open: false }" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

                <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-slate-50/50 transition-colors">
                    <span class="flex items-center gap-2 font-semibold text-emerald-600 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Añadir servicio
                    </span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-transition class="px-5 pb-5 border-t border-slate-100">
                    <form method="POST" action="{{ route('services.store') }}" class="mt-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Nombre del servicio</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                       placeholder="Ej: Corte de pelo" required
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('name') border-red-400 @enderror">
                                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Duración</label>
                                <select name="duration_minutes"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    @foreach([15=>'15 min',30=>'30 min',45=>'45 min',60=>'1 hora',90=>'1h 30min',120=>'2 horas'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('duration_minutes', 30) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Precio (€)</label>
                                <input type="number" name="price" value="{{ old('price', 0) }}"
                                       min="0" step="0.50" required
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Color</label>
                                <input type="color" name="color" value="{{ old('color', '#10B981') }}"
                                       class="h-9 w-20 rounded-lg border-slate-300 cursor-pointer p-0.5">
                            </div>
                        </div>

                        <button type="submit"
                                class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white font-semibold text-sm py-2.5 px-5 rounded-xl shadow-sm transition-all">
                            Añadir servicio
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
