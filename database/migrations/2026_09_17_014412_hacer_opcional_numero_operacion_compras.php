<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE compras MODIFY numero_operacion VARCHAR(50) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE compras MODIFY numero_operacion VARCHAR(50) NOT NULL');
    }
};