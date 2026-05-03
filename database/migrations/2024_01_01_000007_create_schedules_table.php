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
        if (!Schema::hasTable('schedules')) {
            Schema::create('schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
                $table->unsignedTinyInteger('day_of_week'); // 0=Domingo, 1=Lunes, ..., 6=Sábado
                $table->time('open_time');
                $table->time('close_time');
                $table->time('break_start')->nullable();
                $table->time('break_end')->nullable();
                $table->timestamps();

                $table->unique(['employee_id', 'day_of_week']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
