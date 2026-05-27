<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // the student

            $table->unsignedTinyInteger('score');       // 0-100, percentage
            $table->boolean('passed');                  // score >= lesson.pass_threshold at attempt time
            $table->json('answers')->nullable();        // what the student picked, for review

            $table->timestamp('created_at')->nullable(); // append-only history — no updated_at

            $table->index(['tenant_id', 'user_id', 'lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_attempts');
    }
};