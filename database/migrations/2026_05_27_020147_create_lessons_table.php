<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->foreignId('level_id')->constrained('levels');

            $table->string('title');
            $table->longText('content')->nullable();   // lesson body (HTML/markdown — decided at authoring batch)
            $table->unsignedSmallInteger('position');   // order within the level

            // Draft/published (D5) — students never see drafts
            $table->string('status')->default('draft')->index(); // draft | published

            // Pass threshold — per-lesson column, default 80 (D36)
            $table->unsignedTinyInteger('pass_threshold')->default(80);

            // Theory time credit toward the Ministry report (D12) — minutes
            $table->unsignedSmallInteger('duration_minutes')->default(0);

            $table->timestamps();

            $table->index(['tenant_id', 'level_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};