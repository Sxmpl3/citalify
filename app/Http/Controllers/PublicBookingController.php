<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicBookingController extends Controller
{
    public function show(string $slug): View
    {
        $business = User::where('business_slug', $slug)
            ->where('onboarding_completed', true)
            ->firstOrFail();

        // Se eliminó la vista de owner. El dueño verá su página pública normalmente.

        $services = $business->services()->where('is_active', true)->get();

        return view('booking.show', compact('business', 'services'));
    }

    public function ownerCalendar(string $slug): JsonResponse
    {
        $business = User::where('business_slug', $slug)->firstOrFail();
        abort_if(auth()->id() !== $business->id, 403);

        $tz       = $business->timezone ?? 'Europe/Madrid';
        $today    = Carbon::today($tz);
        $daysAhead= $business->booking_days_ahead ?? 14;
        $rangeEnd = $today->copy()->addDays($daysAhead);

        $employee  = $business->employees()->first();
        
        if ($business->schedule_type === 'custom') {
            $schedules = $employee ? $employee->customSchedules()
                ->where('date', '>=', $today->toDateString())
                ->where('date', '<', $rangeEnd->toDateString())
                ->get()->keyBy(fn($s) => $s->date->toDateString()) : collect();
        } else {
            $schedules = $employee ? $employee->schedules->keyBy('day_of_week') : collect();
        }

        $bookings = $employee
            ? Booking::where('employee_id', $employee->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('starts_at', '>=', $today->copy()->utc()->toDateTimeString())
                ->where('starts_at', '<',  $rangeEnd->copy()->utc()->toDateTimeString())
                ->get(['starts_at'])
            : collect();

        $byDate = $bookings->groupBy(
            fn($b) => Carbon::parse($b->starts_at)->setTimezone($tz)->toDateString()
        );

        $days = [];
        for ($i = 0; $i < $daysAhead; $i++) {
            $date    = $today->copy()->addDays($i);
            $dateStr = $date->toDateString();
            
            $status = 'closed';
            $openTime = '09:00';
            $closeTime = '19:00';
            if ($business->schedule_type === 'custom') {
                $sch = $schedules->get($dateStr);
                if ($sch) {
                    if (!$sch->is_closed) $status = 'open';
                    if ($sch->open_time) $openTime = Carbon::parse($sch->open_time)->format('H:i');
                    if ($sch->close_time) $closeTime = Carbon::parse($sch->close_time)->format('H:i');
                }
            } else {
                if ($schedules->has($date->dayOfWeek)) {
                    $status = 'open';
                    $sch = $schedules->get($date->dayOfWeek);
                    $openTime = Carbon::parse($sch->open_time)->format('H:i');
                    $closeTime = Carbon::parse($sch->close_time)->format('H:i');
                }
            }

            $days[]  = [
                'date'   => $dateStr,
                'status' => $status,
                'open_time' => $openTime,
                'close_time' => $closeTime,
                'count'  => $byDate->get($dateStr, collect())->count(),
            ];
        }

        return response()->json($days);
    }

    public function dayBookings(string $slug, Request $request): JsonResponse
    {
        $business = User::where('business_slug', $slug)->firstOrFail();
        abort_if(auth()->id() !== $business->id, 403);

        $tz       = $business->timezone ?? 'Europe/Madrid';
        $employee = $business->employees()->first();

        if (!$employee) {
            return response()->json([]);
        }

        $bookings = Booking::where('employee_id', $employee->id)
            ->whereDate('starts_at', $request->date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->with('service')
            ->orderBy('starts_at')
            ->get()
            ->map(fn($b) => [
                'id'             => $b->id,
                'starts_at'      => Carbon::parse($b->starts_at)->setTimezone($tz)->format('H:i'),
                'ends_at'        => Carbon::parse($b->ends_at)->setTimezone($tz)->format('H:i'),
                'customer_name'  => $b->customer_name,
                'customer_phone' => $b->customer_phone,
                'customer_email' => $b->customer_email,
                'service'        => $b->service->name,
                'status'         => $b->status,
                'notes'          => $b->notes,
            ]);

        return response()->json($bookings);
    }

    public function calendar(string $slug, Request $request): JsonResponse
    {
        $business = User::where('business_slug', $slug)
            ->where('onboarding_completed', true)
            ->firstOrFail();

        $service = Service::where('id', $request->service_id)
            ->where('user_id', $business->id)
            ->where('is_active', true)
            ->firstOrFail();

        $tz       = $business->timezone ?? 'Europe/Madrid';
        $now      = Carbon::now($tz);
        $today    = Carbon::today($tz);
        $daysAhead= $business->booking_days_ahead ?? 14;
        $rangeEnd = $today->copy()->addDays($daysAhead);

        $employee = $business->employees()->first();

        // Si no hay empleado, todos los días cerrados
        if (!$employee) {
            $days = [];
            for ($i = 0; $i < $daysAhead; $i++) {
                $days[] = ['date' => $today->copy()->addDays($i)->toDateString(), 'status' => 'closed'];
            }
            return response()->json($days);
        }

        // Precargar horarios y reservas del período
        if ($business->schedule_type === 'custom') {
            $schedules = $employee->customSchedules()
                ->where('date', '>=', $today->toDateString())
                ->where('date', '<', $rangeEnd->toDateString())
                ->get()->keyBy(fn($s) => $s->date->toDateString());
        } else {
            $schedules = $employee->schedules->keyBy('day_of_week');
        }

        $bookings = Booking::where('employee_id', $employee->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('starts_at', '<', $rangeEnd->copy()->utc()->toDateTimeString())
            ->where('ends_at',   '>', $today->copy()->utc()->toDateTimeString())
            ->get(['starts_at', 'ends_at']);

        $duration = $service->duration_minutes;
        $days     = [];

        for ($i = 0; $i < $daysAhead; $i++) {
            $date      = $today->copy()->addDays($i);
            $dateStr   = $date->toDateString();
            
            if ($business->schedule_type === 'custom') {
                $schedule = $schedules->get($dateStr);
                if (!$schedule || $schedule->is_closed) {
                    $days[] = ['date' => $dateStr, 'status' => 'closed'];
                    continue;
                }
            } else {
                $schedule  = $schedules->get($date->dayOfWeek);
                if (!$schedule) {
                    $days[] = ['date' => $dateStr, 'status' => 'closed'];
                    continue;
                }
            }

            $open   = Carbon::parse($date->toDateString() . ' ' . $schedule->open_time, $tz);
            $close  = Carbon::parse($date->toDateString() . ' ' . $schedule->close_time, $tz);
            $cursor = $open->copy();
            $hasSlot = false;

            while ($cursor->copy()->addMinutes($duration)->lte($close)) {
                $slotEnd = $cursor->copy()->addMinutes($duration);

                if ($cursor->gte($now)) {
                    $conflict = $bookings->first(
                        fn($b) => $b->starts_at < $slotEnd && $b->ends_at > $cursor
                    );
                    if (!$conflict) {
                        $hasSlot = true;
                        break;
                    }
                }
                $cursor->addMinutes($duration);
            }

            $days[] = ['date' => $date->toDateString(), 'status' => $hasSlot ? 'available' : 'full'];
        }

        return response()->json($days);
    }

    public function availability(string $slug, Request $request): JsonResponse
    {
        $business = User::where('business_slug', $slug)
            ->where('onboarding_completed', true)
            ->firstOrFail();

        $service = Service::where('id', $request->service_id)
            ->where('user_id', $business->id)
            ->where('is_active', true)
            ->firstOrFail();

        $tz   = $business->timezone ?? 'Europe/Madrid';
        $date = Carbon::parse($request->date, $tz)->startOfDay();

        // Don't return slots for past dates
        if ($date->isPast() && !$date->isToday()) {
            return response()->json([]);
        }

        $dayOfWeek = $date->dayOfWeek; // 0=Sunday, 6=Saturday

        $employee = $business->employees()->first();
        if (!$employee) {
            return response()->json([]);
        }

        $schedule = null;
        if ($business->schedule_type === 'custom') {
            $schedule = $employee->customSchedules()->where('date', $date->toDateString())->first();
            if (!$schedule || $schedule->is_closed) {
                return response()->json([]);
            }
        } else {
            $schedule = $employee->schedules()->where('day_of_week', $dayOfWeek)->first();
            if (!$schedule) {
                return response()->json([]);
            }
        }

        $open     = Carbon::parse($date->toDateString() . ' ' . $schedule->open_time, $tz);
        $close    = Carbon::parse($date->toDateString() . ' ' . $schedule->close_time, $tz);
        $duration = $service->duration_minutes;

        $now    = Carbon::now($tz);
        $slots  = [];
        $cursor = $open->copy();

        while ($cursor->copy()->addMinutes($duration)->lte($close)) {
            $slotEnd = $cursor->copy()->addMinutes($duration);

            // Solo slots que empiezan ahora o en el futuro
            if ($cursor->gte($now)) {
                $conflict = Booking::where('employee_id', $employee->id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('starts_at', '<', $slotEnd->copy()->utc()->toDateTimeString())
                    ->where('ends_at',   '>', $cursor->copy()->utc()->toDateTimeString())
                    ->exists();

                if (!$conflict) {
                    $slots[] = $cursor->format('H:i');
                }
            }

            $cursor->addMinutes($duration);
        }

        return response()->json($slots);
    }

    public function store(string $slug, Request $request): RedirectResponse
    {
        $business = User::where('business_slug', $slug)
            ->where('onboarding_completed', true)
            ->firstOrFail();

        $data = $request->validate([
            'service_id'     => ['required', 'integer', 'exists:services,id'],
            'date'           => ['required', 'date', 'after_or_equal:today'],
            'time'           => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'customer_name'  => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $service = Service::where('id', $data['service_id'])
            ->where('user_id', $business->id)
            ->where('is_active', true)
            ->firstOrFail();

        if (!empty($data['customer_email'])) {
            $strikes = \App\Models\Blacklist::where('user_id', $business->id)
                ->where('email', $data['customer_email'])
                ->value('strikes');
            if ($strikes >= 3) {
                return back()
                    ->withErrors(['customer_email' => 'No es posible realizar la reserva en este momento.'])
                    ->withInput();
            }
        }

        $employee = $business->employees()->first();
        abort_if(!$employee, 422, 'No hay empleados disponibles.');

        $tz       = $business->timezone ?? 'Europe/Madrid';
        $startsAt = Carbon::parse($data['date'] . ' ' . $data['time'], $tz);
        $endsAt   = $startsAt->copy()->addMinutes($service->duration_minutes);

        // Final conflict check (comparar en UTC para evitar errores de timezone)
        $conflict = Booking::where('employee_id', $employee->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('starts_at', '<', $endsAt->copy()->utc()->toDateTimeString())
            ->where('ends_at',   '>', $startsAt->copy()->utc()->toDateTimeString())
            ->exists();

        if ($conflict) {
            return back()
                ->withErrors(['time' => 'Esta hora ya no está disponible. Por favor, elige otra.'])
                ->withInput();
        }

        Booking::create([
            'user_id'        => $business->id,
            'employee_id'    => $employee->id,
            'service_id'     => $service->id,
            'price'          => $service->price,
            'customer_name'  => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_email' => $data['customer_email'] ?? null,
            'starts_at'      => $startsAt,
            'ends_at'        => $endsAt,
            'status'         => 'pending',
            'notes'          => $data['notes'] ?? null,
        ]);

        return redirect()->route('booking.confirmed', $slug);
    }

    public function confirmed(string $slug): View
    {
        $business = User::where('business_slug', $slug)
            ->where('onboarding_completed', true)
            ->firstOrFail();

        return view('booking.confirmed', compact('business'));
    }
}
