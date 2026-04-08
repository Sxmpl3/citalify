<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AutoConfirmationSummaryMail;

class AutoConfirmEndDayBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:auto-confirm-end-day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically confirm all pending bookings for the current day at the end of the day.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Iniciando proceso de auto-confirmación de fin de día...");

        // Iteramos por todos los usuarios para respetar sus timezones y agendas
        $users = User::all();

        foreach ($users as $user) {
            $tz = $user->timezone ?? 'Europe/Madrid';
            $todayDate = Carbon::now($tz)->toDateString();
            
            // Inicio y fin del día en UTC para la consulta
            $startOfDay = Carbon::now($tz)->startOfDay()->utc();
            $endOfDay = Carbon::now($tz)->endOfDay()->utc();

            $pendingBookings = Booking::where('user_id', $user->id)
                ->where('status', 'pending')
                ->where('starts_at', '>=', $startOfDay)
                ->where('starts_at', '<=', $endOfDay)
                ->with('service')
                ->get();

            if ($pendingBookings->isNotEmpty()) {
                $this->info("Confirmando {$pendingBookings->count()} citas para el negocio: {$user->business_name}");

                // Actualizar a confirmadas
                Booking::whereIn('id', $pendingBookings->pluck('id'))->update(['status' => 'confirmed']);

                // Enviar resumen al propietario
                if (!empty($user->email)) {
                    try {
                        Mail::to($user->email)->send(new AutoConfirmationSummaryMail($pendingBookings, $user, $todayDate));
                        $this->info("Resumen enviado a {$user->email}");
                    } catch (\Exception $e) {
                        $this->error("Error al enviar email a {$user->email}: " . $e->getMessage());
                    }
                }
            }
        }

        $this->info("Proceso de auto-confirmación finalizado.");
    }
}
