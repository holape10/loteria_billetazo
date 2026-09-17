<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'dni' => ['required', 'digits:8'],
            'nombre' => ['required', 'string', 'max:150'],
            'celular' => ['required', 'string', 'max:15'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $cliente = Cliente::where('dni', $request->dni)->first();

        if ($cliente && User::where('cliente_id', $cliente->id)->exists()) {
            throw ValidationException::withMessages([
                'dni' => 'Este DNI ya tiene una cuenta registrada.',
            ]);
        }

        if ($cliente) {
            $cliente->update([
                'nombre' => $request->nombre,
                'celular' => $request->celular,
                'correo' => $request->email,
            ]);
        } else {
            $cliente = Cliente::create([
                'nombre' => $request->nombre,
                'dni' => $request->dni,
                'celular' => $request->celular,
                'correo' => $request->email,
                'juegos' => 0,
                'estado' => true,
            ]);
        }

        $user = User::create([
            'name' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'jugador',
            'cliente_id' => $cliente->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}