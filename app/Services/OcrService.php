<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrService
{
    public function extraerMontoDesdeImagen(string $rutaCompleta, float $montoEsperado): array
    {
        try {
            $ocr = new TesseractOCR($rutaCompleta);

            if ($rutaBinario = config('services.tesseract.path')) {
                $ocr->executable($rutaBinario);
            }

            $texto = $ocr->lang('spa')->run();
        } catch (\Throwable $e) {
            return [
                'coincide' => null,
                'monto_detectado' => null,
            ];
        }

        preg_match_all('/(?:s\/?\.?\s?)(\d{1,4}(?:[.,]\d{1,2})?)/i', $texto, $coincidencias);

        $montos = array_map(function ($valor) {
            return (float) str_replace(',', '.', $valor);
        }, $coincidencias[1] ?? []);

        $montoDetectado = null;
        $coincide = false;

        foreach ($montos as $monto) {
            if (abs($monto - $montoEsperado) < 0.01) {
                $montoDetectado = $monto;
                $coincide = true;
                break;
            }
        }

        if (! $coincide && ! empty($montos)) {
            $montoDetectado = $montos[0];
        }

        return [
            'coincide' => $coincide,
            'monto_detectado' => $montoDetectado,
        ];
    }
}