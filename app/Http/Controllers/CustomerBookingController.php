<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CustomerOtp;
use App\Models\User;
use App\Mail\CustomerLoginOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CustomerBookingController extends Controller
{
    // Muestra el formulario para introducir el email
    public function showLogin()
    {
        return view('customer.login');
    }

    // Genera y envía el OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        CustomerOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'code' => $otp,
                'expires_at' => now()->addMinutes(15),
            ]
        );

        Mail::to($request->email)->send(new CustomerLoginOtpMail($request->email, $otp));

        return redirect()->route('customer.verify', ['email' => $request->email]);
    }

    // Muestra el formulario para introducir el OTP
    public function showVerify(Request $request)
    {
        $email = $request->email;
        if (!$email) return redirect()->route('customer.login');

        return view('customer.verify', compact('email'));
    }

    // Verifica el OTP y loguea al cliente en la sesión
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code'  => ['required', 'string', 'size:6'],
        ]);

        $otpRecord = CustomerOtp::where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$otpRecord || $otpRecord->isExpired()) {
            return back()->withErrors(['code' => 'Código inválido o expirado.']);
        }

        // Marcar correo en la sesión
        session(['customer_email' => $request->email]);
        
        $otpRecord->delete();

        return redirect()->route('customer.index');
    }

    // Dashboard del cliente: lista todas sus reservas
    public function index()
    {
        $email = session('customer_email');
        if (!$email) return redirect()->route('customer.login');

        $bookings = Booking::where('customer_email', $email)
            ->with(['user', 'service'])
            ->orderBy('starts_at', 'desc')
            ->get();

        return view('customer.index', compact('bookings', 'email'));
    }

    // Muestra formulario de reprogramación
    public function reschedule(Booking $booking)
    {
        $email = session('customer_email');
        if (!$email || $booking->customer_email !== $email) {
            abort(403);
        }

        return view('customer.reschedule', compact('booking'));
    }

    // Procesa el cambio de fecha/hora
    public function update(Booking $booking, Request $request)
    {
        $email = session('customer_email');
        if (!$email || $booking->customer_email !== $email) {
            abort(403);
        }

        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'regex:/^\d{2}:\d{2}$/'],
        ]);

        $tz = $booking->user->timezone ?? 'Europe/Madrid';
        $startsAt = Carbon::parse($request->date . ' ' . $request->time, $tz)->utc();
        $endsAt = $startsAt->copy()->addMinutes($booking->service->duration_minutes);

        // Check availability
        $conflict = Booking::where('employee_id', $booking->employee_id)
            ->where('id', '!=', $booking->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('starts_at', '<', $endsAt->toDateTimeString())
            ->where('ends_at', '>', $startsAt->toDateTimeString())
            ->exists();

        if ($conflict) {
            return back()->withErrors(['time' => 'Ese horario ya no está disponible.']);
        }

        $oldStartsAt = $booking->starts_at;

        $booking->update([
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        // Notificar al dueño del cambio
        try {
            Mail::to($booking->user->email)->send(new \App\Mail\BookingRescheduledOwnerMail($booking, $oldStartsAt));
            Mail::to($booking->customer_email)->send(new \App\Mail\BookingRescheduledClientMail($booking));
        } catch (\Exception $e) {
            \Log::error("Error enviando correos de reprogramación: " . $e->getMessage());
        }

        return redirect()->route('customer.index')->with('success', 'Cita reprogramada correctamente.');
    }

    // Cancelación de reserva por parte del cliente
    public function cancel(Booking $booking)
    {
        $email = session('customer_email');
        if (!$email || $booking->customer_email !== $email) {
            abort(403);
        }

        if ($booking->status !== 'cancelled') {
            $booking->update(['status' => 'cancelled']);

            // Notificar al dueño
            try {
                Mail::to($booking->user->email)->send(new \App\Mail\BookingCancelledOwnerMail($booking));
            } catch (\Exception $e) {}
        }

        return back()->with('success', 'Reserva cancelada correctamente.');
    }

    // Cerrar sesión
    public function logout()
    {
        session()->forget('customer_email');
        return redirect()->route('customer.login');
    }
}
