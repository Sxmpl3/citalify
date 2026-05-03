<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::table('plans')->insert([
            [
                'name' => 'Básico',
                'slug' => 'basico',
                'price_monthly' => 9.99,
                'max_employees' => 1,
                'max_bookings_monthly' => 500,
                'whatsapp_reminders' => false,
                'statistics' => false,
                'advanced_statistics' => false,
                'online_payment' => false,
                'embeddable_widget' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Avanzado',
                'slug' => 'avanzado',
                'price_monthly' => 19.99,
                'max_employees' => 10,
                'max_bookings_monthly' => null,
                'whatsapp_reminders' => true,
                'statistics' => true,
                'advanced_statistics' => true,
                'online_payment' => true,
                'embeddable_widget' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
