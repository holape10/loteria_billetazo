<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::whereIn('rol', ['administrador', 'moderador'])->orderBy('name')->get();

        return view('empresa.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('empresa.usuarios.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'rol' => 'required|in:administrador,moderador',
        ]);

        User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'rol' => $datos['rol'],
            'activo' => true,
        ]);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario creado correctamente.');
    }

    public function activar(User $usuario)
    {
        if ($usuario->esSuperAdmin()) {
            return back()->with('error', 'No puedes modificar al superadministrador.');
        }

        $usuario->update(['activo' => true]);

        return back()->with('exito', 'Usuario activado.');
    }

    public function desactivar(User $usuario)
    {
        if ($usuario->esSuperAdmin()) {
            return back()->with('error', 'No puedes modificar al superadministrador.');
        }

        $usuario->update(['activo' => false]);

        return back()->with('exito', 'Usuario desactivado.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->esSuperAdmin()) {
            return back()->with('error', 'No puedes eliminar al superadministrador.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('exito', 'Usuario eliminado.');
    }
}