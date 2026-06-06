<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_applications', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();

            // Applicant data — exactly what they submit
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('town');
            $table->foreignId('desired_level_id')->nullable()->constrained('levels')->nullOnDelete();
            $table->text('notes')->nullable();

            // Intake metadata
            $table->string('source')->default('public_form');     // 'public_form' | 'secretary_entry'
            $table->string('status')->default('pending');         // 'pending' | 'approved' | 'rejected'

            // Lifecycle timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('rejection_reason')->nullable();

            $table->timestamps();

            // Common query patterns: pending applications by tenant, recent applications
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_applications');
    }
};