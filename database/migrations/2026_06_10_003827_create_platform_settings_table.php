<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('monthly_fee_xaf');
            $table->unsignedSmallInteger('free_trial_days')->default(30);
            $table->string('momo_number', 40)->nullable();
            $table->string('orange_number', 40)->nullable();
            $table->text('payment_instructions')->nullable();
            $table->timestamps();
        });

        // Seed the single row with the locked initial values.
        DB::table('platform_settings')->insert([
            'id'                   => 1,
            'monthly_fee_xaf'      => 30000,
            'free_trial_days'      => 30,
            'momo_number'          => null,
            'orange_number'        => null,
            'payment_instructions' => null,
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};