<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::latest()->paginate(10);

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