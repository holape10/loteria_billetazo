<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boletos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('sorteo_id')->constrained('sorteos')->cascadeOnDelete();
            $table->unsignedTinyInteger('numero_1');
            $table->unsignedTinyInteger('numero_2');
            $table->unsignedTinyInteger('numero_3');
            $table->unsignedTinyInteger('numero_4');
            $table->unsignedTinyInteger('numero_5');
            $table->unsignedTinyInteger('numero_6');
            $table->decimal('monto', 8, 2)->default(2.00);
            $table->enum('metodo_pago', ['yape', 'plin'])->nullable();
            $table->string('numero_operacion', 50)->nullable();
            $table->enum('estado_pago', ['pendiente', 'pagado', 'rechazado'])->default('pendiente');
            $table->unsignedTinyInteger('aciertos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boletos');
    }
};