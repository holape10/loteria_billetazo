<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Incidencia;
use Illuminate\Http\Request;

class IncidenciaController extends Controller
{
    public function store(Request $request, Compra $compra)
    {
        $datos = $request->validate([
            'fecha_deposito' => 'nullable|date',
            'observaciones' => 'required|string|max:1000',
        ]);

        $compra->incidencias()->create($datos);

        return back()->with('exito', 'Incidencia registrada correctamente.');
    }

    public function index()
    {
        $cliente = auth()->user()->cliente;

        abort_unless($cliente, 403, 'Esta sección es solo para jugadores.');

        $incidencias = Incidencia::whereHas('compra', function ($q) use ($cliente) {
            $q->where('cliente_id', $cliente->id);
        })->with('compra.sorteo')->latest()->get();

        Incidencia::whereHas('compra', function ($q) use ($cliente) {
            $q->where('cliente_id', $cliente->id);
        })->where('leido', false)->update(['leido' => true]);

        return view('jugador.incidencias.index', compact('incidencias'));
    }
}