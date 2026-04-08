<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class BookingRescheduledOwnerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $oldStartsAt;

    public function __construct(Booking $booking, $oldStartsAt)
    {
        $this->booking = $booking;
        $this->oldStartsAt = $oldStartsAt;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cita Reprogramada: ' . $this->booking->customer_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-rescheduled-owner',
        );
    }
}
