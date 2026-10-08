<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForzarCambioPassword
{
    // Quien entra con una contraseña temporal debe crear la suya antes de usar el sistema
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario?->debe_cambiar_password && ! $request->routeIs('password.temporal.*', 'logout')) {
            return redirect()->route('password.temporal.edit');
        }

        return $next($request);
    }
}
