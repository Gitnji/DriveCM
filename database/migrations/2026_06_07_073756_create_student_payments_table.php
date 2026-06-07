<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();

            $table->string('tenant_id');
            $table->index('tenant_id');

            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payment_type_id')->constrained('payment_types');

            // P3 — status lifecycle.
            $table->string('status'); // pending_review | approved | rejected

            // P3 — screenshot upload (null when manual_mark by owner/secretary for cash).
            $table->foreignId('screenshot_upload_id')->nullable()->constrained('uploads')->nullOnDelete();

            // P3 — amount snapshot at creation. If school later edits the type's amount,
            // existing payment records keep their original amount.
            $table->unsignedInteger('amount_xaf');

            $table->text('notes')->nullable();              // student's optional message
            $table->text('rejection_reason')->nullable();   // reviewer's reason on reject

            // P3 — distinguishes student-uploaded screenshots from manual-marked cash payments.
            $table->string('created_via'); // student_upload | manual_mark

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            // P3 — is-blocked check: find approved payments for a student. Covers the hot path.
            $table->index(['tenant_id', 'student_id', 'status'], 'student_payments_blocking_idx');
            // P5 — review queue: find pending submissions for a tenant.
            $table->index(['tenant_id', 'status'], 'student_payments_queue_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_payments');
    }
};