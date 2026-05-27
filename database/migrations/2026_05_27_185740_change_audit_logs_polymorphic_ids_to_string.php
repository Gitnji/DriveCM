<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Postgres requires an explicit USING clause when converting bigint to varchar.
        // Doctrine's ->change() does not emit USING, so do it via raw ALTER TABLE.
        // (Two columns: subject_id, actor_id — both polymorphic ids that may hold UUID strings.)
        DB::statement('ALTER TABLE audit_logs ALTER COLUMN subject_id TYPE VARCHAR(255) USING subject_id::text');
        DB::statement('ALTER TABLE audit_logs ALTER COLUMN actor_id   TYPE VARCHAR(255) USING actor_id::text');
    }

    public function down(): void
    {
        // Reverting requires that every existing value be coercible to bigint.
        // If any UUID strings have been written by then, this WILL fail — and that is correct.
        DB::statement('ALTER TABLE audit_logs ALTER COLUMN subject_id TYPE BIGINT USING subject_id::bigint');
        DB::statement('ALTER TABLE audit_logs ALTER COLUMN actor_id   TYPE BIGINT USING actor_id::bigint');
    }
};