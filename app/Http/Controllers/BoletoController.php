<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\Compra;
use App\Models\Sorteo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoletoController extends Controller
{
    public function create()
    {
        $sorteo = Sorteo::where('estado', 'pendiente')->orderBy('fecha')->orderBy('hora')->first();

        if (! $sorteo) {
            return back()->with('error', 'No hay ningún sorteo programado por ahora.');
        }

        return view('jugador.boletos.create', compact('sorteo'));
    }

    public function store(Request $request, Sorteo $sorteo)
    {
        $datos = $request->validate([
            'jugadas' => 'required|array|min:1',
            'jugadas.*' => 'array|size:6',
            'jugadas.*.*' => 'integer|min:1|max:60',
            'metodo_pago' => 'required|in:yape,plin',
            'numero_operacion' => 'nullable|string|max:50',
            'comprobante' => 'nullable|image|max:4096',
        ]);

        foreach ($datos['jugadas'] as $jugada) {
            if (count(array_unique($jugada)) !== 6) {
                return back()->withErrors(['jugadas' => 'Cada jugada debe tener 6 números distintos.'])->withInput();
            }
        }

        $cantidadJugadas = count($datos['jugadas']);
        $montoTotal = $cantidadJugadas * 1.00;

        //$rutaComprobante = $request->file('comprobante')->store('comprobantes', 'public');

        $rutaComprobante = $request->hasFile('comprobante')
        ? $request->file('comprobante')->store('comprobantes', 'public')
        : null;

        $compra = Compra::create([
            'cliente_id' => Auth::user()->cliente_id,
            'sorteo_id' => $sorteo->id,
            'cantidad_jugadas' => $cantidadJugadas,
            'monto_total' => $montoTotal,
            'metodo_pago' => $datos['metodo_pago'],
            //'numero_operacion' => $datos['numero_operacion'],
            'numero_operacion' => $datos['numero_operacion'] ?? null,
            'comprobante' => $rutaComprobante,
            'estado_pago' => 'pendiente',
        ]);

        foreach ($datos['jugadas'] as $jugada) {
            sort($jugada);

            Boleto::create([
                'cliente_id' => Auth::user()->cliente_id,
                'sorteo_id' => $sorteo->id,
                'compra_id' => $compra->id,
                'numero_1' => $jugada[0],
                'numero_2' => $jugada[1],
                'numero_3' => $jugada[2],
                'numero_4' => $jugada[3],
                'numero_5' => $jugada[4],
                'numero_6' => $jugada[5],
                'monto' => 1.00,
            ]);
        }

        return redirect()->route('dashboard')->with('exito', "Compra registrada: {$cantidadJugadas} jugada(s) por S/ {$montoTotal}, en espera de validación de pago.");
    }
}