<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OwnerDailyAgendaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $confirmed;
    public $pending;
    public $date;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Collection $confirmed, Collection $pending, string $date)
    {
        $this->user = $user;
        $this->confirmed = $confirmed;
        $this->pending = $pending;
        $this->date = $date;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Agenda de Mañana - Citalify',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.owner-daily-agenda',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
