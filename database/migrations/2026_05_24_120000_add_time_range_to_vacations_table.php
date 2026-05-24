<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vacations')) {
            return;
        }

        Schema::table('vacations', function (Blueprint $table) {
            if (!Schema::hasColumn('vacations', 'start_time')) {
                $table->time('start_time')->nullable()->after('date');
            }
            if (!Schema::hasColumn('vacations', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
        });

        // Drop the unique (user_id, date) constraint so several time-range
        // vacations can coexist on the same date.
        try {
            Schema::table('vacations', function (Blueprint $table) {
                $table->dropUnique(['user_id', 'date']);
            });
        } catch (\Throwable $e) {
            // Index may not exist on older installs — ignore.
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('vacations')) {
            return;
        }

        Schema::table('vacations', function (Blueprint $table) {
            if (Schema::hasColumn('vacations', 'end_time')) {
                $table->dropColumn('end_time');
            }
            if (Schema::hasColumn('vacations', 'start_time')) {
                $table->dropColumn('start_time');
            }
        });

        try {
            Schema::table('vacations', function (Blueprint $table) {
                $table->unique(['user_id', 'date']);
            });
        } catch (\Throwable $e) {
            // Ignore.
        }
    }
};
