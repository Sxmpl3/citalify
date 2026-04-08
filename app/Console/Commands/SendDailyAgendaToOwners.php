<?php

namespace App\Console\Commands;

use App\Mail\OwnerDailyAgendaMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyAgendaToOwners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-daily-agenda';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a daily agenda summary for tomorrow to all business owners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to send daily agendas...');

        $businesses = User::where('onboarding_completed', true)->get();

        foreach ($businesses as $business) {
            $tz = $business->timezone ?? 'Europe/Madrid';
            $tomorrow = Carbon::tomorrow($tz);
            $tomorrowDateStr = $tomorrow->toDateString();

            // Range in UTC
            $start = $tomorrow->copy()->startOfDay()->utc();
            $end = $tomorrow->copy()->endOfDay()->utc();

            $bookings = $business->bookings()
                ->where('starts_at', '>=', $start)
                ->where('starts_at', '<=', $end)
                ->whereIn('status', ['pending', 'confirmed'])
                ->with('service')
                ->orderBy('starts_at')
                ->get();

            if ($bookings->count() > 0) {
                $confirmed = $bookings->where('status', 'confirmed');
                $pending = $bookings->where('status', 'pending');

                Mail::to($business->email)->send(new OwnerDailyAgendaMail($business, $confirmed, $pending, $tomorrowDateStr));
                
                $this->line("Sent agenda to {$business->business_name} ({$bookings->count()} bookings)");
            } else {
                // Decision: Should we send an email if there are NO bookings? 
                // User requirement implies "se enviara un correo... indicando las citas", 
                // usually we don't spam if empty, but for now I'll skip to keep it clean.
                $this->line("No bookings tomorrow for {$business->business_name}, skipping.");
            }
        }

        $this->info('Daily agenda process completed.');
    }
}
