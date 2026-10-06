<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ComprobantePagoService
{
    // Disco privado: las capturas de pago no deben ser accesibles por URL pública
    public const DISCO = 'local';

    public const REGLAS = ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];

    public function guardar(UploadedFile $archivo, float $montoEsperado): array
    {
        $ruta = $archivo->store('comprobantes', self::DISCO);

        $montoDetectado = null;
        $requiereRevision = false;

        if ($montoEsperado > 0) {
            $resultadoOcr = app(OcrService::class)->extraerMontoDesdeImagen(
                Storage::disk(self::DISCO)->path($ruta),
                $montoEsperado
            );

            $montoDetectado = $resultadoOcr['monto_detectado'];
            $requiereRevision = $resultadoOcr['coincide'] === false;
        }

        return [
            'comprobante' => $ruta,
            'monto_detectado' => $montoDetectado,
            'requiere_revision' => $requiereRevision,
        ];
    }

    public function eliminar(?string $ruta): void
    {
        if (! $ruta) {
            return;
        }

        Storage::disk(self::DISCO)->delete($ruta);
        Storage::disk('public')->delete($ruta);
    }

    // Los comprobantes antiguos se guardaron en el disco público; se buscan en ambos
    public function discoDe(string $ruta): ?string
    {
        foreach ([self::DISCO, 'public'] as $disco) {
            if (Storage::disk($disco)->exists($ruta)) {
                return $disco;
            }
        }

        return null;
    }
}
