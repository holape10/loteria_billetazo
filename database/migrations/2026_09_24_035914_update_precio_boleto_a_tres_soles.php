<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE boletos MODIFY monto DECIMAL(8,2) NOT NULL DEFAULT 3.00');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE boletos MODIFY monto DECIMAL(8,2) NOT NULL DEFAULT 2.00');
    }
};