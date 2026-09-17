<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->input('buscar');

        $compras = Compra::with(['cliente', 'sorteo'])
            ->when($busqueda, function ($query, $busqueda) {
                $query->whereHas('cliente', function ($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('dni', 'like', "%{$busqueda}%")
                        ->orWhere('celular', 'like', "%{$busqueda}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('empresa.compras.index', compact('compras', 'busqueda'));
    }

    public function aprobar(Compra $compra)
    {
        if ($compra->estado_pago !== 'pendiente') {
            return back()->with('error', 'Esta compra ya fue procesada.');
        }

        $compra->update(['estado_pago' => 'pagado']);
        $compra->cliente->increment('juegos', $compra->cantidad_jugadas);

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