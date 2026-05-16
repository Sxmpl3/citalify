<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
use App\Models\Vacation;
use App\Models\PendingBooking;
use App\Mail\BookingOtpMail;
use App\Mail\BookingConfirmationMail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PublicBookingController extends Controller
{
    public function show(string $slug): View
    {
        $business = User::where('business_slug', $slug)
            ->where('onboarding_completed', true)
            ->firstOrFail();

        if (is_null($business->plan_id)) {
            return view('booking.inactive', compact('business'));
        }

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
            $vacationDates = collect();
        } else {
            $schedules = $employee ? $employee->schedules->keyBy('day_of_week') : collect();
            $vacationDates = $business->vacations()
                ->where('date', '>=', $today->toDateString())
                ->where('date', '<', $rangeEnd->toDateString())
                ->pluck('date')
                ->map(fn($d) => Carbon::parse($d)->toDateString())
                ->flip();
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
                if ($vacationDates->has($dateStr)) {
                    $status = 'closed';
                } elseif ($schedules->has($date->dayOfWeek)) {
                    $status = 'open';
                    $sch = $schedules->get($date->dayOfWeek);
                    $openTime = Carbon::parse($sch->open_time)->format('H:i');
                    $closeTime = Carbon::parse($sch->close_time)->format('H:i');
                }
            }

            $breakStart = null;
            $breakEnd = null;
            if ($business->schedule_type === 'custom') {
                $sch = $schedules->get($dateStr);
                if ($sch && $sch->break_start) {
                    $breakStart = Carbon::parse($sch->break_start)->format('H:i');
                    $breakEnd = Carbon::parse($sch->break_end)->format('H:i');
                }
            }

            $days[]  = [
                'date'   => $dateStr,
                'status' => $status,
                'open_time' => $openTime,
                'close_time' => $closeTime,
                'break_start' => $breakStart,
                'break_end' => $breakEnd,
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

        $start = Carbon::parse($request->date, $tz)->startOfDay()->utc();
        $end   = Carbon::parse($request->date, $tz)->endOfDay()->utc();

        $bookings = Booking::where('employee_id', $employee->id)
            ->where('starts_at', '>=', $start)
            ->where('starts_at', '<=', $end)
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

        if (is_null($business->plan_id)) {
            return response()->json([]);
        }

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
            $vacationDates = collect();
        } else {
            $schedules = $employee->schedules->keyBy('day_of_week');
            $vacationDates = $business->vacations()
                ->where('date', '>=', $today->toDateString())
                ->where('date', '<', $rangeEnd->toDateString())
                ->pluck('date')
                ->map(fn($d) => Carbon::parse($d)->toDateString())
                ->flip();
        }

        $bookings = Booking::where('employee_id', $employee->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('starts_at', '<', $rangeEnd->copy()->utc()->toDateTimeString())
            ->where('ends_at',   '>', $today->copy()->utc()->toDateTimeString())
            ->get(['starts_at', 'ends_at']);

        $pending = PendingBooking::where('employee_id', $employee->id)
            ->where('expires_at', '>', now())
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
                if ($vacationDates->has($dateStr)) {
                    $days[] = ['date' => $dateStr, 'status' => 'closed'];
                    continue;
                }
                $schedule  = $schedules->get($date->dayOfWeek);
                if (!$schedule) {
                    $days[] = ['date' => $dateStr, 'status' => 'closed'];
                    continue;
                }
            }

            $open   = Carbon::parse($date->toDateString() . ' ' . $schedule->open_time, $tz);
            $close  = Carbon::parse($date->toDateString() . ' ' . $schedule->close_time, $tz);
            $breakStart = $schedule->break_start ? Carbon::parse($date->toDateString() . ' ' . $schedule->break_start, $tz) : null;
            $breakEnd   = $schedule->break_end   ? Carbon::parse($date->toDateString() . ' ' . $schedule->break_end,   $tz) : null;
            $cursor = $open->copy();
            $hasSlot = false;

            while ($cursor->copy()->addMinutes($duration)->lte($close)) {
                $slotEnd = $cursor->copy()->addMinutes($duration);

                // Skip slots that overlap with the break
                if ($breakStart && $breakEnd && $cursor->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                    $cursor->addMinutes($duration);
                    continue;
                }

                if ($cursor->gte($now)) {
                    $conflict = $bookings->first(
                        fn($b) => $b->starts_at < $slotEnd && $b->ends_at > $cursor
                    ) ?: $pending->first(
                        fn($p) => $p->starts_at < $slotEnd && $p->ends_at > $cursor
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

        if (is_null($business->plan_id)) {
            return response()->json([]);
        }

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
            $schedule = $employee->customSchedules()->whereDate('date', $date->toDateString())->first();
            if (!$schedule || $schedule->is_closed) {
                return response()->json([]);
            }
        } else {
            $isVacation = $business->vacations()
                ->whereDate('date', $date->toDateString())
                ->exists();
            if ($isVacation) {
                return response()->json([]);
            }
            $schedule = $employee->schedules()->where('day_of_week', $dayOfWeek)->first();
            if (!$schedule) {
                return response()->json([]);
            }
        }

        $open     = Carbon::parse($date->toDateString() . ' ' . $schedule->open_time, $tz);
        $close    = Carbon::parse($date->toDateString() . ' ' . $schedule->close_time, $tz);
        $breakStart = $schedule->break_start ? Carbon::parse($date->toDateString() . ' ' . $schedule->break_start, $tz) : null;
        $breakEnd   = $schedule->break_end   ? Carbon::parse($date->toDateString() . ' ' . $schedule->break_end,   $tz) : null;
        $duration = $service->duration_minutes;

        $now    = Carbon::now($tz);
        $slots  = [];
        $cursor = $open->copy();

        while ($cursor->copy()->addMinutes($duration)->lte($close)) {
            $slotEnd = $cursor->copy()->addMinutes($duration);

            // Skip slots that overlap with the break window
            if ($breakStart && $breakEnd && $cursor->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                $cursor->addMinutes($duration);
                continue;
            }

            // Solo slots que empiezan ahora o en el futuro
            if ($cursor->gte($now)) {
                $conflictBody = Booking::where('employee_id', $employee->id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('starts_at', '<', $slotEnd->copy()->utc()->toDateTimeString())
                    ->where('ends_at',   '>', $cursor->copy()->utc()->toDateTimeString())
                    ->exists();

                $conflictPending = PendingBooking::where('employee_id', $employee->id)
                    ->where('expires_at', '>', now())
                    ->where('starts_at', '<', $slotEnd->copy()->utc()->toDateTimeString())
                    ->where('ends_at',   '>', $cursor->copy()->utc()->toDateTimeString())
                    ->exists();

                if (!$conflictBody && !$conflictPending) {
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

        if (is_null($business->plan_id)) {
            return back()->withErrors(['error' => 'El negocio se encuentra inactivo actualmente.']);
        }

        $data = $request->validate([
            'service_id'     => ['required', 'integer', 'exists:services,id'],
            'date'           => ['required', 'date', 'after_or_equal:today'],
            'time'           => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'customer_name'  => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_email' => ['required', 'email', 'max:100'],
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

        if ($business->schedule_type === 'normal') {
            $isVacation = $business->vacations()
                ->whereDate('date', $startsAt->toDateString())
                ->exists();
            if ($isVacation) {
                return back()
                    ->withErrors(['date' => 'Este día no está disponible para reservas.'])
                    ->withInput();
            }
        }

        // Final conflict check (comparar en UTC para evitar errores de timezone)
        $conflictBody = Booking::where('employee_id', $employee->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('starts_at', '<', $endsAt->copy()->utc()->toDateTimeString())
            ->where('ends_at',   '>', $startsAt->copy()->utc()->toDateTimeString())
            ->exists();

        $conflictPending = PendingBooking::where('employee_id', $employee->id)
            ->where('expires_at', '>', now())
            ->where('starts_at', '<', $endsAt->copy()->utc()->toDateTimeString())
            ->where('ends_at',   '>', $startsAt->copy()->utc()->toDateTimeString())
            ->exists();

        if ($conflictBody || $conflictPending) {
            return back()
                ->withErrors(['time' => 'Esta hora ya no está disponible. Por favor, elige otra.'])
                ->withInput();
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $pending = PendingBooking::create([
            'user_id'        => $business->id,
            'employee_id'    => $employee->id,
            'service_id'     => $service->id,
            'price'          => $service->price,
            'customer_name'  => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_email' => $data['customer_email'],
            'starts_at'      => $startsAt->utc(),
            'ends_at'        => $endsAt->utc(),
            'notes'          => $data['notes'] ?? null,
            'verification_code' => $otp,
            'expires_at'     => now()->addMinutes(20),
        ]);

        Mail::to($pending->customer_email)->send(new BookingOtpMail($pending));

        return redirect()->route('booking.verify', [$slug, $pending->id]);
    }

    public function showVerify(string $slug, PendingBooking $pending_booking): View|RedirectResponse
    {
        $business = User::where('business_slug', $slug)->firstOrFail();
        
        if (is_null($business->plan_id)) {
            return redirect()->route('booking.show', $slug);
        }
        
        abort_if($pending_booking->isExpired(), 404, 'El tiempo de reserva ha expirado.');
        
        $pendingBooking = $pending_booking;
        
        return view('booking.verify', compact('business', 'pendingBooking'));
    }

    public function verifyCode(string $slug, Request $request, PendingBooking $pending_booking): RedirectResponse
    {
        $business = User::where('business_slug', $slug)->firstOrFail();

        if (is_null($business->plan_id)) {
            return redirect()->route('booking.show', $slug);
        }

        if ($pending_booking->isExpired()) {
            return redirect()->route('booking.show', $slug)
                ->withErrors(['error' => 'Tu reserva temporal ha expirado. Por favor, inténtalo de nuevo.']);
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        if (strtoupper($data['code']) !== strtoupper($pending_booking->verification_code)) {
            return back()->withErrors(['code' => 'El código introducido no es correcto.']);
        }

        // Move to real bookings table
        $booking = Booking::create([
            'user_id'        => $pending_booking->user_id,
            'employee_id'    => $pending_booking->employee_id,
            'service_id'     => $pending_booking->service_id,
            'price'          => $pending_booking->price,
            'customer_name'  => $pending_booking->customer_name,
            'customer_phone' => $pending_booking->customer_phone,
            'customer_email' => $pending_booking->customer_email,
            'starts_at'      => $pending_booking->starts_at,
            'ends_at'        => $pending_booking->ends_at,
            'status'         => 'pending',
            'notes'          => $pending_booking->notes,
        ]);

        // Delete pending record
        $pending_booking->delete();

        Mail::to($booking->customer_email)->send(new BookingConfirmationMail($booking));

        return redirect()->route('booking.confirmed', $slug);
    }

    public function confirmed(string $slug): View
    {
        $business = User::where('business_slug', $slug)
            ->where('onboarding_completed', true)
            ->firstOrFail();

        if (is_null($business->plan_id)) {
            return redirect()->route('booking.show', $slug);
        }

        return view('booking.confirmed', compact('business'));
    }
}
