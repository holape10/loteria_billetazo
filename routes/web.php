<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DniController;
use App\Http\Controllers\SorteoController;
use App\Http\Controllers\BoletoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ReporteController;


Route::get('/', function () {
    $proximoSorteo = \App\Models\Sorteo::where('estado', 'pendiente')
        ->orderBy('fecha')->orderBy('hora')->first();

    $ultimoSorteoJugado = \App\Models\Sorteo::where('estado', 'cerrado')
        ->whereNotNull('numero_1')
        ->orderByDesc('fecha')->orderByDesc('hora')
        ->first();

    $huboGanadorMayorSemanaPasada = $ultimoSorteoJugado
        ? $ultimoSorteoJugado->boletos()->where('aciertos', 6)->exists()
        : null;

    return view('welcome', compact('proximoSorteo', 'ultimoSorteoJugado', 'huboGanadorMayorSemanaPasada'));
})->name('welcome');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('clientes', ClienteController::class);
    Route::resource('sorteos', SorteoController::class)->except(['show', 'edit', 'update', 'destroy']);
    Route::post('sorteos/{sorteo}/realizar', [SorteoController::class, 'realizar'])->name('sorteos.realizar');
    Route::get('sorteos/{sorteo}/realizar-manual', [SorteoController::class, 'formularioManual'])->name('sorteos.realizar-manual.form');
    Route::post('sorteos/{sorteo}/realizar-manual', [SorteoController::class, 'realizarManual'])->name('sorteos.realizar-manual');
    Route::get('sorteos/{sorteo}/individual', [SorteoController::class, 'individual'])->name('sorteos.individual');
    Route::post('sorteos/{sorteo}/verificar-parcial', [SorteoController::class, 'verificarParcial'])->name('sorteos.verificar-parcial');
    
    Route::get('compras', [CompraController::class, 'index'])->name('compras.index');
    Route::post('compras/{compra}/aprobar', [CompraController::class, 'aprobar'])->name('compras.aprobar');
    Route::post('compras/{compra}/rechazar', [CompraController::class, 'rechazar'])->name('compras.rechazar');

    Route::get('reportes/clientes-frecuentes', [ReporteController::class, 'clientesFrecuentes'])->name('reportes.clientes-frecuentes');
    Route::get('reportes/numeros-frecuentes', [ReporteController::class, 'numerosFrecuentes'])->name('reportes.numeros-frecuentes');
});

Route::get('/dashboard', function () {
    $usuario = auth()->user();
    $boletos = collect();

    if ($usuario->cliente) {
        $boletos = $usuario->cliente->boletos()->with(['sorteo', 'compra'])->latest()->paginate(10);
    }

    return view('dashboard', compact('boletos'));
})->middleware(['auth', 'verified'])->name('dashboard');



Route::post('/dni/consultar', [DniController::class, 'consultar'])
    ->middleware('throttle:10,1')
    ->name('dni.consultar');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/jugar', [BoletoController::class, 'create'])->name('boletos.create');
    Route::post('/jugar/{sorteo}', [BoletoController::class, 'store'])->name('boletos.store');

});


require __DIR__.'/auth.php';
