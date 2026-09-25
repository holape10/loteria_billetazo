<?php

namespace App\Http\Controllers;

use App\Models\Sorteo;

class GanadorController extends Controller
{
    public function index()
    {
        $ultimoSorteoJugado = Sorteo::where('estado', 'cerrado')
            ->whereNotNull('numero_1')
            ->orderByDesc('fecha')->orderByDesc('hora')
            ->first();

        $ganadores = collect();

        if ($ultimoSorteoJugado) {
            $ganadores = $ultimoSorteoJugado->boletos()
                ->where(function ($q) {
                    $q->whereNotNull('premio_ganado')->orWhere('jugada_gratis_ganada', true);
                })
                ->with('cliente')
                ->orderBy('aciertos')
                ->get();
        }

        return view('empresa.ganadores.index', compact('ultimoSorteoJugado', 'ganadores'));
    }
}