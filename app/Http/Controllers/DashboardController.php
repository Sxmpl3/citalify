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
        $tz   = $user->timezone ?? 'Europe/Madrid';
        $now  = Carbon::now($tz);
        $today = Carbon::today($tz);

        $todayBookings = Booking::where('user_id', $user->id)
            ->whereDate('starts_at', $today)
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

        Booking::create([
            'user_id'        => $user->id,
            'employee_id'    => $employee->id,
            'service_id'     => $service->id,
            'price'          => $service->price,
            'customer_name'  => $data['customer_name'],
            'customer_phone' => $data['customer_phone'] ?? '',
            'customer_email' => $data['customer_email'],
            'starts_at'      => $startsAt,
            'ends_at'        => $endsAt,
            'status'         => 'confirmed',
            'notes'          => $data['notes'],
        ]);

        return back()->with('success', 'Cita agregada manualmente.');
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

        $booking->delete();

        return back()->with('success', 'Cita cancelada correctamente.');
    }
}
