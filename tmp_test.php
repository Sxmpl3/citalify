<?php
$business = App\Models\User::first();
$employee = $business->employees()->first();
$tz = $business->timezone ?? 'Europe/Madrid';
$now = Carbon\Carbon::now($tz);
echo "Now: " . $now->toDateTimeString() . "\n";
// Let's print out what calendar endpoint sees for today and tomorrow
$today = Carbon\Carbon::today($tz);
for ($i=0; $i<3; $i++) {
    $date = $today->copy()->addDays($i);
    $dateStr = $date->toDateString();
    echo "\n=== DATE: $dateStr ===\n";
    $schedule = $employee->customSchedules()->where('date', $dateStr)->first();
    if (!$schedule) { echo "NO SCHEDULE\n"; continue; }
    echo "closed: " . $schedule->is_closed . "\n";
    echo "open_time: " . $schedule->open_time . "\n";
    echo "close_time: " . $schedule->close_time . "\n";
    
    $open = Carbon\Carbon::parse($date->toDateString() . ' ' . $schedule->open_time, $tz);
    $close = Carbon\Carbon::parse($date->toDateString() . ' ' . $schedule->close_time, $tz);
    $duration = 30;
    $cursor = $open->copy();
    $slots = [];
    $hasSlot = false;
    while ($cursor->copy()->addMinutes($duration)->lte($close)) {
         if ($cursor->gte($now)) {
            $hasSlot = true;
            $slots[] = $cursor->format('H:i');
         }
         $cursor->addMinutes($duration);
    }
    echo "Has slot: " . ($hasSlot ? "yes" : "no") . "\n";
    print_r($slots);
}
