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
        if (!Schema::hasTable('pending_bookings')) {
            Schema::create('pending_bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
                $table->foreignId('service_id')->constrained()->cascadeOnDelete();
                $table->string('customer_name');
                $table->string('customer_phone');
                $table->string('customer_email');
                $table->dateTime('starts_at');
                $table->dateTime('ends_at');
                $table->decimal('price', 10, 2);
                $table->text('notes')->nullable();
                $table->string('verification_code', 6);
                $table->timestamp('expires_at');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_bookings');
    }
};
