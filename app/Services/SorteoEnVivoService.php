<?php

namespace App\Services;

use App\Models\Boleto;
use App\Models\Sorteo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SorteoEnVivoService
{
    public const TOTAL_NUMEROS = 6;

    // El servidor elige cada bolilla: así el resultado no depende del navegador y sobrevive a una recarga
    public function extraer(Sorteo $sorteo): ?array
    {
        return DB::transaction(function () use ($sorteo) {
            $sorteo = Sorteo::whereKey($sorteo->id)->lockForUpdate()->first();

            $extraidos = $sorteo->numeros_en_vivo ?? [];

            if ($sorteo->estado !== 'pendiente' || count($extraidos) >= self::TOTAL_NUMEROS) {
                return null;
            }

            $disponibles = array_values(array_diff(range(1, 60), $extraidos));
            $extraidos[] = $disponibles[random_int(0, count($disponibles) - 1)];

            $sorteo->update([
                'numeros_en_vivo' => $extraidos,
                'en_vivo_desde' => $sorteo->en_vivo_desde ?? now(),
            ]);

            return $extraidos;
        });
    }

    /**
     * Boletos pagados del sorteo con la cantidad de números que ya acertaron.
     *
     * @return Collection<int, array{boleto: Boleto, aciertos: int}>
     */
    public function boletosConAciertos(Sorteo $sorteo, array $extraidos): Collection
    {
        return $sorteo->boletos()
            ->whereHas('compra', fn ($q) => $q->where('estado_pago', 'pagado'))
            ->with('cliente:id,nombre')
            ->get()
            ->map(fn (Boleto $boleto) => [
                'boleto' => $boleto,
                'aciertos' => count(array_intersect($this->numerosDe($boleto), $extraidos)),
            ]);
    }

    // Jugadores que acertaron todos los números extraídos hasta ahora (siguen en carrera por el pozo)
    public function enCarrera(Sorteo $sorteo, array $extraidos): array
    {
        if (empty($extraidos)) {
            return [];
        }

        return $this->boletosConAciertos($sorteo, $extraidos)
            ->filter(fn ($fila) => $fila['aciertos'] === count($extraidos))
            ->map(fn ($fila) => [
                'cliente' => $fila['boleto']->cliente->nombre,
                'coincidencias' => $fila['aciertos'],
            ])
            ->values()
            ->all();
    }

    /**
     * Estado público del sorteo para la vista de los jugadores (sin datos personales completos).
     */
    public function estadoPublico(Sorteo $sorteo): array
    {
        $extraidos = $sorteo->numerosExtraidos();

        // Se cachea unos segundos: muchos jugadores consultan a la vez y el resultado solo cambia con cada bolilla
        $ranking = Cache::remember(
            "en-vivo:{$sorteo->id}:" . implode('-', $extraidos) . ':' . $sorteo->estado,
            now()->addSeconds(5),
            fn () => $this->calcularRanking($sorteo, $extraidos)
        );

        return [
            'sorteo' => [
                'id' => $sorteo->id,
                'fecha' => $sorteo->fecha->format('d/m/Y'),
                'hora' => substr((string) $sorteo->hora, 0, 5),
                'inicio' => $sorteo->fechaHoraLima()->toIso8601String(),
                'premio_mayor' => number_format((float) $sorteo->premio_mayor, 2),
            ],
            'extraidos' => $extraidos,
            'ranking' => $ranking['ranking'],
            'total_en_carrera' => $ranking['total_en_carrera'],
            'total_jugadas' => $ranking['total_jugadas'],
        ];
    }

    private function calcularRanking(Sorteo $sorteo, array $extraidos): array
    {
        $filas = $this->boletosConAciertos($sorteo, $extraidos);
        $cerrado = $sorteo->estado === 'cerrado';

        $ranking = $filas
            ->filter(fn ($fila) => $fila['aciertos'] > 0)
            ->sortByDesc('aciertos')
            ->take(10)
            ->map(fn ($fila) => [
                'boleto_id' => $fila['boleto']->id,
                'cliente_id' => $fila['boleto']->cliente_id,
                'nombre' => $this->abreviarNombre($fila['boleto']->cliente->nombre ?? 'Jugador'),
                'aciertos' => $fila['aciertos'],
                'premio' => $cerrado && $fila['boleto']->premio_ganado ? number_format((float) $fila['boleto']->premio_ganado, 2) : null,
                'jugada_gratis' => $cerrado && $fila['boleto']->jugada_gratis_ganada,
            ])
            ->values()
            ->all();

        return [
            'ranking' => $ranking,
            'total_en_carrera' => empty($extraidos) ? $filas->count() : $filas->where('aciertos', count($extraidos))->count(),
            'total_jugadas' => $filas->count(),
        ];
    }

    // "CHINCHAY PINTADO, MIGUEL ANGEL" → "Miguel C."  ·  "Juan Perez Lopez" → "Juan P."
    public function abreviarNombre(string $nombre): string
    {
        if (str_contains($nombre, ',')) {
            [$apellidos, $nombres] = array_map('trim', explode(',', $nombre, 2));
        } else {
            $partes = preg_split('/\s+/', trim($nombre));
            $nombres = array_shift($partes) ?? '';
            $apellidos = implode(' ', $partes);
        }

        $primerNombre = Str::title(Str::lower(strtok($nombres, ' ') ?: $nombres));
        $inicial = $apellidos !== '' ? Str::upper(mb_substr($apellidos, 0, 1)) . '.' : '';

        return trim("{$primerNombre} {$inicial}");
    }

    public function numerosDe(Boleto $boleto): array
    {
        return [
            $boleto->numero_1, $boleto->numero_2, $boleto->numero_3,
            $boleto->numero_4, $boleto->numero_5, $boleto->numero_6,
        ];
    }
}
