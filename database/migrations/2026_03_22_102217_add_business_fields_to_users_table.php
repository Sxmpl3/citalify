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
        Schema::table('users', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('name');
            $table->string('business_slug')->nullable()->unique()->after('business_name');
            $table->string('phone')->nullable()->after('business_slug');
            $table->string('logo')->nullable()->after('phone');
            $table->string('address')->nullable()->after('logo');
            $table->string('timezone')->default('Europe/Madrid')->after('address');
            $table->foreignId('plan_id')->nullable()->constrained('plans')->after('timezone');
            $table->string('stripe_customer_id')->nullable()->after('plan_id');
            $table->timestamp('trial_ends_at')->nullable()->after('stripe_customer_id');
            $table->boolean('onboarding_completed')->default(false)->after('trial_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn([
                'business_name', 'business_slug', 'phone', 'logo', 'address',
                'timezone', 'plan_id', 'stripe_customer_id', 'trial_ends_at', 'onboarding_completed',
            ]);
        });
    }
};
