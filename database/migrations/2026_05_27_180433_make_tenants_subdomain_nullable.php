<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // D15 lifecycle: a pending tenant has no live subdomain yet — it is set
        // at approval (D96). The NOT NULL on subdomain pre-dates moderated
        // registration; relaxing it to nullable so pending tenants can exist.
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('subdomain')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Can't safely reverse — if any tenant has subdomain=null, this would
        // fail. Treat as forward-only (rollback would require backfilling).
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('subdomain')->nullable(false)->change();
        });
    }
};