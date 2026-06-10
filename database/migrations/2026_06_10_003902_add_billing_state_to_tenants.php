<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('billing_status', 20)->default('active');
            $table->timestamp('current_billing_period_start')->nullable();
            $table->timestamp('next_billing_due')->nullable();
            $table->timestamp('last_paid_at')->nullable();
            $table->timestamp('terms_agreed_at')->nullable();
        });

        // FB1 — backfill: existing tenants get a 30-day trial window starting now.
        // Per the locked decision: legacy tenants treated as if they just started.
        DB::table('tenants')->whereNull('next_billing_due')->update([
            'billing_status'               => 'active',
            'current_billing_period_start' => now(),
            'next_billing_due'             => now()->addDays(30),
        ]);
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'billing_status',
                'current_billing_period_start',
                'next_billing_due',
                'last_paid_at',
                'terms_agreed_at',
            ]);
        });
    }
};