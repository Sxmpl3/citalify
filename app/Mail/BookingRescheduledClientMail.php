<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class BookingRescheduledClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación: Tu cita en ' . $this->booking->user->business_name . ' ha sido reprogramada',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-rescheduled-client',
        );
    }
}
