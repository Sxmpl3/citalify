<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800 leading-tight">
                    Vacaciones
                </h2>
                <p class="text-sm text-slate-400 mt-0.5">
                    Marca los días en los que el negocio estará cerrado por vacaciones.
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

        $vacationsSet = $vacations->flip();
        $dayLabels = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
        $monthLabels = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
    @endphp

    <div class="py-8">
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
                    <p class="text-amber-700 text-xs">Al marcar un día como vacaciones, las citas existentes ese día se <strong>cancelarán automáticamente</strong> y se notificará por email a los clientes.</p>
                </div>
            </div>

            {{-- Leyenda --}}
            <div class="flex items-center gap-4 text-xs text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-rose-500 inline-block"></span> Vacaciones</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded border-2 border-slate-200 inline-block"></span> Disponible</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-slate-100 inline-block"></span> Pasado</span>
            </div>

            {{-- Calendarios por mes --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($months as $month)
                    @php
                        $firstDay = $month->copy()->startOfMonth();
                        $lastDay  = $month->copy()->endOfMonth();
                        // Carbon dayOfWeek: 0=Domingo. Queremos lunes como primer día → offset
                        $startWeekday = ($firstDay->dayOfWeek + 6) % 7; // 0=lunes, 6=domingo
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
                                        $date = $month->copy()->day($d);
                                        $dateStr = $date->toDateString();
                                        $isPast = $date->lt($today);
                                        $isVacation = $vacationsSet->has($dateStr);
                                        $vacationId = $isVacation
                                            ? auth()->user()->vacations()->whereDate('date', $dateStr)->value('id')
                                            : null;
                                    @endphp

                                    @if($isPast)
                                        <div class="aspect-square rounded-lg flex items-center justify-center bg-slate-50 text-slate-300 text-xs">
                                            {{ $d }}
                                        </div>
                                    @elseif($isVacation)
                                        <form method="POST" action="{{ route('vacations.destroy', $vacationId) }}" class="contents">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                title="Quitar vacaciones del {{ $date->format('d/m/Y') }}"
                                                class="aspect-square rounded-lg flex items-center justify-center bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold shadow-sm transition-all hover:scale-105">
                                                {{ $d }}
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('vacations.store') }}" class="contents">
                                            @csrf
                                            <input type="hidden" name="date" value="{{ $dateStr }}">
                                            <button type="submit"
                                                title="Marcar {{ $date->format('d/m/Y') }} como vacaciones"
                                                class="aspect-square rounded-lg flex items-center justify-center bg-white border-2 border-slate-200 hover:border-rose-300 hover:bg-rose-50 text-slate-600 hover:text-rose-600 text-xs font-medium transition-all">
                                                {{ $d }}
                                            </button>
                                        </form>
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
                        <h3 class="font-display font-bold text-slate-800 text-base">Días marcados</h3>
                        <span class="text-xs text-slate-500">{{ $vacations->count() }} {{ $vacations->count() === 1 ? 'día' : 'días' }}</span>
                    </div>
                    <div class="p-4 flex flex-wrap gap-2">
                        @foreach($vacations as $vDate)
                            @php
                                $vId = auth()->user()->vacations()->whereDate('date', $vDate)->value('id');
                                $parsed = \Carbon\Carbon::parse($vDate);
                            @endphp
                            <form method="POST" action="{{ route('vacations.destroy', $vId) }}" class="inline-flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="group inline-flex items-center gap-2 bg-rose-50 hover:bg-rose-100 text-rose-700 px-3 py-1.5 rounded-xl text-xs font-medium transition-colors">
                                    {{ $parsed->locale('es')->isoFormat('ddd D MMM') }}
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
                    <p class="text-xs text-slate-400 mt-1">Haz click en cualquier día del calendario para marcarlo.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
