<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Sorteo;

class ReporteController extends Controller
{
    public function clientesFrecuentes()
    {
        $clientes = Cliente::withCount('boletos')
            ->withSum('boletos', 'monto')
            ->withCount(['boletos as veces_ganadas' => function ($q) {
                $q->whereNotNull('premio_ganado');
            }])
            ->withMax('boletos', 'created_at')
            ->orderByDesc('boletos_count')
            ->paginate(15);

        return view('empresa.reportes.clientes-frecuentes', compact('clientes'));
    }

    public function numerosFrecuentes()
    {
        $sorteosJugados = Sorteo::where('estado', 'cerrado')->whereNotNull('numero_1')->get();

        $conteo = array_fill(1, 60, 0);

        foreach ($sorteosJugados as $sorteo) {
            foreach ([
                $sorteo->numero_1, $sorteo->numero_2, $sorteo->numero_3,
                $sorteo->numero_4, $sorteo->numero_5, $sorteo->numero_6,
            ] as $numero) {
                if ($numero) {
                    $conteo[$numero]++;
                }
            }
        }

        arsort($conteo);

        return view('empresa.reportes.numeros-frecuentes', [
            'conteo' => $conteo,
            'totalSorteos' => $sorteosJugados->count(),
        ]);
    }
}