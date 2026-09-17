<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Clientes
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('exito'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                        {{ session('exito') }}
                    </div>
                @endif

                <a href="{{ route('clientes.create') }}" class="inline-block mb-4 px-4 py-2 bg-gray-800 text-white rounded">
                    + Nuevo cliente
                </a>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Nombre</th>
                            <th class="py-2">DNI</th>
                            <th class="py-2">Celular</th>
                            <th class="py-2">Correo</th>
                            <th class="py-2">Juegos</th>
                            <th class="py-2">Estado</th>
                            <th class="py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clientes as $cliente)
                            <tr class="border-b">
                                <td class="py-2">{{ $cliente->nombre }}</td>
                                <td class="py-2">{{ $cliente->dni }}</td>
                                <td class="py-2">{{ $cliente->celular }}</td>
                                <td class="py-2">{{ $cliente->correo }}</td>
                                <td class="py-2">{{ $cliente->juegos }}</td>
                                <td class="py-2">{{ $cliente->estado ? 'Activo' : 'Inactivo' }}</td>
                                <td class="py-2">
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="text-blue-600">Editar</a>
                                    <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro de eliminar este cliente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 ml-2">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $clientes->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>