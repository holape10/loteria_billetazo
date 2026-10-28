<?php

namespace App\Console\Commands;

use App\Models\Sorteo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Deshace un sorteo en vivo que se empezó por error (por ejemplo, en producción en vez de en la PC de pruebas).
 * Solo funciona con sorteos que todavía NO se confirmaron: los ya cerrados, con premios repartidos, no se tocan.
 * Se usa solo desde el servidor (no hay botón en la web) para que nadie pueda repetir un sorteo hasta que
 * salgan los números que le convienen.
 */
class ReiniciarSorteoEnVivo extends Command
{
    protected $signature = 'sorteo:reiniciar-en-vivo {sorteo? : ID del sorteo (si no lo pones, te muestra los que están en vivo)}';

    protected $description = 'Borra los números de un sorteo en vivo que no se confirmó, para empezarlo de nuevo';

    public function handle(): int
    {
        $enVivo = Sorteo::where('estado', 'pendiente')->whereNotNull('numeros_en_vivo')->get();

        if (! $this->argument('sorteo')) {
            if ($enVivo->isEmpty()) {
                $this->info('No hay ningún sorteo en vivo a medias.');

                return self::SUCCESS;
            }

            $this->table(['ID', 'Fecha', 'Hora', 'Números que salieron'], $enVivo->map(fn ($s) => [
                $s->id, $s->fecha->format('d/m/Y'), $s->hora, implode(' - ', $s->numeros_en_vivo),
            ]));
            $this->line('Para reiniciarlo: php artisan sorteo:reiniciar-en-vivo ID');

            return self::SUCCESS;
        }

        $sorteo = Sorteo::find($this->argument('sorteo'));

        if (! $sorteo) {
            $this->error('No existe un sorteo con ese ID.');

            return self::FAILURE;
        }

        if ($sorteo->estado !== 'pendiente') {
            $this->error('Ese sorteo ya fue confirmado y cerrado (con premios repartidos). No se puede reiniciar.');

            return self::FAILURE;
        }

        if (empty($sorteo->numeros_en_vivo)) {
            $this->info('Ese sorteo no tiene números en vivo: no hay nada que reiniciar.');

            return self::SUCCESS;
        }

        $this->warn("Sorteo {$sorteo->id} del {$sorteo->fecha->format('d/m/Y')} {$sorteo->hora}");
        $this->warn('Números que salieron: ' . implode(' - ', $sorteo->numeros_en_vivo));

        if (! $this->confirm('¿Borrar estos números y dejar el sorteo como si no hubiera empezado?')) {
            $this->line('Cancelado. No se cambió nada.');

            return self::SUCCESS;
        }

        $sorteo->update(['numeros_en_vivo' => null, 'en_vivo_desde' => null]);
        Cache::forget("sorteo:{$sorteo->id}:mezclando");
        Cache::forget("sorteo:{$sorteo->id}:publicidad");

        $this->info("Listo: el sorteo {$sorteo->id} quedó pendiente otra vez.");
        $this->info('Ventas abiertas: ' . ($sorteo->fresh()->ventasAbiertas() ? 'SÍ' : 'NO (ya pasó la fecha y hora del sorteo)'));

        return self::SUCCESS;
    }
}
