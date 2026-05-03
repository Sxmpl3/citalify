<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('custom_schedules')) {
            Schema::create('custom_schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
                $table->date('date');
                $table->time('open_time')->nullable();
                $table->time('close_time')->nullable();
                $table->time('break_start')->nullable();
                $table->time('break_end')->nullable();
                $table->boolean('is_closed')->default(false);
                $table->timestamps();

                $table->unique(['employee_id', 'date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_schedules');
    }
};
