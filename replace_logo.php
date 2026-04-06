<?php
$files = [
    'resources/views/welcome.blade.php',
    'resources/views/layouts/onboarding.blade.php',
    'resources/views/layouts/guest.blade.php',
    'resources/views/components/onboarding-layout.blade.php',
    'resources/views/booking/successfully-confirmed.blade.php',
    'resources/views/booking/show.blade.php',
    'resources/views/booking/owner.blade.php',
    'resources/views/booking/confirmed.blade.php'
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        $c = preg_replace('/<span class="w-[89] h-[89] rounded-xl flex items-center justify-center bg-gradient-to-br from-emerald-600 to-emerald-700[^>]*>.*?<\/span>/s', '<img src="{{ asset(\'img/logo.png\') }}" alt="Citalify Logo" class="h-9 w-auto rounded-xl">', $c);
        file_put_contents($f, $c);
        echo "Replaced in $f\n";
    }
}
