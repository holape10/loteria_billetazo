<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificarCuentaActiva
{
    // Si una cuenta se desactiva o bloquea, su sesión abierta se cierra en la siguiente petición
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario) {
            $bloqueado = ! $usuario->activo
                || ($usuario->rol === 'jugador' && $usuario->cliente && ! $usuario->cliente->estado);

            if ($bloqueado) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Tu cuenta está inactiva o bloqueada. Contacta al administrador.',
                ]);
            }
        }

        return $next($request);
    }
}
