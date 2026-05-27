<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practical_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();

            $table->foreignId('student_id')->constrained('users');
            $table->foreignId('instructor_id')->constrained('users'); // required (D86)

            $table->dateTime('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes'); // planned at scheduling (D84)

            // D83 lifecycle
            $table->string('status')->default('scheduled')->index();
            // scheduled | completed | cancelled | absent

            $table->text('notes')->nullable();

            // D85 — was the theory gate overridden by an instructor at scheduling?
            $table->boolean('theory_gate_overridden')->default(false);

            // Attendance marking — nullable until completed/marked
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users');

            $table->timestamps();

            $table->index(['tenant_id', 'student_id']);
            $table->index(['tenant_id', 'instructor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practical_sessions');
    }
};