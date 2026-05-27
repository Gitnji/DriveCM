<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_validations', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->foreignId('student_id')->constrained('users');
            $table->foreignId('validated_by')->constrained('users');

            // Snapshot of hours at validation time (D90).
            $table->unsignedInteger('theory_minutes');
            $table->unsignedInteger('practical_minutes');

            $table->timestamp('created_at')->nullable(); // append-only (D93) — no updated_at

            $table->index(['tenant_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_validations');
    }
};