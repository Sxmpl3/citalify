<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-bold text-xl text-slate-800 leading-tight">
                Facturación
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Hoy --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col p-6 overflow-hidden relative">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-emerald-50 to-white rounded-bl-[100px] -z-0"></div>
                    <p class="text-sm font-medium text-slate-500 mb-1 z-10 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Hoy
                    </p>
                    <p class="text-3xl font-display font-bold text-slate-800 z-10">{{ number_format($todayRevenue, 2, ',', '.') }} €</p>
                </div>
                {{-- Semana --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col p-6 overflow-hidden relative">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-blue-50 to-white rounded-bl-[100px] -z-0"></div>
                    <p class="text-sm font-medium text-slate-500 mb-1 z-10 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Esta Semana
                    </p>
                    <p class="text-3xl font-display font-bold text-slate-800 z-10">{{ number_format($weekRevenue, 2, ',', '.') }} €</p>
                </div>
                {{-- Mes --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col p-6 overflow-hidden relative">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-purple-50 to-white rounded-bl-[100px] -z-0"></div>
                    <p class="text-sm font-medium text-slate-500 mb-1 z-10 flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Este Mes
                    </p>
                    <p class="text-3xl font-display font-bold text-slate-800 z-10">{{ number_format($monthRevenue, 2, ',', '.') }} €</p>
                </div>
            </div>

            {{-- Filtro e histórico --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-5 bg-slate-50/50">
                    <div>
                        <h3 class="font-display font-bold text-lg text-slate-800">Desglose por fechas</h3>
                        <p class="text-sm text-slate-500">Muestra las citas confirmadas en el rango seleccionado.</p>
                    </div>
                    <form method="GET" action="{{ route('billing.index') }}" class="flex flex-col sm:flex-row items-end gap-3 justify-between w-full lg:w-auto">
                        <div class="flex gap-3 w-full sm:w-auto justify-between sm:justify-start">
                            <div class="flex-1 sm:flex-none">
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1">Desde</label>
                                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm px-4">
                            </div>
                            <div class="flex-1 sm:flex-none">
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1">Hasta</label>
                                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm px-4">
                            </div>
                        </div>
                        <button type="submit" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-colors shadow-sm">
                            Filtrar
                        </button>
                    </form>
                </div>

                <div class="p-5 sm:p-6 bg-emerald-50/50 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-emerald-800">Total en el periodo seleccionado</p>
                        <p class="text-xs text-emerald-600/80 mt-0.5">Basado en {{ $rangeBookings->count() }} citas confirmadas</p>
                    </div>
                    <p class="text-3xl font-display font-bold text-emerald-700">{{ number_format($rangeRevenue, 2, ',', '.') }} €</p>
                </div>

                {{-- Vista móvil: lista de tarjetas --}}
                <div class="divide-y divide-slate-100 md:hidden">
                    @forelse($rangeBookings as $b)
                        <div class="px-4 py-3 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate">{{ $b->customer_name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5 tabular-nums">
                                    {{ \Carbon\Carbon::parse($b->starts_at)->timezone(auth()->user()->timezone ?? 'Europe/Madrid')->format('d/m/Y · H:i') }}
                                </p>
                            </div>
                            <p class="text-sm font-bold text-slate-800 shrink-0 tabular-nums">{{ number_format($b->price, 2, ',', '.') }} €</p>
                        </div>
                    @empty
                        <div class="py-12 px-4 text-center">
                            <p class="text-slate-500 font-medium text-sm">No hay facturación registrada en este rango.</p>
                            <p class="text-slate-400 text-xs mt-1">Gana dinero programando más citas para este periodo.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Vista desktop: tabla completa --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-slate-500 bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-6 font-semibold uppercase tracking-wider text-[11px]">Fecha y Hora</th>
                                <th class="py-3 px-6 font-semibold uppercase tracking-wider text-[11px]">Cliente</th>
                                <th class="py-3 px-6 font-semibold uppercase tracking-wider text-[11px]">Servicio</th>
                                <th class="py-3 px-6 font-semibold uppercase tracking-wider text-[11px] text-right">Importe</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-700">
                            @forelse($rangeBookings as $b)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-6 font-medium text-slate-600 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($b->starts_at)->timezone(auth()->user()->timezone ?? 'Europe/Madrid')->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-4 px-6 font-medium">{{ $b->customer_name }}</td>
                                    <td class="py-4 px-6 text-slate-500">
                                        <span class="inline-flex items-center px-2 py-1 rounded bg-slate-100 text-xs text-slate-600 font-medium">
                                            {{ $b->service->name ?? 'Servicio eliminado' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right font-semibold text-slate-800 whitespace-nowrap">
                                        {{ number_format($b->price, 2, ',', '.') }} €
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 px-6 text-center">
                                        <p class="text-slate-500 font-medium text-sm">No hay facturación registrada en este rango.</p>
                                        <p class="text-slate-400 text-xs mt-1">Gana dinero programando más citas para este periodo.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
