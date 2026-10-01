<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class OcrService
{
    public function extraerMontoDesdeImagen(string $rutaImagen, float $montoEsperado): array
    {
        try {
            $tsv = $this->ejecutarTesseractTsv($rutaImagen);
        } catch (\Throwable $e) {
            return [
                'coincide' => null,
                'monto_detectado' => null,
            ];
        }

        $candidatos = $this->extraerCandidatos($tsv);

        if (empty($candidatos)) {
            return [
                'coincide' => null,
                'monto_detectado' => null,
            ];
        }

        // Ordenamos de mayor a menor tamaño de letra (el monto principal suele ser el más grande)
        usort($candidatos, fn ($a, $b) => $b['alto'] <=> $a['alto']);

        $montoDetectado = $candidatos[0]['valor'];
        $coincide = abs($montoDetectado - $montoEsperado) < 0.01;

        // Por si el más grande no coincidió, revisamos si el monto esperado aparece en algún otro candidato
        if (! $coincide) {
            foreach ($candidatos as $candidato) {
                if (abs($candidato['valor'] - $montoEsperado) < 0.01) {
                    $coincide = true;
                    break;
                }
            }
        }

        return [
            'coincide' => $coincide,
            'monto_detectado' => $montoDetectado,
        ];
    }

    private function ejecutarTesseractTsv(string $rutaImagen): string
    {
        $binario = config('services.tesseract.path') ?: 'tesseract';

        $proceso = new Process([$binario, $rutaImagen, 'stdout', '-l', 'spa', 'tsv']);
        $proceso->run();

        if (! $proceso->isSuccessful()) {
            throw new \RuntimeException($proceso->getErrorOutput());
        }

        return $proceso->getOutput();
    }

    private function extraerCandidatos(string $tsv): array
    {
        $lineas = explode("\n", trim($tsv));
        array_shift($lineas); // la primera línea es el encabezado de columnas

        $candidatos = [];

        foreach ($lineas as $linea) {
            $campos = explode("\t", $linea);

            if (count($campos) < 12) {
                continue;
            }

            $alto = (int) $campos[9];
            $texto = trim($campos[11]);

            if ($texto === '' || ! preg_match('/\d/', $texto)) {
                continue;
            }

            $numero = str_replace(',', '.', preg_replace('/[^\d.,]/', '', $texto));
            $digitos = preg_replace('/[^\d]/', '', $numero);

            // Si tiene más de 6 dígitos, probablemente es un teléfono o número de operación, no un monto
            if ($numero === '' || ! is_numeric($numero) || strlen($digitos) > 6) {
                continue;
            }

            $candidatos[] = [
                'valor' => (float) $numero,
                'alto' => $alto,
            ];
        }

        return $candidatos;
    }
}