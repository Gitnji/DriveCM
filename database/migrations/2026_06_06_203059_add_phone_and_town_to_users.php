<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // STUDENT (D169) — contact data, populated on student approval, editable thereafter.
            // Nullable: existing users (owner, secretary, instructor created via tinker, etc.)
            // don't have this data and shouldn't be forced to backfill.
            $table->string('phone')->nullable()->after('email');
            $table->string('town')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'town']);
        });
    }
};