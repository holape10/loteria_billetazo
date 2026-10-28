<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function edit()
    {
        return view('empresa.configuracion.edit', [
            'jugadasGratisRegistro' => Configuracion::jugadasGratisRegistro(),
            // Registros nuevos que ya recibieron el regalo (no cuenta a quienes tenían cuenta antes de esta opción)
            'regalosEntregados' => Cliente::whereNotNull('bono_bienvenida_at')
                ->where('bono_bienvenida_at', '>', Configuracion::where('clave', Configuracion::JUGADAS_GRATIS_REGISTRO)->value('created_at'))
                ->count(),
        ]);
    }

    public function update(Request $request)
    {
        $datos = $request->validate([
            'jugadas_gratis_registro' => 'required|integer|min:0|max:10',
        ], [
            'jugadas_gratis_registro.max' => 'Como máximo se pueden regalar 10 jugadas al registrarse.',
        ]);

        Configuracion::guardar(Configuracion::JUGADAS_GRATIS_REGISTRO, $datos['jugadas_gratis_registro']);

        $mensaje = $datos['jugadas_gratis_registro'] > 0
            ? "Listo: los nuevos jugadores recibirán {$datos['jugadas_gratis_registro']} jugada(s) gratis al registrarse."
            : 'Listo: ya no se regalan jugadas gratis al registrarse.';

        return back()->with('exito', $mensaje);
    }
}
