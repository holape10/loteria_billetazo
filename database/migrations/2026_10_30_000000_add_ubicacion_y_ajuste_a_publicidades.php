<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publicidades', function (Blueprint $table) {
            // sorteo = pantalla del sorteo en vivo · inicio = ventana en la página principal · ambos
            $table->string('ubicacion', 10)->default('sorteo')->after('titulo');
            $table->string('enlace', 255)->nullable()->after('texto');
            // contain = imagen completa · cover = llena el recuadro (puede recortar los bordes)
            $table->string('ajuste_imagen', 10)->default('contain')->after('imagen');
            $table->string('posicion_imagen', 10)->default('center')->after('ajuste_imagen');
            $table->index(['ubicacion', 'activo', 'orden']);
        });

        DB::table('configuraciones')->insert([
            ['clave' => 'publicidad_en_vivo_modo', 'valor' => 'siempre', 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'publicidad_inicio_frecuencia', 'valor' => 'sesion', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('publicidades', function (Blueprint $table) {
            $table->dropIndex(['ubicacion', 'activo', 'orden']);
            $table->dropColumn(['ubicacion', 'enlace', 'ajuste_imagen', 'posicion_imagen']);
        });

        DB::table('configuraciones')->whereIn('clave', ['publicidad_en_vivo_modo', 'publicidad_inicio_frecuencia'])->delete();
    }
};
