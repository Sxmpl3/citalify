<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Collection;

class AutoConfirmationSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bookings;
    public $user;
    public $date;

    /**
     * Create a new message instance.
     */
    public function __construct(Collection $bookings, User $user, $date)
    {
        $this->bookings = $bookings;
        $this->user = $user;
        $this->date = $date;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Resumen: Citas confirmadas automáticamente hoy')
                    ->view('emails.auto-confirmation-summary');
    }
}
