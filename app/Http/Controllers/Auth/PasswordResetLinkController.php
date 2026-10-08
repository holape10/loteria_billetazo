<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password', [
            'whatsappSoporte' => config('services.soporte.whatsapp'),
        ]);
    }

    /**
     * El jugador puede escribir su correo o su DNI; el enlace siempre se envía al correo registrado.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'identificador' => ['required', 'string', 'max:255'],
        ], [
            'identificador.required' => 'Escribe tu correo o tu DNI.',
        ]);

        $usuario = $this->buscarUsuario(trim($request->input('identificador')));

        if (! $usuario) {
            return back()->withInput()->withErrors(['identificador' => __('passwords.user')]);
        }

        if (! $usuario->activo) {
            return back()->withInput()->withErrors(['identificador' => 'Tu cuenta está inactiva. Contacta al administrador.']);
        }

        $estado = Password::sendResetLink(['email' => $usuario->email]);

        if ($estado !== Password::RESET_LINK_SENT) {
            return back()->withInput()->withErrors(['identificador' => __($estado)]);
        }

        return back()->with('status', 'Te enviamos un enlace a ' . $this->ocultarCorreo($usuario->email)
            . '. Ábrelo para crear tu nueva contraseña (revisa también la carpeta de spam o promociones).');
    }

    private function buscarUsuario(string $identificador): ?User
    {
        if (preg_match('/^\d{8}$/', $identificador)) {
            $clienteId = Cliente::where('dni', $identificador)->value('id');

            return $clienteId ? User::where('cliente_id', $clienteId)->first() : null;
        }

        return User::where('email', Str::lower($identificador))->first();
    }

    // "jacker@jacker.com" → "ja****@jacker.com"
    private function ocultarCorreo(string $correo): string
    {
        [$usuario, $dominio] = explode('@', $correo, 2) + [1 => ''];

        return Str::substr($usuario, 0, 2) . str_repeat('*', max(Str::length($usuario) - 2, 3)) . '@' . $dominio;
    }
}
