<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            // stancl/tenancy core columns
            $table->string('id')->primary(); // UUID string (D19)

            // DriveCM identity columns (D18 — real, queryable)
            $table->string('name');                 // school display name
            $table->string('subdomain')->unique();  // schoolname -> schoolname.drivecm.cm

            // Lifecycle (D15) — indexed: Super Admin review queue filters on this
            $table->string('status')->default('pending')->index();
            // allowed values: pending | approved | active | rejected

            // Registration request fields (D15)
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('reviewed_by')->nullable(); // central admin id, set on approve/reject
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            // stancl/tenancy virtual-column store — keep for soft attributes (e.g. branding later)
            $table->json('data')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};