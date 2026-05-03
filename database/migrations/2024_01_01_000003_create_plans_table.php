<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->decimal('price_monthly', 8, 2);
                $table->unsignedInteger('max_employees');
                $table->unsignedInteger('max_bookings_monthly')->nullable();
                $table->boolean('whatsapp_reminders')->default(false);
                $table->boolean('statistics')->default(false);
                $table->boolean('advanced_statistics')->default(false);
                $table->boolean('online_payment')->default(false);
                $table->boolean('embeddable_widget')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
