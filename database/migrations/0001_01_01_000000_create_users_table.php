<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Tenant scoping (D7 — single-DB + tenant_id). UUID to match tenants.id (D19).
            $table->string('tenant_id');
            $table->index('tenant_id');

            $table->string('name');
            $table->string('email');
            $table->string('password');

            // DriveCM role — in-school roles only (D3). Super Admin is a separate central table.
            $table->string('role'); // owner | secretary | instructor | student

            // Per-user language (D14)
            $table->string('language', 2)->default('en'); // en | fr

            // Forced first-login password change (blueprint §8.1)
            $table->boolean('must_change_password')->default(true);

            $table->rememberToken();
            $table->timestamps();

            // Email is unique PER SCHOOL, not globally — same email could exist at two schools
            $table->unique(['tenant_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};