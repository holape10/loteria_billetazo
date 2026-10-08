<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{
    /**
     * Para jugadores que no pueden recuperar su cuenta por correo: se genera una contraseña temporal
     * que el administrador le envía por WhatsApp. Al ingresar, el sistema lo obliga a crear una propia.
     */
    public function restablecerPassword(Cliente $cliente)
    {
        $usuario = $cliente->usuario;

        if (! $usuario) {
            return back()->with('error', 'Este cliente todavía no tiene una cuenta creada.');
        }

        // Fácil de dictar o escribir en el celular: "BZ" + 6 dígitos
        $temporal = 'BZ' . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $usuario->forceFill([
            'password' => Hash::make($temporal),
            'debe_cambiar_password' => true,
            'remember_token' => null,
        ])->save();

        return back()->with('password_temporal', [
            'cliente' => $cliente->nombre,
            'usuario' => $usuario->email,
            'dni' => $cliente->dni,
            'password' => $temporal,
            'celular' => $this->celularWhatsapp($cliente->celular),
        ]);
    }

    // "964 382 212" → "51964382212" (formato que pide wa.me)
    private function celularWhatsapp(?string $celular): ?string
    {
        $digitos = preg_replace('/\D/', '', (string) $celular);

        if (strlen($digitos) === 9) {
            return '51' . $digitos;
        }

        return strlen($digitos) >= 10 ? $digitos : null;
    }

    public function index()
    {
        $clientes = Cliente::with('usuario:id,cliente_id')->latest()->paginate(10);

        return view('empresa.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('empresa.clientes.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:150',
            'dni' => 'required|string|max:8|unique:clientes,dni',
            'direccion' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:15',
            'correo' => 'nullable|email|max:150',
        ]);

        Cliente::create($datos);

        return redirect()->route('clientes.index')->with('exito', 'Cliente registrado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('empresa.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:150',
            'dni' => 'required|string|max:8|unique:clientes,dni,' . $cliente->id,
            'direccion' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:15',
            'correo' => 'nullable|email|max:150',
            'estado' => 'required|boolean',
        ]);

        $cliente->update($datos);

        return redirect()->route('clientes.index')->with('exito', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')->with('exito', 'Cliente eliminado correctamente.');
    }
}