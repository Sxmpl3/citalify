<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $tz    = $user->timezone ?? 'Europe/Madrid';
        $now   = Carbon::now($tz);
        $start = Carbon::today($tz)->startOfDay()->utc();
        $end   = Carbon::today($tz)->endOfDay()->utc();

        $todayBookings = Booking::where('user_id', $user->id)
            ->where('starts_at', '>=', $start)
            ->where('starts_at', '<=', $end)
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['service', 'employee'])
            ->orderBy('starts_at')
            ->get();

        $stats = [
            'today'   => $todayBookings->count(),
            'pending' => Booking::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'week'    => Booking::where('user_id', $user->id)
                ->whereBetween('starts_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'month'   => Booking::where('user_id', $user->id)
                ->whereMonth('starts_at', $now->month)
                ->whereYear('starts_at', $now->year)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
        ];

        $services = $user->services()->where('is_active', true)->get();

        return view('dashboard', compact('todayBookings', 'stats', 'services'));
    }

    public function storeManual(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        $employee = $user->employees()->first();
        abort_if(!$employee, 422, 'Debes tener un empleado configurado.');

        $data = $request->validate([
            'service_id'    => ['required', 'exists:services,id'],
            'date'          => ['required', 'date'],
            'time'          => ['required', 'date_format:H:i'],
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone'=> ['nullable', 'string', 'max:20'],
            'customer_email'=> ['nullable', 'email', 'max:100'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ]);

        $service = \App\Models\Service::where('id', $data['service_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        $tz       = $user->timezone ?? 'Europe/Madrid';
        $startsAt = Carbon::parse($data['date'] . ' ' . $data['time'], $tz);
        $endsAt   = $startsAt->copy()->addMinutes($service->duration_minutes);

        // Check for conflicts
        $conflict = Booking::where('employee_id', $employee->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('starts_at', '<', $endsAt->copy()->utc()->toDateTimeString())
            ->where('ends_at',   '>', $startsAt->copy()->utc()->toDateTimeString())
            ->exists();

        if ($conflict) {
            return back()
                ->withErrors(['time' => 'Este horario ya está ocupado por otra cita.'])
                ->withInput();
        }

        Booking::create([
            'user_id'        => $user->id,
            'employee_id'    => $employee->id,
            'service_id'     => $service->id,
            'price'          => $service->price,
            'customer_name'  => $data['customer_name'],
            'customer_phone' => $data['customer_phone'] ?? '',
            'customer_email' => $data['customer_email'] ?? null,
            'starts_at'      => $startsAt->utc(),
            'ends_at'        => $endsAt->utc(),
            'status'         => 'confirmed',
            'notes'          => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Cita agregada manualmente.');
    }

    public function confirm(\App\Models\Booking $booking): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        abort_if($booking->user_id !== $user->id, 403);
        
        $booking->update(['status' => 'confirmed']);
        
        return back()->with('success', 'Asistencia confirmada para ' . $booking->customer_name);
    }

    public function cancel(Booking $booking, Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        abort_if($booking->user_id !== $user->id, 403);

        if ($request->boolean('blacklist') && !empty($booking->customer_email)) {
            $blacklist = \App\Models\Blacklist::firstOrCreate(
                ['user_id' => $user->id, 'email' => $booking->customer_email],
                ['strikes' => 0]
            );
            $blacklist->increment('strikes');
        }

        if (!empty($booking->customer_email)) {
            \Illuminate\Support\Facades\Mail::to($booking->customer_email)
                ->send(new \App\Mail\BookingCancelledClientMail($booking, $user));
        }

        $booking->delete();

        return back()->with('success', 'Cita cancelada correctamente.');
    }

    public function updateSchedule(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        abort_if($user->schedule_type !== 'custom', 400, 'Solo disponible en Horario Especial');

        $data = $request->validate([
            'date' => ['required', 'date'],
            'is_closed' => ['required', 'boolean'],
            'open_time' => ['nullable', 'date_format:H:i'],
            'close_time' => ['nullable', 'date_format:H:i', 'after:open_time'],
            'break_start' => ['nullable', 'date_format:H:i'],
            'break_end' => ['nullable', 'date_format:H:i', 'after:break_start'],
        ]);

        $employee = $user->employees()->first();
        if ($employee) {
            $formattedDate = \Carbon\Carbon::parse($data['date'])->toDateString();
            $schedule = $employee->customSchedules()->whereDate('date', $formattedDate)->first();
            $wasOpen = $schedule ? !$schedule->is_closed : true;
            $isNowClosed = $data['is_closed'];

            if ($schedule) {
                $schedule->update([
                    'is_closed' => $data['is_closed'],
                    'open_time' => $data['is_closed'] ? null : ($data['open_time'] ?? null),
                    'close_time' => $data['is_closed'] ? null : ($data['close_time'] ?? null),
                    'break_start' => $data['is_closed'] ? null : ($data['break_start'] ?? null),
                    'break_end' => $data['is_closed'] ? null : ($data['break_end'] ?? null),
                ]);
            } else {
                $employee->customSchedules()->create([
                    'date' => $formattedDate,
                    'is_closed' => $data['is_closed'],
                    'open_time' => $data['is_closed'] ? null : ($data['open_time'] ?? null),
                    'close_time' => $data['is_closed'] ? null : ($data['close_time'] ?? null),
                    'break_start' => $data['is_closed'] ? null : ($data['break_start'] ?? null),
                    'break_end' => $data['is_closed'] ? null : ($data['break_end'] ?? null),
                ]);
            }

            if ($wasOpen && $isNowClosed) {
                $tz = $user->timezone ?? 'Europe/Madrid';
                $startRange = \Carbon\Carbon::parse($formattedDate, $tz)->startOfDay()->utc();
                $endRange   = \Carbon\Carbon::parse($formattedDate, $tz)->endOfDay()->utc();

                $bookings = \App\Models\Booking::where('employee_id', $employee->id)
                    ->where('starts_at', '>=', $startRange)
                    ->where('starts_at', '<=', $endRange)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->with('service')
                    ->get();

                if ($bookings->isNotEmpty()) {
                    foreach ($bookings as $booking) {
                        $booking->update(['status' => 'cancelled']);
                        if (!empty($booking->customer_email)) {
                            \Illuminate\Support\Facades\Mail::to($booking->customer_email)
                                ->send(new \App\Mail\BookingCancelledClientMail($booking, $user));
                        }
                    }

                    if (!empty($user->email)) {
                        \Illuminate\Support\Facades\Mail::to($user->email)
                            ->send(new \App\Mail\DayClosedOwnerSummaryMail($bookings, $user, $formattedDate));
                    }
                }
            }
        }

        return back()->with('success', 'Horario actualizado correctamente.');
    }

    public function updateWeeklySchedule(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        abort_if($user->schedule_type !== 'normal', 400, 'Solo disponible en Horario Normal');

        if (is_array($request->input('schedules'))) {
            $request->merge([
                'schedules' => collect($request->input('schedules'))->map(function ($sch) {
                    foreach (['open', 'close', 'break_start', 'break_end'] as $k) {
                        if (isset($sch[$k]) && is_string($sch[$k]) && strlen($sch[$k]) > 5) {
                            $sch[$k] = substr($sch[$k], 0, 5);
                        }
                    }
                    return $sch;
                })->all(),
            ]);
        }

        $request->validate([
            'schedules'            => ['required', 'array'],
            'schedules.*.day'      => ['required', 'integer', 'between:0,6'],
            'schedules.*.open'     => ['required', 'date_format:H:i'],
            'schedules.*.close'    => ['required', 'date_format:H:i', 'after:schedules.*.open'],
            'schedules.*.break_start' => ['nullable', 'date_format:H:i'],
            'schedules.*.break_end'   => ['nullable', 'date_format:H:i', 'after:schedules.*.break_start'],
        ]);

        $employee = $user->employees()->first();
        if ($employee) {
            // Eliminar horarios anteriores y crear los nuevos
            $employee->schedules()->delete();

            foreach ($request->schedules as $sch) {
                $employee->schedules()->create([
                    'day_of_week'  => $sch['day'],
                    'open_time'    => $sch['open'],
                    'close_time'   => $sch['close'],
                    'break_start'  => !empty($sch['break_start']) ? $sch['break_start'] : null,
                    'break_end'    => !empty($sch['break_end']) ? $sch['break_end'] : null,
                ]);
            }
        }

        return back()->with('success', 'Horario semanal actualizado correctamente.');
    }
}
