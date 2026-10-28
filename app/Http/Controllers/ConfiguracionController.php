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
            'publicidadEnVivoModo' => Configuracion::publicidadEnVivoModo(),
            'publicidadInicioFrecuencia' => Configuracion::publicidadInicioFrecuencia(),
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
            'publicidad_en_vivo_modo' => 'required|in:' . implode(',', array_keys(Configuracion::MODOS_EN_VIVO)),
            'publicidad_inicio_frecuencia' => 'required|in:' . implode(',', array_keys(Configuracion::FRECUENCIAS_INICIO)),
        ], [
            'jugadas_gratis_registro.max' => 'Como máximo se pueden regalar 10 jugadas al registrarse.',
        ]);

        Configuracion::guardar(Configuracion::JUGADAS_GRATIS_REGISTRO, $datos['jugadas_gratis_registro']);
        Configuracion::guardar(Configuracion::PUBLICIDAD_EN_VIVO_MODO, $datos['publicidad_en_vivo_modo']);
        Configuracion::guardar(Configuracion::PUBLICIDAD_INICIO_FRECUENCIA, $datos['publicidad_inicio_frecuencia']);

        return back()->with('exito', 'Configuración guardada.');
    }
}
