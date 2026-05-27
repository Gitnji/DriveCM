<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // D95 — town wasn't in the original tenants columns.
            $table->string('applicant_town')->nullable();
            // D96 — desired subdomain is separate from the live `subdomain` column.
            $table->string('desired_subdomain')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['applicant_town', 'desired_subdomain']);
        });
    }
};