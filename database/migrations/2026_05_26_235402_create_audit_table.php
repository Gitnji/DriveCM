<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Nullable: null = platform-level event, set = tenant-level event (D21)
            $table->string('tenant_id')->nullable()->index();

            // Who performed the action (polymorphic — Admin or User)
            $table->string('actor_type')->nullable(); // 'admin' | 'user'
            $table->unsignedBigInteger('actor_id')->nullable();

            // What happened
            $table->string('action')->index(); // e.g. 'tenant.approved', 'report.validated'

            // What it was performed on (polymorphic, optional)
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            // Optional structured context
            $table->json('detail')->nullable();

            $table->timestamp('created_at')->nullable(); // log is append-only — no updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};