<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('monto_detectado', 10, 2)->nullable()->after('monto_total');
            $table->boolean('requiere_revision')->default(false)->after('monto_detectado');
        });
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn(['monto_detectado', 'requiere_revision']);
        });
    }
};