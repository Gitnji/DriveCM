<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();

            // Tenant scoping (D7). String to match tenants.id UUID (D19).
            $table->string('tenant_id');
            $table->index('tenant_id');

            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->unsignedInteger('amount_xaf'); // P1 — whole XAF, no subdivisions

            // P1 — required types trigger automatic prompts; optional types are catalog-only.
            $table->boolean('is_required')->default(false);
            // P1 — null for optional types; 0+ for required types (lessons of level N completed).
            $table->unsignedTinyInteger('levels_required_before_prompt')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->softDeletes(); // D169-style soft-delete pattern
            $table->timestamps();

            // Composite index for the listing query (active + sorted).
            $table->index(['tenant_id', 'is_active', 'sort_order'], 'payment_types_listing_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_types');
    }
};