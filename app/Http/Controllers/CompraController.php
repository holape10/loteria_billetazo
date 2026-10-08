<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->input('buscar');

        $compras = Compra::with(['cliente', 'sorteo'])
            ->when($busqueda, function ($query, $busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('numero_operacion', 'like', "%{$busqueda}%")
                        ->orWhereHas('cliente', function ($q) use ($busqueda) {
                            $q->where('nombre', 'like', "%{$busqueda}%")
                                ->orWhere('dni', 'like', "%{$busqueda}%")
                                ->orWhere('celular', 'like', "%{$busqueda}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Números de operación que aparecen en más de una compra (posible voucher reutilizado)
        $operacionesRepetidas = Compra::whereIn('numero_operacion', $compras->pluck('numero_operacion')->filter())
            ->where('estado_pago', '!=', 'rechazado')
            ->groupBy('numero_operacion')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('numero_operacion');

        return view('empresa.compras.index', compact('compras', 'busqueda', 'operacionesRepetidas'));
    }

    public function aprobar(Compra $compra)
    {
        $aprobada = DB::transaction(function () use ($compra) {
            $compra = Compra::whereKey($compra->id)->lockForUpdate()->first();

            if ($compra->estado_pago !== 'pendiente') {
                return false;
            }

            $compra->update(['estado_pago' => 'pagado']);
            $compra->cliente->increment('juegos', $compra->cantidad_jugadas);

            return true;
        });

        if (! $aprobada) {
            return back()->with('error', 'Esta compra ya fue procesada.');
        }

        return back()->with('exito', 'Compra aprobada correctamente.');
    }

    public function rechazar(Compra $compra)
    {
        if ($compra->estado_pago !== 'pendiente') {
            return back()->with('error', 'Esta compra ya fue procesada.');
        }

        $compra->update(['estado_pago' => 'rechazado']);

        return back()->with('exito', 'Compra rechazada.');
    }
}