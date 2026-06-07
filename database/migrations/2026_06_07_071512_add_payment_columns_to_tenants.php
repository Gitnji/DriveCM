<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // P2 (Flow A) — tenant payment receiving info. All nullable — schools
            // accept whichever methods they support.
            $table->string('momo_number', 40)->nullable();
            $table->string('orange_number', 40)->nullable();
            $table->text('payment_instructions')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['momo_number', 'orange_number', 'payment_instructions']);
        });
    }
};