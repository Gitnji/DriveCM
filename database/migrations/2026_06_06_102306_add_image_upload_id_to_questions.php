<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // P3a — optional image attached to a question (e.g., road sign, scenario diagram).
            // nullOnDelete: if an upload is deleted, the question survives with null image.
            $table->foreignId('image_upload_id')
                  ->nullable()
                  ->after('prompt')
                  ->constrained('uploads')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['image_upload_id']);
            $table->dropColumn('image_upload_id');
        });
    }
};