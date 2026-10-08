<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Sorteo;
use App\Services\ComprobantePagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BoletoController extends Controller
{
    public function create()
    {
        $cliente = Auth::user()->cliente;

        if (! $cliente) {
            abort(403, 'Solo los jugadores pueden comprar jugadas.');
        }

        $sorteo = Sorteo::where('estado', 'pendiente')->orderBy('fecha')->orderBy('hora')->first();

        if (! $sorteo) {
            return back()->with('error', 'No hay ningún sorteo programado por ahora.');
        }

        if (! $sorteo->ventasAbiertas()) {
            return back()->with('error', 'Las ventas para el sorteo de esta semana ya están cerradas.');
        }

        $creditosDisponibles = $cliente->jugadas_gratis ?? 0;

        return view('jugador.boletos.create', compact('sorteo', 'creditosDisponibles'));
    }

    public function store(Request $request, Sorteo $sorteo, ComprobantePagoService $comprobantes)
    {
        $cliente = Auth::user()->cliente;

        if (! $cliente || ! $cliente->estado) {
            abort(403, 'Tu cuenta no puede realizar compras.');
        }

        if (! $sorteo->ventasAbiertas()) {
            return redirect()->route('dashboard')->with('error', 'Las ventas para este sorteo ya están cerradas.');
        }

        $datos = $request->validate([
            'jugadas' => 'required|array|min:1|max:50',
            'jugadas.*' => 'array|size:6',
            'jugadas.*.*' => 'integer|min:1|max:60',
            'gratis' => 'nullable|array',
            'gratis.*' => 'nullable|in:1',
            'metodo_pago' => 'nullable|in:yape,plin',
            'numero_operacion' => 'nullable|string|max:50',
            'comprobante' => array_merge(['nullable'], ComprobantePagoService::REGLAS),
        ]);

        foreach ($datos['jugadas'] as $jugada) {
            if (count(array_unique($jugada)) !== 6) {
                return back()->withErrors(['jugadas' => 'Cada jugada debe tener 6 números distintos.'])->withInput();
            }
        }

        // Solo cuentan como gratis los índices que corresponden a una jugada real
        $gratisMarcadas = array_intersect_key(array_filter($datos['gratis'] ?? []), $datos['jugadas']);
        $cantidadGratisSolicitadas = count($gratisMarcadas);

        $cantidadJugadas = count($datos['jugadas']);
        $cantidadPagadas = $cantidadJugadas - $cantidadGratisSolicitadas;
        $montoTotal = $cantidadPagadas * 3.00;

        if ($montoTotal > 0 && empty($datos['metodo_pago'])) {
            return back()->withErrors(['metodo_pago' => 'Selecciona un método de pago.'])->withInput();
        }

        $numeroOperacion = filled($datos['numero_operacion'] ?? null) ? trim($datos['numero_operacion']) : null;

        $datosComprobante = [
            'comprobante' => null,
            'monto_detectado' => null,
            'numero_operacion' => $numeroOperacion,
            'requiere_revision' => $comprobantes->operacionRepetida($numeroOperacion),
        ];

        if ($request->hasFile('comprobante')) {
            $datosComprobante = $comprobantes->guardar($request->file('comprobante'), $montoTotal, $numeroOperacion);
        }

        try {
            $compra = DB::transaction(function () use ($cliente, $sorteo, $datos, $gratisMarcadas, $cantidadGratisSolicitadas, $cantidadJugadas, $montoTotal, $datosComprobante) {
                // Bloqueamos al cliente para que dos envíos simultáneos no gasten las mismas jugadas gratis
                $cliente = Cliente::whereKey($cliente->id)->lockForUpdate()->first();

                if ($cantidadGratisSolicitadas > $cliente->jugadas_gratis) {
                    throw ValidationException::withMessages(['gratis' => 'No tienes suficientes jugadas gratis disponibles.']);
                }

                $compra = Compra::create(array_merge([
                    'cliente_id' => $cliente->id,
                    'sorteo_id' => $sorteo->id,
                    'cantidad_jugadas' => $cantidadJugadas,
                    'monto_total' => $montoTotal,
                    'metodo_pago' => $datos['metodo_pago'] ?? null,
                    'estado_pago' => $montoTotal == 0 ? 'pagado' : 'pendiente',
                ], $datosComprobante));

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

                return $compra;
            });
        } catch (\Throwable $e) {
            $comprobantes->eliminar($datosComprobante['comprobante']);

            if ($e instanceof ValidationException) {
                return back()->withErrors($e->errors())->withInput();
            }

            throw $e;
        }

        $mensaje = $montoTotal == 0
            ? "¡Listo! {$cantidadJugadas} jugada(s) gratis registrada(s) para el sorteo."
            : "Compra registrada: {$cantidadJugadas} jugada(s) por S/ {$montoTotal}, en espera de validación de pago.";

        return redirect()->route('compras.comprobante', $compra)->with('exito', $mensaje);
    }
}
