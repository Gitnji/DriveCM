<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();

            $table->string('path');                  // storage path, relative to the 'local' disk
            $table->string('original_name');
            $table->string('mime');
            $table->unsignedInteger('size');         // bytes

            $table->foreignId('uploaded_by')->constrained('users');

            // Nullable: an upload may not yet be attached to a saved lesson.
            // Orphan = lesson_id null and older than a cutoff (D56 cleanup).
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};