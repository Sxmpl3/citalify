<?php

namespace App\Http\Controllers;

use App\Mail\BookingCancelledClientMail;
use App\Mail\DayClosedOwnerSummaryMail;
use App\Models\Booking;
use App\Models\Vacation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class VacationController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->schedule_type !== 'normal') {
            return redirect()->route('dashboard')
                ->with('error', 'Las vacaciones solo están disponibles para horario normal.');
        }

        $tz       = $user->timezone ?? 'Europe/Madrid';
        $today    = Carbon::today($tz);
        $endOfYear = Carbon::create($today->year, 12, 31, 0, 0, 0, $tz);

        $vacations = $user->vacations()
            ->where('date', '>=', $today->toDateString())
            ->where('date', '<=', $endOfYear->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Group by date string for quick lookup in the view.
        $byDate = $vacations->groupBy(fn($v) => $v->date->toDateString());

        return view('vacations.index', [
            'vacations'    => $vacations,
            'byDate'       => $byDate,
            'todayStr'     => $today->toDateString(),
            'endOfYearStr' => $endOfYear->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        abort_if($user->schedule_type !== 'normal', 400, 'Solo disponible en Horario Normal');

        $data = $request->validate([
            'date'       => ['required', 'date', 'after_or_equal:today'],
            'mode'       => ['required', 'in:full,range'],
            'start_time' => ['nullable', 'required_if:mode,range', 'regex:/^\d{2}:\d{2}$/'],
            'end_time'   => ['nullable', 'required_if:mode,range', 'regex:/^\d{2}:\d{2}$/'],
        ], [
            'start_time.required_if' => 'Indica la hora de inicio.',
            'end_time.required_if'   => 'Indica la hora de fin.',
            'start_time.regex'       => 'La hora de inicio debe tener formato HH:MM.',
            'end_time.regex'         => 'La hora de fin debe tener formato HH:MM.',
        ]);

        $tz   = $user->timezone ?? 'Europe/Madrid';
        $date = Carbon::parse($data['date'], $tz)->toDateString();

        $endOfYear = Carbon::create(Carbon::today($tz)->year, 12, 31, 0, 0, 0, $tz)->toDateString();
        if ($date > $endOfYear) {
            return back()->withErrors(['date' => 'Solo puedes elegir días dentro del año actual.']);
        }

        $isRange   = $data['mode'] === 'range';
        $startTime = null;
        $endTime   = null;

        if ($isRange) {
            $startTime = $data['start_time'] . ':00';
            $endTime   = $data['end_time']   . ':00';

            if ($endTime <= $startTime) {
                return back()
                    ->withErrors(['end_time' => 'La hora de fin debe ser posterior a la de inicio.'])
                    ->withInput();
            }
        }

        $existing = Vacation::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->get();

        $hasFullDay = $existing->contains(fn($v) => $v->isFullDay());

        if ($hasFullDay) {
            return back()->with('success', 'Este día ya está marcado como vacaciones completo.');
        }

        if (!$isRange) {
            // Adding a full-day vacation: replace any existing partial ranges.
            foreach ($existing as $v) {
                $v->delete();
            }
        } else {
            // Adding a partial range: prevent overlap with existing ranges on the same date.
            foreach ($existing as $v) {
                if ($v->isFullDay()) {
                    continue;
                }
                if ($startTime < $v->end_time && $endTime > $v->start_time) {
                    return back()
                        ->withErrors(['start_time' => 'La franja se solapa con otra ya existente este día.'])
                        ->withInput();
                }
            }
        }

        Vacation::create([
            'user_id'    => $user->id,
            'date'       => $date,
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ]);

        $this->cancelOverlappingBookings($user, $date, $tz, $startTime, $endTime);

        return back()->with('success', $isRange
            ? 'Franja de vacaciones añadida correctamente.'
            : 'Día añadido a vacaciones correctamente.');
    }

    public function destroy(Vacation $vacation): RedirectResponse
    {
        $user = auth()->user();
        abort_if($vacation->user_id !== $user->id, 403);

        $vacation->delete();

        return back()->with('success', 'Vacaciones eliminadas.');
    }

    /**
     * Cancel bookings overlapping the vacation window and notify clients.
     * When $startTime/$endTime are null, the whole day is cancelled.
     */
    private function cancelOverlappingBookings($user, string $date, string $tz, ?string $startTime, ?string $endTime): void
    {
        $employee = $user->employees()->first();
        if (!$employee) {
            return;
        }

        if ($startTime && $endTime) {
            $startRange = Carbon::parse($date . ' ' . $startTime, $tz)->utc();
            $endRange   = Carbon::parse($date . ' ' . $endTime,   $tz)->utc();

            $bookings = Booking::where('employee_id', $employee->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('starts_at', '<', $endRange)
                ->where('ends_at',   '>', $startRange)
                ->with('service')
                ->get();
        } else {
            $startRange = Carbon::parse($date, $tz)->startOfDay()->utc();
            $endRange   = Carbon::parse($date, $tz)->endOfDay()->utc();

            $bookings = Booking::where('employee_id', $employee->id)
                ->whereBetween('starts_at', [$startRange, $endRange])
                ->whereIn('status', ['pending', 'confirmed'])
                ->with('service')
                ->get();
        }

        if ($bookings->isEmpty()) {
            return;
        }

        foreach ($bookings as $booking) {
            $booking->update(['status' => 'cancelled']);

            if (!empty($booking->customer_email)) {
                Mail::to($booking->customer_email)
                    ->send(new BookingCancelledClientMail($booking, $user));
            }
        }

        if (!empty($user->email)) {
            Mail::to($user->email)
                ->send(new DayClosedOwnerSummaryMail($bookings, $user, $date));
        }
    }
}
