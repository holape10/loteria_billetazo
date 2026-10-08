<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CambioPasswordTemporalController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        if (! $request->user()->debe_cambiar_password) {
            return redirect()->route('dashboard');
        }

        return view('auth.cambiar-password-temporal');
    }

    public function update(Request $request): RedirectResponse
    {
        $usuario = $request->user();

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (Hash::check($request->password, $usuario->password)) {
            return back()->withErrors(['password' => 'Elige una contraseña distinta a la temporal.']);
        }

        $usuario->forceFill([
            'password' => Hash::make($request->password),
            'debe_cambiar_password' => false,
        ])->save();

        return redirect()->route('dashboard')->with('exito', '¡Listo! Tu nueva contraseña quedó guardada.');
    }
}
