<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DniController;
use App\Http\Controllers\SorteoController;
use App\Http\Controllers\BoletoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\GanadorController;
use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\IncidenciaController;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

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

Route::get('/ganadores', [GanadorController::class, 'index'])->name('ganadores.index');

Route::get('/terminos', function () {
    return view('legal.terminos');
})->name('terminos');

Route::post('/dni/consultar', [DniController::class, 'consultar'])
    ->middleware('throttle:10,1')
    ->name('dni.consultar');

/*
|--------------------------------------------------------------------------
| Solo administrador / superadmin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('clientes', ClienteController::class);

    Route::get('sorteos/crear', [SorteoController::class, 'create'])->name('sorteos.create');
    Route::post('sorteos', [SorteoController::class, 'store'])->name('sorteos.store');
    Route::get('sorteos/{sorteo}/editar', [SorteoController::class, 'edit'])->name('sorteos.edit');
    Route::put('sorteos/{sorteo}', [SorteoController::class, 'update'])->name('sorteos.update');
    Route::delete('sorteos/{sorteo}', [SorteoController::class, 'destroy'])->name('sorteos.destroy');

    Route::get('compras', [CompraController::class, 'index'])->name('compras.index');
    Route::post('compras/{compra}/aprobar', [CompraController::class, 'aprobar'])->name('compras.aprobar');
    Route::post('compras/{compra}/rechazar', [CompraController::class, 'rechazar'])->name('compras.rechazar');
    Route::post('compras/{compra}/incidencia', [IncidenciaController::class, 'store'])->name('incidencias.store');

    Route::get('reportes/clientes-frecuentes', [ReporteController::class, 'clientesFrecuentes'])->name('reportes.clientes-frecuentes');
    Route::get('reportes/numeros-frecuentes', [ReporteController::class, 'numerosFrecuentes'])->name('reportes.numeros-frecuentes');
    Route::get('reportes/financiero', [ReporteController::class, 'financiero'])->name('reportes.financiero');
});

/*
|--------------------------------------------------------------------------
| Administrador, superadmin y moderador (ver y ejecutar sorteos)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'sorteos'])->group(function () {
    Route::get('sorteos', [SorteoController::class, 'index'])->name('sorteos.index');
    Route::post('sorteos/{sorteo}/realizar', [SorteoController::class, 'realizar'])->name('sorteos.realizar');
    Route::get('sorteos/{sorteo}/realizar-manual', [SorteoController::class, 'formularioManual'])->name('sorteos.realizar-manual.form');
    Route::post('sorteos/{sorteo}/realizar-manual', [SorteoController::class, 'realizarManual'])->name('sorteos.realizar-manual');
    Route::get('sorteos/{sorteo}/individual', [SorteoController::class, 'individual'])->name('sorteos.individual');
    Route::post('sorteos/{sorteo}/verificar-parcial', [SorteoController::class, 'verificarParcial'])->name('sorteos.verificar-parcial');
});

/*
|--------------------------------------------------------------------------
| Solo superadmin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::post('usuarios/{usuario}/activar', [UsuarioController::class, 'activar'])->name('usuarios.activar');
    Route::post('usuarios/{usuario}/desactivar', [UsuarioController::class, 'desactivar'])->name('usuarios.desactivar');
    Route::delete('usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
});

/*
|--------------------------------------------------------------------------
| Cualquier usuario logueado (jugador, admin, moderador, superadmin)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/jugar', [BoletoController::class, 'create'])->name('boletos.create');
    Route::post('/jugar/{sorteo}', [BoletoController::class, 'store'])->name('boletos.store');
    Route::get('mis-incidencias', [IncidenciaController::class, 'index'])->name('incidencias.index');

    Route::get('compras/{compra}/comprobante', [ComprobanteController::class, 'ver'])->name('compras.comprobante');
    Route::get('compras/{compra}/comprobante/pdf', [ComprobanteController::class, 'pdf'])->name('compras.comprobante.pdf');
});

Route::get('/dashboard', function () {
    $usuario = auth()->user();
    $boletos = collect();
    $creditosGratis = 0;

    if ($usuario->cliente) {
        $boletos = $usuario->cliente->boletos()->with(['sorteo', 'compra'])->latest()->paginate(10);
        $creditosGratis = $usuario->cliente->jugadas_gratis;
    }

    return view('dashboard', compact('boletos', 'creditosGratis'));
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';