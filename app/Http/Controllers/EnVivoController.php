<?php

namespace App\Http\Controllers;

use App\Models\Sorteo;
use App\Services\SorteoEnVivoService;
use Illuminate\Support\Facades\Auth;

class EnVivoController extends Controller
{
    public function index()
    {
        return view('jugador.en-vivo');
    }

    public function estado(SorteoEnVivoService $enVivo)
    {
        [$sorteo, $estado] = $this->sorteoActual();

        if (! $sorteo) {
            return response()->json(['estado' => 'sin_sorteo']);
        }

        $publico = $enVivo->estadoPublico($sorteo);
        $clienteId = Auth::user()->cliente_id;

        // El ranking cacheado es igual para todos; aquí se marca cuáles son del jugador y se ocultan los ids
        $publico['ranking'] = array_map(function ($fila) use ($clienteId) {
            $fila['es_mio'] = $clienteId && (int) $fila['cliente_id'] === (int) $clienteId;
            unset($fila['cliente_id'], $fila['boleto_id']);

            return $fila;
        }, $publico['ranking']);

        return response()->json(array_merge($publico, [
            'estado' => $estado,
            'mis_jugadas' => $this->misJugadas($sorteo, $publico['extraidos'], $enVivo),
        ]));
    }

    /**
     * Prioridad: sorteo jugándose ahora → recién terminado (3 h) → próximo programado → último jugado.
     */
    private function sorteoActual(): array
    {
        $enVivo = Sorteo::where('estado', 'pendiente')->whereNotNull('numeros_en_vivo')->orderBy('fecha')->first();
        if ($enVivo && $enVivo->estaEnVivo()) {
            return [$enVivo, 'en_vivo'];
        }

        $recienCerrado = Sorteo::where('estado', 'cerrado')->where('cerrado_en', '>=', now()->subHours(3))
            ->latest('cerrado_en')->first();
        if ($recienCerrado) {
            return [$recienCerrado, 'finalizado'];
        }

        $proximo = Sorteo::where('estado', 'pendiente')->orderBy('fecha')->orderBy('hora')->first();
        if ($proximo) {
            return [$proximo, 'esperando'];
        }

        $ultimo = Sorteo::where('estado', 'cerrado')->whereNotNull('numero_1')
            ->orderByDesc('fecha')->orderByDesc('hora')->first();

        return [$ultimo, $ultimo ? 'finalizado' : null];
    }

    private function misJugadas(Sorteo $sorteo, array $extraidos, SorteoEnVivoService $enVivo): array
    {
        $cliente = Auth::user()->cliente;

        if (! $cliente) {
            return [];
        }

        return $sorteo->boletos()
            ->where('cliente_id', $cliente->id)
            ->with('compra:id,estado_pago')
            ->get()
            ->map(fn ($boleto) => [
                'numeros' => $enVivo->numerosDe($boleto),
                'aciertos' => count(array_intersect($enVivo->numerosDe($boleto), $extraidos)),
                'participa' => $boleto->compra?->estado_pago === 'pagado',
                'premio' => $boleto->premio_ganado ? number_format((float) $boleto->premio_ganado, 2) : null,
                'jugada_gratis' => (bool) $boleto->jugada_gratis_ganada,
            ])
            ->sortByDesc('aciertos')
            ->values()
            ->all();
    }
}
