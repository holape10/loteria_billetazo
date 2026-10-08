<?php

namespace App\Services;

use App\Models\Compra;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ComprobantePagoService
{
    // Disco privado: las capturas de pago no deben ser accesibles por URL pública
    public const DISCO = 'local';

    public const REGLAS = ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];

    /**
     * Guarda la captura y la lee con OCR. Si el jugador no escribió el número de operación, se usa el detectado.
     */
    public function guardar(UploadedFile $archivo, float $montoEsperado, ?string $numeroOperacion = null, ?int $exceptoCompraId = null): array
    {
        $ruta = $archivo->store('comprobantes', self::DISCO);

        $montoDetectado = null;
        $requiereRevision = false;

        if ($montoEsperado > 0) {
            $resultadoOcr = app(OcrService::class)->analizarComprobante(
                Storage::disk(self::DISCO)->path($ruta),
                $montoEsperado
            );

            $montoDetectado = $resultadoOcr['monto_detectado'];
            $requiereRevision = $resultadoOcr['coincide'] === false;
            $numeroOperacion = filled($numeroOperacion) ? $numeroOperacion : $resultadoOcr['numero_operacion'];
        }

        return [
            'comprobante' => $ruta,
            'monto_detectado' => $montoDetectado,
            'numero_operacion' => $numeroOperacion,
            // Un mismo voucher usado en otra compra es la señal más común de fraude
            'requiere_revision' => $requiereRevision || $this->operacionRepetida($numeroOperacion, $exceptoCompraId),
        ];
    }

    public function operacionRepetida(?string $numeroOperacion, ?int $exceptoCompraId = null): bool
    {
        if (blank($numeroOperacion)) {
            return false;
        }

        return Compra::where('numero_operacion', $numeroOperacion)
            ->where('estado_pago', '!=', 'rechazado')
            ->when($exceptoCompraId, fn ($q) => $q->whereKeyNot($exceptoCompraId))
            ->exists();
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
