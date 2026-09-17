<?php

namespace App\Http\Controllers;

use App\Services\DniService;
use Illuminate\Http\Request;

class DniController extends Controller
{
    public function consultar(Request $request, DniService $dniService)
    {
        $request->validate([
            'dni' => 'required|digits:8',
        ]);

        $datos = $dniService->consultar($request->dni);

        if (! $datos) {
            return response()->json(['encontrado' => false]);
        }

        return response()->json([
            'encontrado' => true,
            'nombre_completo' => $datos['nombre_completo'],
        ]);
    }
}