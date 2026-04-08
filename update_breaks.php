<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$slug = 'peluqueria-izann';
$user = App\Models\User::where('business_slug', $slug)->first();

if ($user) {
    $employee = $user->employees()->first();
    if ($employee) {
        $employee->schedules()->update([
            'break_start' => '14:00',
            'break_end' => '15:00'
        ]);
        echo "UPDATED BREAKS FOR $slug\n";
    }
}
