<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sorteos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->unsignedTinyInteger('numero_1')->nullable();
            $table->unsignedTinyInteger('numero_2')->nullable();
            $table->unsignedTinyInteger('numero_3')->nullable();
            $table->unsignedTinyInteger('numero_4')->nullable();
            $table->unsignedTinyInteger('numero_5')->nullable();
            $table->unsignedTinyInteger('numero_6')->nullable();
            $table->unsignedInteger('boletos_vendidos')->default(0);
            $table->decimal('premio_mayor', 10, 2)->default(1000.00);
            $table->decimal('premio_cinco_aciertos', 10, 2)->default(100.00);
            $table->decimal('premio_cuatro_aciertos', 10, 2)->default(50.00);
            $table->enum('estado', ['pendiente', 'jugado', 'cerrado'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sorteos');
    }
};