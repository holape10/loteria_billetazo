<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY rol ENUM('administrador','moderador','jugador','superadmin') NOT NULL DEFAULT 'jugador'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY rol ENUM('administrador','jugador') NOT NULL DEFAULT 'jugador'");
    }
};