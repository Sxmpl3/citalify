<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$slug = 'izancano';
$user = App\Models\User::where('business_slug', $slug)->first();

if (!$user) {
    echo "USER NOT FOUND\n";
    exit;
}

$employee = $user->employees()->first();
if (!$employee) {
    echo "EMPLOYEE NOT FOUND\n";
    exit;
}

echo "Schedule Type: " . $user->schedule_type . "\n";

if ($user->schedule_type === 'normal') {
    $schedules = $employee->schedules;
    foreach ($schedules as $s) {
        echo "Day: {$s->day_of_week}, Open: {$s->open_time}, Close: {$s->close_time}, Break Start: " . ($s->break_start ?? 'NULL') . ", Break End: " . ($s->break_end ?? 'NULL') . "\n";
    }
} else {
    $schedules = $employee->customSchedules;
    foreach ($schedules as $s) {
        echo "Date: {$s->date}, Open: {$s->open_time}, Close: {$s->close_time}, Break Start: " . ($s->break_start ?? 'NULL') . ", Break End: " . ($s->break_end ?? 'NULL') . "\n";
    }
}
