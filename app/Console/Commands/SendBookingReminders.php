<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\BookingReminderMail;

class SendBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send confirmation and reminder emails to customers for tomorrow\'s appointments.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Buscando citas para mañana...");

        $tomorrow = Carbon::tomorrow();
        $startOfTomorrow = $tomorrow->copy()->startOfDay()->toDateTimeString();
        $endOfTomorrow = $tomorrow->copy()->endOfDay()->toDateTimeString();

        $bookings = Booking::whereIn('status', ['pending', 'confirmed'])
            ->whereNotNull('customer_email')
            ->where('starts_at', '>=', $startOfTomorrow)
            ->where('starts_at', '<=', $endOfTomorrow)
            ->with(['service', 'employee.user'])
            ->get();

        if ($bookings->isEmpty()) {
            $this->info("No hay citas pendientes con email para mañana.");
            return;
        }

        foreach ($bookings as $booking) {
            $business = $booking->employee->user;
            
            // Generar Signed URL de confirmación (caduca en 2 días o sin caducidad)
            $confirmationUrl = URL::signedRoute('booking.confirm', ['booking' => $booking->id]);

            try {
                Mail::to($booking->customer_email)->send(new BookingReminderMail($booking, $business, $confirmationUrl));
                $this->info("Recordatorio enviado a {$booking->customer_email} para cita #{$booking->id}");
            } catch (\Exception $e) {
                $this->error("Error al enviar email a {$booking->customer_email}: {$e->getMessage()}");
            }
        }

        $this->info("Proceso de recordatorio finalizado.");
    }
}
