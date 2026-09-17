<?php

namespace App\Http\Controllers;

use App\Models\Compra;

class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::with(['cliente', 'sorteo'])->latest()->paginate(10);

        return view('empresa.compras.index', compact('compras'));
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