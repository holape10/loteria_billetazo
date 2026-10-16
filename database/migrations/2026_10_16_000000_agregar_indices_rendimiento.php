<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Índices para las consultas más frecuentes: sorteo actual / en vivo, compras por estado,
// búsqueda y control de números de operación repetidos, y notificaciones sin leer.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sorteos', function (Blueprint $table) {
            $table->index(['estado', 'fecha', 'hora']);
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->index(['sorteo_id', 'estado_pago']);
            $table->index('numero_operacion');
        });

        Schema::table('incidencias', function (Blueprint $table) {
            $table->index(['compra_id', 'leido']);
        });
    }

    public function down(): void
    {
        // MySQL usa los índices compuestos para las llaves foráneas (sorteo_id, compra_id):
        // antes de quitarlos se vuelve a crear el índice simple que la llave foránea necesita.
        if (Schema::hasIndex('sorteos', ['estado', 'fecha', 'hora'])) {
            Schema::table('sorteos', fn (Blueprint $table) => $table->dropIndex(['estado', 'fecha', 'hora']));
        }

        Schema::table('compras', function (Blueprint $table) {
            if (! Schema::hasIndex('compras', ['sorteo_id'])) {
                $table->index('sorteo_id', 'compras_sorteo_id_foreign');
            }
        });
        Schema::table('compras', function (Blueprint $table) {
            if (Schema::hasIndex('compras', ['sorteo_id', 'estado_pago'])) {
                $table->dropIndex(['sorteo_id', 'estado_pago']);
            }
            if (Schema::hasIndex('compras', ['numero_operacion'])) {
                $table->dropIndex(['numero_operacion']);
            }
        });

        Schema::table('incidencias', function (Blueprint $table) {
            if (! Schema::hasIndex('incidencias', ['compra_id'])) {
                $table->index('compra_id', 'incidencias_compra_id_foreign');
            }
        });
        Schema::table('incidencias', function (Blueprint $table) {
            if (Schema::hasIndex('incidencias', ['compra_id', 'leido'])) {
                $table->dropIndex(['compra_id', 'leido']);
            }
        });
    }
};
