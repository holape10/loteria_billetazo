<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\Cliente;
use App\Models\Sorteo;
use App\Services\SorteoEnVivoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SorteoController extends Controller
{
    public function index(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $numero = $request->input('numero');

        $sorteos = Sorteo::query()
            ->when($fechaDesde, fn ($q) => $q->whereDate('fecha', '>=', $fechaDesde))
            ->when($fechaHasta, fn ($q) => $q->whereDate('fecha', '<=', $fechaHasta))
            ->when($numero, function ($q) use ($numero) {
                $q->where(function ($qq) use ($numero) {
                    foreach (range(1, 6) as $i) {
                        $qq->orWhere("numero_{$i}", $numero);
                    }
                });
            })
            ->latest('fecha')
            ->paginate(50)
            ->withQueryString();

        return view('empresa.sorteos.index', compact('sorteos', 'fechaDesde', 'fechaHasta', 'numero'));
    }

    public function create()
    {
        $premioMayorProximo = $this->calcularProximoPremioMayor();

        return view('empresa.sorteos.create', compact('premioMayorProximo'));
    }

        public function edit(Sorteo $sorteo)
    {
        if ($sorteo->estado !== 'pendiente') {
            return back()->with('error', 'Solo puedes editar sorteos que todavía no se han jugado.');
        }

        if ($sorteo->estaEnVivo()) {
            return back()->with('error', 'No puedes modificar un sorteo que se está jugando en vivo.');
        }

        return view('empresa.sorteos.edit', compact('sorteo'));
    }

    public function update(Request $request, Sorteo $sorteo)
    {
        if ($sorteo->estado !== 'pendiente') {
            return back()->with('error', 'Solo puedes editar sorteos que todavía no se han jugado.');
        }

        if ($sorteo->estaEnVivo()) {
            return back()->with('error', 'No puedes modificar un sorteo que se está jugando en vivo.');
        }

        $datos = $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
        ]);

        $sorteo->update($datos);

        return redirect()->route('sorteos.index')->with('exito', 'Sorteo actualizado correctamente.');
    }

    public function destroy(Sorteo $sorteo)
    {
        if ($sorteo->estado !== 'pendiente') {
            return back()->with('error', 'Solo puedes eliminar sorteos que todavía no se han jugado.');
        }

        if ($sorteo->estaEnVivo()) {
            return back()->with('error', 'No puedes modificar un sorteo que se está jugando en vivo.');
        }

        if ($sorteo->boletos()->exists()) {
            return back()->with('error', 'No puedes eliminar un sorteo que ya tiene jugadas registradas.');
        }

        $sorteo->delete();

        return redirect()->route('sorteos.index')->with('exito', 'Sorteo eliminado correctamente.');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
        ]);

        $premioMayor = $this->calcularProximoPremioMayor();

        Sorteo::create(array_merge($datos, ['premio_mayor' => $premioMayor]));

        return redirect()->route('sorteos.index')
            ->with('exito', 'Sorteo programado correctamente. Premio mayor: S/ ' . number_format($premioMayor, 2));
    }

    public function realizar(Sorteo $sorteo)
    {
        if ($sorteo->estado !== 'pendiente') {
            return back()->with('error', 'Este sorteo ya fue jugado.');
        }

        if ($sorteo->estaEnVivo()) {
            return back()->with('error', 'Este sorteo ya se está jugando en vivo. Termínalo desde la pantalla del sorteo en vivo.');
        }

        $numerosGanadores = collect(range(1, 60))->shuffle()->take(6)->values()->all();

        if (! $this->procesarResultados($sorteo, $numerosGanadores)) {
            return back()->with('error', 'Este sorteo ya fue jugado.');
        }

        return redirect()->route('sorteos.index')
            ->with('exito', 'Sorteo realizado al azar. Números ganadores: ' . collect($numerosGanadores)->sort()->join(', '));
    }

    public function formularioManual(Sorteo $sorteo)
    {
        if ($sorteo->estado !== 'pendiente') {
            return back()->with('error', 'Este sorteo ya fue jugado.');
        }

        if ($sorteo->estaEnVivo()) {
            return redirect()->route('sorteos.individual', $sorteo)->with('error', 'Este sorteo ya se está jugando en vivo.');
        }

        return view('empresa.sorteos.realizar-manual', compact('sorteo'));
    }

    public function realizarManual(Request $request, Sorteo $sorteo)
    {
        if ($sorteo->estado !== 'pendiente') {
            return back()->with('error', 'Este sorteo ya fue jugado.');
        }

        if ($sorteo->estaEnVivo()) {
            return back()->with('error', 'Este sorteo ya se está jugando en vivo. Termínalo desde la pantalla del sorteo en vivo.');
        }

        $datos = $request->validate([
            'numeros' => 'required|array|size:6',
            'numeros.*' => 'integer|min:1|max:60|distinct',
        ]);

        if (! $this->procesarResultados($sorteo, $datos['numeros'])) {
            return back()->with('error', 'Este sorteo ya fue jugado.');
        }

        return redirect()->route('sorteos.index')
            ->with('exito', 'Sorteo realizado manualmente. Números ganadores: ' . collect($datos['numeros'])->sort()->join(', '));
    }

    public function individual(Sorteo $sorteo, SorteoEnVivoService $enVivo)
    {
        if ($sorteo->estado !== 'pendiente') {
            return back()->with('error', 'Este sorteo ya fue jugado.');
        }

        $enCarrera = $enVivo->enCarrera($sorteo, $sorteo->numeros_en_vivo ?? []);

        // Las jugadas con pago sin validar no participan: se avisa antes de empezar
        $comprasPendientes = $sorteo->compras()->where('estado_pago', 'pendiente')->count();

        return view('empresa.sorteos.individual', compact('sorteo', 'enCarrera', 'comprasPendientes'));
    }

    public function publicidad(Sorteo $sorteo, SorteoEnVivoService $enVivo)
    {
        if ($sorteo->estado !== 'pendiente') {
            return response()->json(['mensaje' => 'Este sorteo ya fue jugado.'], 422);
        }

        $enVivo->lanzarPublicidad($sorteo);

        return response()->json(['ok' => true]);
    }

    public function mezclar(Sorteo $sorteo, SorteoEnVivoService $enVivo)
    {
        if ($sorteo->estado !== 'pendiente' || count($sorteo->numeros_en_vivo ?? []) >= SorteoEnVivoService::TOTAL_NUMEROS) {
            return response()->json(['mensaje' => 'El sorteo ya no admite más extracciones.'], 422);
        }

        $enVivo->marcarMezclando($sorteo);

        return response()->json(['segundos' => SorteoEnVivoService::SEGUNDOS_MEZCLA]);
    }

    public function extraer(Sorteo $sorteo, SorteoEnVivoService $enVivo)
    {
        $extraidos = $enVivo->extraer($sorteo);

        if ($extraidos === null) {
            return response()->json(['mensaje' => 'El sorteo ya no admite más extracciones.'], 422);
        }

        return response()->json([
            'numero' => end($extraidos),
            'extraidos' => $extraidos,
            'en_carrera' => count($extraidos) >= 3 ? $enVivo->enCarrera($sorteo, $extraidos) : [],
        ]);
    }

    public function confirmarEnVivo(Sorteo $sorteo)
    {
        $extraidos = $sorteo->numeros_en_vivo ?? [];

        if (count($extraidos) !== SorteoEnVivoService::TOTAL_NUMEROS) {
            return back()->with('error', 'Aún no se han extraído los 6 números.');
        }

        if (! $this->procesarResultados($sorteo, $extraidos)) {
            return back()->with('error', 'Este sorteo ya fue jugado.');
        }

        return redirect()->route('sorteos.index')
            ->with('exito', 'Sorteo en vivo confirmado. Números ganadores: ' . collect($extraidos)->sort()->join(', '));
    }

    private function calcularProximoPremioMayor(): float
    {
        $ultimoSorteo = Sorteo::where('estado', 'cerrado')->latest('fecha')->latest('hora')->first();

        if (! $ultimoSorteo) {
            return 1000.00;
        }

        $huboGanadorMayor = $ultimoSorteo->boletos()->where('aciertos', 6)->exists();

        return $huboGanadorMayor ? 1000.00 : (float) $ultimoSorteo->premio_mayor + 200;
    }

    private function procesarResultados(Sorteo $sorteo, array $numerosGanadores): bool
    {
        // Bloqueamos el sorteo: si llegan dos peticiones a la vez (doble clic), solo la primera lo juega
        return DB::transaction(function () use ($sorteo, $numerosGanadores) {
            $sorteo = Sorteo::whereKey($sorteo->id)->lockForUpdate()->first();

            if ($sorteo->estado !== 'pendiente') {
                return false;
            }

            $this->asignarPremios($sorteo, $numerosGanadores);

            return true;
        });
    }

    private function asignarPremios(Sorteo $sorteo, array $numerosGanadores): void
    {
        sort($numerosGanadores);

        $sorteo->update([
            'numero_1' => $numerosGanadores[0],
            'numero_2' => $numerosGanadores[1],
            'numero_3' => $numerosGanadores[2],
            'numero_4' => $numerosGanadores[3],
            'numero_5' => $numerosGanadores[4],
            'numero_6' => $numerosGanadores[5],
        ]);

        $boletos = $sorteo->boletos()
            ->whereHas('compra', fn ($q) => $q->where('estado_pago', 'pagado'))
            ->get(['id', 'cliente_id', 'numero_1', 'numero_2', 'numero_3', 'numero_4', 'numero_5', 'numero_6']);

        // Aciertos de cada jugada, agrupados: [aciertos => [ids de boletos]]
        $porAciertos = [];
        foreach ($boletos as $boleto) {
            $aciertos = count(array_intersect([
                $boleto->numero_1, $boleto->numero_2, $boleto->numero_3,
                $boleto->numero_4, $boleto->numero_5, $boleto->numero_6,
            ], $numerosGanadores));
            $porAciertos[$aciertos][] = $boleto->id;
        }

        // Una consulta por grupo de aciertos (en vez de una por jugada)
        foreach ($porAciertos as $aciertos => $ids) {
            Boleto::whereIn('id', $ids)->update(['aciertos' => $aciertos]);
        }

        // Cada categoría se paga por separado (como dicen los términos): si alguien gana el pozo,
        // los demás igual cobran sus 5, 4 o 3 aciertos y su jugada gratis por 2 aciertos.
        // Si hay varios ganadores en una categoría, el premio se divide entre ellos.
        $premios = [
            6 => (float) $sorteo->premio_mayor,
            5 => (float) $sorteo->premio_cinco_aciertos,
            4 => (float) $sorteo->premio_cuatro_aciertos,
            3 => (float) $sorteo->premio_tres_aciertos,
        ];

        foreach ($premios as $aciertos => $premio) {
            $ids = $porAciertos[$aciertos] ?? [];
            if ($ids && $premio > 0) {
                Boleto::whereIn('id', $ids)->update(['premio_ganado' => round($premio / count($ids), 2)]);
            }
        }

        // 2 aciertos: una jugada gratis por cada jugada ganadora
        $idsDos = $porAciertos[2] ?? [];
        if ($idsDos) {
            Boleto::whereIn('id', $idsDos)->update(['jugada_gratis_ganada' => true]);

            $boletos->whereIn('id', $idsDos)->countBy('cliente_id')->each(
                fn ($cantidad, $clienteId) => Cliente::whereKey($clienteId)->increment('jugadas_gratis', $cantidad)
            );
        }

        $sorteo->update(['estado' => 'cerrado', 'cerrado_en' => now()]);
    }
}