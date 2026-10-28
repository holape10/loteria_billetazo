<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajustes que el administrador puede cambiar sin tocar el código
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique();
            $table->text('valor')->nullable();
            $table->timestamps();
        });

        DB::table('configuraciones')->insert([
            'clave' => 'jugadas_gratis_registro',
            'valor' => '2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Cuándo recibió su regalo de bienvenida: así cada DNI lo recibe una sola vez
        Schema::table('clientes', function (Blueprint $table) {
            $table->timestamp('bono_bienvenida_at')->nullable()->after('jugadas_gratis');
        });

        // Quienes ya tienen cuenta se registraron antes de este regalo: se marcan para que,
        // si borran su cuenta y se registran otra vez, no lo reciban
        DB::table('clientes')
            ->whereIn('id', DB::table('users')->whereNotNull('cliente_id')->select('cliente_id'))
            ->update(['bono_bienvenida_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('bono_bienvenida_at');
        });

        Schema::dropIfExists('configuraciones');
    }
};
