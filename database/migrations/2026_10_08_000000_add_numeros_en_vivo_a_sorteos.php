<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sorteos', function (Blueprint $table) {
            // Números que van saliendo durante el sorteo en vivo, en orden de extracción
            $table->json('numeros_en_vivo')->nullable()->after('numero_6');
            $table->timestamp('en_vivo_desde')->nullable()->after('numeros_en_vivo');
            $table->timestamp('cerrado_en')->nullable()->after('en_vivo_desde');
        });
    }

    public function down(): void
    {
        Schema::table('sorteos', function (Blueprint $table) {
            $table->dropColumn(['numeros_en_vivo', 'en_vivo_desde', 'cerrado_en']);
        });
    }
};
