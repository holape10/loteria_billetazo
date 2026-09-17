<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boletos', function (Blueprint $table) {
            $table->foreignId('compra_id')->nullable()->after('sorteo_id')->constrained('compras')->cascadeOnDelete();
        });

        Schema::table('boletos', function (Blueprint $table) {
            $table->dropColumn(['metodo_pago', 'numero_operacion', 'estado_pago']);
        });

        DB::statement('ALTER TABLE boletos MODIFY monto DECIMAL(8,2) NOT NULL DEFAULT 1.00');
    }

    public function down(): void
    {
        Schema::table('boletos', function (Blueprint $table) {
            $table->dropForeign(['compra_id']);
            $table->dropColumn('compra_id');
            $table->enum('metodo_pago', ['yape', 'plin'])->nullable();
            $table->string('numero_operacion', 50)->nullable();
            $table->enum('estado_pago', ['pendiente', 'pagado', 'rechazado'])->default('pendiente');
        });

        DB::statement('ALTER TABLE boletos MODIFY monto DECIMAL(8,2) NOT NULL DEFAULT 2.00');
    }
};