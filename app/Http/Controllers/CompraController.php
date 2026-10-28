<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Sorteo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public const POR_PAGINA = 100;

    // Compras del sorteo que todavía no se juega: la lista "limpia" para validar pagos nuevos
    public function index(Request $request)
    {
        return $this->listar($request, false);
    }

    // Compras de los sorteos ya jugados
    public function historial(Request $request)
    {
        return $this->listar($request, true);
    }

    private function listar(Request $request, bool $historial)
    {
        $busqueda = $request->input('buscar');
        $estado = $request->input('estado');
        $sorteoId = $historial ? $request->integer('sorteo') ?: null : null;

        $sorteosJugados = $historial
            ? Sorteo::where('estado', 'cerrado')->orderByDesc('fecha')->orderByDesc('hora')->get(['id', 'fecha', 'hora'])
            : collect();

        $base = Compra::query()
            ->whereHas('sorteo', fn ($q) => $q->where('estado', $historial ? 'cerrado' : 'pendiente'))
            ->when($sorteoId, fn ($q) => $q->where('sorteo_id', $sorteoId));

        // Resumen por estado de pago (antes de filtrar por estado o búsqueda)
        $resumen = (clone $base)->selectRaw('estado_pago, COUNT(*) as total, SUM(monto_total) as monto')
            ->groupBy('estado_pago')->get()->keyBy('estado_pago');

        $compras = $base->with(['cliente:id,nombre,dni,celular', 'sorteo:id,fecha,hora,estado'])
            ->when(in_array($estado, ['pendiente', 'pagado', 'rechazado'], true), fn ($q) => $q->where('estado_pago', $estado))
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
            ->paginate(self::POR_PAGINA)
            ->withQueryString();

        // Números de operación que aparecen en más de una compra (posible voucher reutilizado)
        $operacionesRepetidas = Compra::whereIn('numero_operacion', $compras->pluck('numero_operacion')->filter())
            ->where('estado_pago', '!=', 'rechazado')
            ->groupBy('numero_operacion')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('numero_operacion');

        return view('empresa.compras.index', compact(
            'compras', 'busqueda', 'estado', 'operacionesRepetidas', 'historial', 'sorteosJugados', 'sorteoId', 'resumen'
        ));
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
        $devueltas = DB::transaction(function () use ($compra) {
            $compra = Compra::whereKey($compra->id)->lockForUpdate()->first();

            if ($compra->estado_pago !== 'pendiente') {
                return null;
            }

            $compra->update(['estado_pago' => 'rechazado']);

            // Las jugadas gratis que el jugador usó en esta compra se le devuelven
            $gratis = $compra->boletos()->where('monto', 0)->count();
            if ($gratis > 0) {
                $compra->cliente()->increment('jugadas_gratis', $gratis);
            }

            return $gratis;
        });

        if ($devueltas === null) {
            return back()->with('error', 'Esta compra ya fue procesada.');
        }

        return back()->with('exito', $devueltas > 0
            ? "Compra rechazada. Se le devolvieron {$devueltas} jugada(s) gratis al jugador."
            : 'Compra rechazada.');
    }
}