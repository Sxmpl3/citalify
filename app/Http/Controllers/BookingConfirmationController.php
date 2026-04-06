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
}
