<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();

            $table->text('prompt');
            $table->string('type')->default('mcq'); // mcq | true_false (D34)
            $table->unsignedSmallInteger('position');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};