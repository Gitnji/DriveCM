<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Existing rows have NULL or text content; convert column to jsonb.
        // USING clause handles the type cast for any existing data.
        DB::statement('ALTER TABLE lessons ALTER COLUMN content TYPE jsonb USING content::jsonb');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE lessons ALTER COLUMN content TYPE text USING content::text');
    }
};