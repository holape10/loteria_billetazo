<?php

namespace App\Http\Controllers;

use App\Models\Sorteo;
use Illuminate\Http\Request;

class SorteoController extends Controller
{
    public function index(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $numero = $request->input('numero');

        $sorteos = Sorteo::query()
            ->when($fechaDesde, fn ($q) => $q->whereDate('fecha', '>=', $fechaDesde))
            ->when($fechaHasta, fn ($q) => $q->whereDate('fecha', '<=', $fechaHasta))
            ->when($numero, function ($q) use ($numero) {
                $q->where(function ($qq) use ($numero) {
                    foreach (range(1, 6) as $i) {
                        $qq->orWhere("numero_{$i}", $numero);
                    }
                });
            })
            ->latest('fecha')
            ->paginate(10)
            ->withQueryString();

        return view('empresa.sorteos.index', compact('sorteos', 'fechaDesde', 'fechaHasta', 'numero'));
    }

    public function create()
    {
        $premioMayorProximo = $this->calcularProximoPremioMayor();

        return view('empresa.sorteos.create', compact('premioMayorProximo'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
        ]);

        $premioMayor = $this->calcularProximoPremioMayor();

        Sorteo::create(array_merge($datos, ['premio_mayor' => $premioMayor]));

        return redirect()->route('sorteos.index')
            ->with('exito', 'Sorteo programado correctamente. Premio mayor: S/ ' . number_format($premioMayor, 2));
    }

    public function realizar(Sorteo $sorteo)
    {
        if ($sorteo->estado !== 'pendiente') {
            return back()->with('error', 'Este sorteo ya fue jugado.');
        }

        $numerosGanadores = collect(range(1, 60))->shuffle()->take(6)->values()->all();

        $this->procesarResultados($sorteo, $numerosGanadores);

        return redirect()->route('sorteos.index')
            ->with('exito', 'Sorteo realizado al azar. Números ganadores: ' . collect($numerosGanadores)->sort()->join(', '));
    }

    public function formularioManual(Sorteo $sorteo)
    {
        if ($sorteo->estado !== 'pendiente') {
            return back()->with('error', 'Este sorteo ya fue jugado.');
        }

        return view('empresa.sorteos.realizar-manual', compact('sorteo'));
    }

    public function realizarManual(Request $request, Sorteo $sorteo)
    {
        if ($sorteo->estado !== 'pendiente') {
            return back()->with('error', 'Este sorteo ya fue jugado.');
        }

        $datos = $request->validate([
            'numeros' => 'required|array|size:6',
            'numeros.*' => 'integer|min:1|max:60|distinct',
        ]);

        $this->procesarResultados($sorteo, $datos['numeros']);

        return redirect()->route('sorteos.index')
            ->with('exito', 'Sorteo realizado manualmente. Números ganadores: ' . collect($datos['numeros'])->sort()->join(', '));
    }

    public function individual(Sorteo $sorteo)
    {
        if ($sorteo->estado !== 'pendiente') {
            return back()->with('error', 'Este sorteo ya fue jugado.');
        }

        return view('empresa.sorteos.individual', compact('sorteo'));
    }

    private function calcularProximoPremioMayor(): float
    {
        $ultimoSorteo = Sorteo::where('estado', 'cerrado')->latest('fecha')->latest('hora')->first();

        if (! $ultimoSorteo) {
            return 1000.00;
        }

        $huboGanadorMayor = $ultimoSorteo->boletos()->where('aciertos', 6)->exists();

        return $huboGanadorMayor ? 1000.00 : (float) $ultimoSorteo->premio_mayor + 200;
    }

    private function procesarResultados(Sorteo $sorteo, array $numerosGanadores): void
    {
        sort($numerosGanadores);

        $sorteo->update([
            'numero_1' => $numerosGanadores[0],
            'numero_2' => $numerosGanadores[1],
            'numero_3' => $numerosGanadores[2],
            'numero_4' => $numerosGanadores[3],
            'numero_5' => $numerosGanadores[4],
            'numero_6' => $numerosGanadores[5],
        ]);

        $boletos = $sorteo->boletos()->whereHas('compra', function ($q) {
            $q->where('estado_pago', 'pagado');
        })->get();

        foreach ($boletos as $boleto) {
            $numerosBoleto = [
                $boleto->numero_1, $boleto->numero_2, $boleto->numero_3,
                $boleto->numero_4, $boleto->numero_5, $boleto->numero_6,
            ];

            $boleto->update([
                'aciertos' => count(array_intersect($numerosBoleto, $numerosGanadores)),
            ]);
        }

        $ganadoresMayor = $boletos->fresh()->where('aciertos', 6);

        if ($ganadoresMayor->count() > 0) {
            $premioCadaUno = round($sorteo->premio_mayor / $ganadoresMayor->count(), 2);
            foreach ($ganadoresMayor as $boleto) {
                $boleto->update(['premio_ganado' => $premioCadaUno]);
            }
        } else {
            $ganadoresCinco = $boletos->fresh()->where('aciertos', 5);
            if ($ganadoresCinco->count() > 0) {
                $premioCadaUno = round($sorteo->premio_cinco_aciertos / $ganadoresCinco->count(), 2);
                foreach ($ganadoresCinco as $boleto) {
                    $boleto->update(['premio_ganado' => $premioCadaUno]);
                }
            }

            $ganadoresCuatro = $boletos->fresh()->where('aciertos', 4);
            if ($ganadoresCuatro->count() > 0) {
                $premioCadaUno = round($sorteo->premio_cuatro_aciertos / $ganadoresCuatro->count(), 2);
                foreach ($ganadoresCuatro as $boleto) {
                    $boleto->update(['premio_ganado' => $premioCadaUno]);
                }
            }
        }

        $sorteo->update(['estado' => 'cerrado']);
    }
}