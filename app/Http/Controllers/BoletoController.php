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

        $creditosDisponibles = Auth::user()->cliente->jugadas_gratis ?? 0;

        return view('jugador.boletos.create', compact('sorteo', 'creditosDisponibles'));
    }

    public function store(Request $request, Sorteo $sorteo)
    {
        $datos = $request->validate([
            'jugadas' => 'required|array|min:1',
            'jugadas.*' => 'array|size:6',
            'jugadas.*.*' => 'integer|min:1|max:60',
            'gratis' => 'nullable|array',
            'gratis.*' => 'nullable|in:1',
            'metodo_pago' => 'nullable|in:yape,plin',
            'numero_operacion' => 'nullable|string|max:50',
            'comprobante' => 'nullable|image|max:4096',
        ]);

        foreach ($datos['jugadas'] as $jugada) {
            if (count(array_unique($jugada)) !== 6) {
                return back()->withErrors(['jugadas' => 'Cada jugada debe tener 6 números distintos.'])->withInput();
            }
        }

        $cliente = Auth::user()->cliente;
        $gratisMarcadas = $datos['gratis'] ?? [];
        $cantidadGratisSolicitadas = count(array_filter($gratisMarcadas));

        if ($cantidadGratisSolicitadas > $cliente->jugadas_gratis) {
            return back()->withErrors(['gratis' => 'No tienes suficientes jugadas gratis disponibles.'])->withInput();
        }

        $cantidadJugadas = count($datos['jugadas']);
        $cantidadPagadas = $cantidadJugadas - $cantidadGratisSolicitadas;
        $montoTotal = $cantidadPagadas * 3.00;

        if ($montoTotal > 0 && empty($datos['metodo_pago'])) {
            return back()->withErrors(['metodo_pago' => 'Selecciona un método de pago.'])->withInput();
        }

        $rutaComprobante = $request->hasFile('comprobante')
            ? $request->file('comprobante')->store('comprobantes', 'public')
            : null;

        $compra = Compra::create([
            'cliente_id' => $cliente->id,
            'sorteo_id' => $sorteo->id,
            'cantidad_jugadas' => $cantidadJugadas,
            'monto_total' => $montoTotal,
            'metodo_pago' => $datos['metodo_pago'] ?? null,
            'numero_operacion' => $datos['numero_operacion'] ?? null,
            'comprobante' => $rutaComprobante,
            'estado_pago' => $montoTotal == 0 ? 'pagado' : 'pendiente',
        ]);

        foreach ($datos['jugadas'] as $indice => $jugada) {
            sort($jugada);
            $esGratis = ! empty($gratisMarcadas[$indice]);

            Boleto::create([
                'cliente_id' => $cliente->id,
                'sorteo_id' => $sorteo->id,
                'compra_id' => $compra->id,
                'numero_1' => $jugada[0],
                'numero_2' => $jugada[1],
                'numero_3' => $jugada[2],
                'numero_4' => $jugada[3],
                'numero_5' => $jugada[4],
                'numero_6' => $jugada[5],
                'monto' => $esGratis ? 0.00 : 3.00,
            ]);
        }

        if ($cantidadGratisSolicitadas > 0) {
            $cliente->decrement('jugadas_gratis', $cantidadGratisSolicitadas);
        }

        $mensaje = $montoTotal == 0
            ? "¡Listo! {$cantidadJugadas} jugada(s) gratis registrada(s) para el sorteo."
            : "Compra registrada: {$cantidadJugadas} jugada(s) por S/ {$montoTotal}, en espera de validación de pago.";

        //return redirect()->route('dashboard')->with('exito', $mensaje);
          return redirect()->route('compras.comprobante', $compra)->with('exito', $mensaje);
    }
}