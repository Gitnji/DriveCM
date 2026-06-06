<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes(); // adds deleted_at timestamp, nullable
        });

        // STAFF (D168) — partial unique index so soft-deleted users don't block re-using
        // their email for a new staff member. Postgres-specific partial index.
        DB::statement('ALTER TABLE users DROP CONSTRAINT users_tenant_id_email_unique');
        DB::statement('CREATE UNIQUE INDEX users_tenant_id_email_unique ON users (tenant_id, email) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX users_tenant_id_email_unique');
        DB::statement('ALTER TABLE users ADD CONSTRAINT users_tenant_id_email_unique UNIQUE (tenant_id, email)');

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};