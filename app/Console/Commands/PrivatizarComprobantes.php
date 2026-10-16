<?php

namespace App\Console\Commands;

use App\Models\Compra;
use App\Services\ComprobantePagoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Los comprobantes subidos antes de la mejora de seguridad quedaron en storage/app/public,
 * donde cualquiera con el enlace podía verlos. Este comando los mueve a la carpeta privada.
 */
class PrivatizarComprobantes extends Command
{
    protected $signature = 'comprobantes:privatizar {--simular : Solo muestra qué se movería, sin mover nada}';

    protected $description = 'Mueve las capturas de pago antiguas del disco público al privado';

    public function handle(): int
    {
        $publico = Storage::disk('public');
        $privado = Storage::disk(ComprobantePagoService::DISCO);
        $simular = (bool) $this->option('simular');
        $movidos = 0;

        $rutas = Compra::whereNotNull('comprobante')->pluck('comprobante')->unique();

        foreach ($rutas as $ruta) {
            if (! $publico->exists($ruta)) {
                continue;
            }

            if ($simular) {
                $this->line("Se movería: {$ruta}");
                $movidos++;
                continue;
            }

            if (! $privado->exists($ruta)) {
                $privado->writeStream($ruta, $publico->readStream($ruta));
            }

            // Solo se borra la copia pública cuando la privada quedó completa
            if ($privado->exists($ruta) && $privado->size($ruta) === $publico->size($ruta)) {
                $publico->delete($ruta);
                $movidos++;
            } else {
                $this->error("No se pudo copiar: {$ruta} (se dejó donde estaba)");
            }
        }

        $this->info($simular
            ? "{$movidos} comprobante(s) se moverían a la carpeta privada."
            : "{$movidos} comprobante(s) movidos a la carpeta privada.");

        return self::SUCCESS;
    }
}
