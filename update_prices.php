<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bookings = \App\Models\Booking::with('service')->get();
$count = 0;
foreach($bookings as $b) {
    if($b->service) {
        $b->update(['price' => $b->service->price]);
        $count++;
    }
}
echo "Updated $count bookings.\n";
