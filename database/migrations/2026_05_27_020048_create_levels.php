<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedTinyInteger('position'); // 1-5, fixed (D33)
            $table->string('name');                  // editable per school (D33)
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'position']); // each school: one level per position
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};