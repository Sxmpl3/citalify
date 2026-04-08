<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingConfirmationController extends Controller
{
    public function confirm(Request $request, Booking $booking)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'El enlace de confirmación es inválido o ha expirado.');
        }

        if ($booking->status !== 'confirmed') {
            $booking->update(['status' => 'confirmed']);
        }

        return view('booking.successfully-confirmed', compact('booking'));
    }

    public function cancel(Request $request, Booking $booking)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'El enlace de cancelación es inválido o ha expirado.');
        }

        if ($booking->status !== 'cancelled') {
            $booking->update(['status' => 'cancelled']);

            // Notificar al dueño del negocio
            \Illuminate\Support\Facades\Mail::to($booking->user->email)
                ->send(new \App\Mail\BookingCancelledOwnerMail($booking));
        }

        return view('booking.successfully-cancelled', compact('booking'));
    }
}
