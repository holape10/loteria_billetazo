<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publicidades', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 150);
            $table->text('texto')->nullable();
            $table->string('imagen')->nullable();
            $table->string('audio')->nullable();
            // Lo que lee la voz; si está vacío se lee el título y el texto
            $table->text('texto_voz')->nullable();
            $table->boolean('con_voz')->default(false);
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->unsignedTinyInteger('duracion_segundos')->default(10);
            $table->timestamps();

            $table->index(['activo', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publicidades');
    }
};
