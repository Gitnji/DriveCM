<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->unique();        // one settings row per tenant

            $table->jsonb('theme')->nullable();           // colors, fonts, etc. (D123)
            $table->foreignId('logo_upload_id')->nullable()->constrained('uploads')->nullOnDelete();
            $table->jsonb('nav_config')->nullable();      // tenant-defined nav menu

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};