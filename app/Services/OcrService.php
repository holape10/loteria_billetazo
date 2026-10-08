<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class OcrService
{
    /**
     * Lee la captura una sola vez y devuelve el monto y el número de operación detectados.
     *
     * @return array{coincide: ?bool, monto_detectado: ?float, numero_operacion: ?string}
     */
    public function analizarComprobante(string $rutaImagen, float $montoEsperado): array
    {
        $vacio = ['coincide' => null, 'monto_detectado' => null, 'numero_operacion' => null];

        try {
            $tsv = $this->ejecutarTesseractTsv($rutaImagen);
        } catch (\Throwable $e) {
            return $vacio;
        }

        return array_merge(
            $this->analizarMonto($tsv, $montoEsperado),
            ['numero_operacion' => $this->extraerNumeroOperacion($this->lineasDeTexto($tsv))]
        );
    }

    public function extraerMontoDesdeImagen(string $rutaImagen, float $montoEsperado): array
    {
        $resultado = $this->analizarComprobante($rutaImagen, $montoEsperado);

        return ['coincide' => $resultado['coincide'], 'monto_detectado' => $resultado['monto_detectado']];
    }

    private function analizarMonto(string $tsv, float $montoEsperado): array
    {
        $candidatos = $this->extraerCandidatos($tsv);

        if (empty($candidatos)) {
            return ['coincide' => null, 'monto_detectado' => null];
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

        return ['coincide' => $coincide, 'monto_detectado' => $montoDetectado];
    }

    /**
     * Busca la etiqueta del número de operación (Yape: "Nro. de operación", Plin: "Número de operación",
     * otros bancos: "Código de operación", "N° de operación") y toma el código que la acompaña.
     */
    public function extraerNumeroOperacion(array $lineas): ?string
    {
        $etiqueta = '/(?:n[uú]m(?:ero)?|nro|n[°º]|c[oó]d(?:igo)?)\.?\s*(?:de\s*)?operaci[oó]n\s*[:#.]?\s*(.*)$/iu';

        foreach ($lineas as $indice => $linea) {
            if (! preg_match($etiqueta, $linea, $coincidencia)) {
                continue;
            }

            // El código puede estar en la misma línea o en la siguiente
            foreach ([$coincidencia[1], $lineas[$indice + 1] ?? ''] as $texto) {
                foreach (preg_split('/\s+/', trim($texto)) as $token) {
                    $codigo = $this->normalizarCodigo($token);

                    if ($codigo !== null) {
                        return $codigo;
                    }
                }
            }
        }

        return null;
    }

    private function normalizarCodigo(string $token): ?string
    {
        $token = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $token));

        if (strlen($token) < 5 || strlen($token) > 20 || preg_match_all('/\d/', $token) < 3) {
            return null;
        }

        // Los códigos de Plin son hexadecimales y los de Yape numéricos: corregimos letras que el OCR confunde con dígitos
        if (preg_match('/[G-Z]/', $token)) {
            $corregido = strtr($token, ['O' => '0', 'Q' => '0', 'I' => '1', 'L' => '1', 'S' => '5', 'Z' => '2', 'G' => '6', 'T' => '7']);

            if (preg_match('/^[0-9A-F]+$/', $corregido)) {
                return $corregido;
            }
        }

        return $token;
    }

    // Reconstruye las líneas de texto a partir de las palabras del TSV de Tesseract
    private function lineasDeTexto(string $tsv): array
    {
        $lineas = [];

        foreach (array_slice(explode("\n", trim($tsv)), 1) as $fila) {
            $campos = explode("\t", $fila);

            if (count($campos) < 12 || trim($campos[11]) === '') {
                continue;
            }

            $clave = "{$campos[2]}-{$campos[3]}-{$campos[4]}";
            $lineas[$clave] = isset($lineas[$clave]) ? $lineas[$clave] . ' ' . trim($campos[11]) : trim($campos[11]);
        }

        return array_values($lineas);
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

            // El año de la fecha ("30 septiembre 2026") suele ser grande en la captura y no es un monto
            if (preg_match('/^20\d{2}$/', $numero) && abs((int) $numero - (int) date('Y')) <= 1) {
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