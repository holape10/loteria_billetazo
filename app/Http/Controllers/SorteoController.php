<?php

namespace App\Http\Controllers;

use App\Models\Sorteo;
use Illuminate\Http\Request;

class SorteoController extends Controller
{
    public function index()
    {
        $sorteos = Sorteo::latest('fecha')->paginate(10);

        return view('empresa.sorteos.index', compact('sorteos'));
    }

    public function create()
    {
        return view('empresa.sorteos.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
        ]);

        Sorteo::create($datos);

        return redirect()->route('sorteos.index')->with('exito', 'Sorteo programado correctamente.');
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