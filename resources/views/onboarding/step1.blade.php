<x-onboarding-layout :currentStep="1">

    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Cuéntanos sobre tu negocio</h1>
        <p class="text-gray-500 mb-6">Esta información aparecerá en tu página de reservas.</p>

        <form method="POST" action="{{ route('onboarding.store1') }}">
            @csrf

            {{-- Nombre del negocio --}}
            <div class="mb-5">
                <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre del negocio <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="business_name"
                    name="business_name"
                    value="{{ old('business_name', auth()->user()->business_name) }}"
                    placeholder="Ej: Peluquería Carmen"
                    required
                    class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('business_name') border-red-400 @enderror"
                    x-data
                    x-on:input="
                        const slug = document.getElementById('business_slug');
                        if (!slug.dataset.modified) {
                            slug.value = $el.value.toLowerCase()
                                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                                .replace(/[^a-z0-9]+/g, '-')
                                .replace(/^-|-$/g, '');
                        }
                    "
                >
                @error('business_name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug (URL) --}}
            <div class="mb-5">
                <label for="business_slug" class="block text-sm font-medium text-gray-700 mb-1">
                    URL de tu página de reservas <span class="text-red-500">*</span>
                </label>
                <div class="flex rounded-lg shadow-sm">
                    <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-sm">
                        citalify.es/
                    </span>
                    <input
                        type="text"
                        id="business_slug"
                        name="business_slug"
                        value="{{ old('business_slug', auth()->user()->business_slug) }}"
                        placeholder="mi-peluqueria"
                        required
                        x-data
                        x-on:input="$el.dataset.modified = '1'"
                        class="flex-1 min-w-0 rounded-none rounded-r-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('business_slug') border-red-400 @enderror"
                    >
                </div>
                @error('business_slug')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">Solo letras, números y guiones. Ej: peluqueria-carmen</p>
            </div>

            {{-- Teléfono --}}
            <div class="mb-5">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                    Teléfono de contacto <span class="text-red-500">*</span>
                </label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="{{ old('phone', auth()->user()->phone) }}"
                    placeholder="Ej: 612 345 678"
                    required
                    class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('phone') border-red-400 @enderror"
                >
                @error('phone')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dirección --}}
            <div class="mb-5">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                    Dirección
                </label>
                <input
                    type="text"
                    id="address"
                    name="address"
                    value="{{ old('address', auth()->user()->address) }}"
                    placeholder="Ej: Calle Mayor 12, Madrid"
                    class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
            </div>

            {{-- Días de agenda --}}
            <div class="mb-5">
                <label for="booking_days_ahead" class="block text-sm font-medium text-gray-700 mb-1">
                    Vista de tu agenda <span class="text-red-500">*</span>
                </label>
                <select id="booking_days_ahead" name="booking_days_ahead" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="14" {{ old('booking_days_ahead', auth()->user()->booking_days_ahead) == 14 ? 'selected' : '' }}>Próximos 14 días (Recomendado)</option>
                    <option value="28" {{ old('booking_days_ahead', auth()->user()->booking_days_ahead) == 28 ? 'selected' : '' }}>Próximos 28 días (Mes completo)</option>
                </select>
                <p class="mt-1 text-xs text-gray-400">Determina hasta cuántos días podrán ver de disponibilidad.</p>
                @error('booking_days_ahead')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Zona horaria --}}
            <div class="mb-8">
                <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1">
                    Zona horaria <span class="text-red-500">*</span>
                </label>
                <select
                    id="timezone"
                    name="timezone"
                    class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
                    @foreach(['Europe/Madrid' => 'España (Madrid)', 'Atlantic/Canary' => 'España (Canarias)', 'America/Mexico_City' => 'México', 'America/Bogota' => 'Colombia', 'America/Buenos_Aires' => 'Argentina', 'America/Lima' => 'Perú', 'America/Santiago' => 'Chile'] as $tz => $label)
                        <option value="{{ $tz }}" {{ old('timezone', auth()->user()->timezone) === $tz ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('timezone')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white font-semibold py-3 px-6 rounded-xl shadow-md shadow-emerald-900/20 transition-all">
                Continuar &rarr;
            </button>
        </form>
    </div>

</x-onboarding-layout>

