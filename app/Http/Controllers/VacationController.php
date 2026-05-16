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
            ->get()
            ->map(fn($v) => $v->date->toDateString())
            ->values();

        return view('vacations.index', [
            'vacations'   => $vacations,
            'todayStr'    => $today->toDateString(),
            'endOfYearStr'=> $endOfYear->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        abort_if($user->schedule_type !== 'normal', 400, 'Solo disponible en Horario Normal');

        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $tz   = $user->timezone ?? 'Europe/Madrid';
        $date = Carbon::parse($data['date'], $tz)->toDateString();

        $endOfYear = Carbon::create(Carbon::today($tz)->year, 12, 31, 0, 0, 0, $tz)->toDateString();
        if ($date > $endOfYear) {
            return back()->withErrors(['date' => 'Solo puedes elegir días dentro del año actual.']);
        }

        $vacation = Vacation::firstOrCreate([
            'user_id' => $user->id,
            'date'    => $date,
        ]);

        if (!$vacation->wasRecentlyCreated) {
            return back()->with('success', 'Este día ya estaba marcado como vacaciones.');
        }

        $employee = $user->employees()->first();

        if ($employee) {
            $startRange = Carbon::parse($date, $tz)->startOfDay()->utc();
            $endRange   = Carbon::parse($date, $tz)->endOfDay()->utc();

            $bookings = Booking::where('employee_id', $employee->id)
                ->where('starts_at', '>=', $startRange)
                ->where('starts_at', '<=', $endRange)
                ->whereIn('status', ['pending', 'confirmed'])
                ->with('service')
                ->get();

            if ($bookings->isNotEmpty()) {
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

        return back()->with('success', 'Día añadido a vacaciones correctamente.');
    }

    public function destroy(Vacation $vacation): RedirectResponse
    {
        $user = auth()->user();
        abort_if($vacation->user_id !== $user->id, 403);

        $vacation->delete();

        return back()->with('success', 'Día de vacaciones eliminado.');
    }
}
