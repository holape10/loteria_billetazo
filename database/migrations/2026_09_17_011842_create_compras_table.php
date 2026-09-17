<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('sorteo_id')->constrained('sorteos')->cascadeOnDelete();
            $table->unsignedInteger('cantidad_jugadas');
            $table->decimal('monto_total', 8, 2);
            $table->enum('metodo_pago', ['yape', 'plin']);
            $table->string('numero_operacion', 50);
            $table->string('comprobante')->nullable();
            $table->enum('estado_pago', ['pendiente', 'pagado', 'rechazado'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};