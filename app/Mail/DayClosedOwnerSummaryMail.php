<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Collection;
use App\Models\User;

class DayClosedOwnerSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bookings;
    public $business;
    public $dateString;

    /**
     * Create a new message instance.
     */
    public function __construct(Collection $bookings, User $business, string $dateString)
    {
        $this->bookings = $bookings;
        $this->business = $business;
        $this->dateString = $dateString;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Resumen de citas anuladas - ' . \Carbon\Carbon::parse($this->dateString)->format('d/m/Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.day-closed-owner-summary',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
