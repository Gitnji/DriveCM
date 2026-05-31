<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();

            $table->string('title');
            $table->string('slug');                       // unique per tenant (enforced below)
            $table->boolean('is_home')->default(false);
            $table->string('status')->default('draft');   // draft | published
            $table->unsignedInteger('position')->default(0); // nav order

            $table->jsonb('content')->nullable();         // ordered block array (D124)

            // SEO (used in a later batch)
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();

            $table->timestamps();

            // Slug unique within a tenant (not globally — two schools can both have 'about').
            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};