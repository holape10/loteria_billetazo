<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">Usuarios administradores</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">

                @if (session('exito'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('exito') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
                @endif

                <a href="{{ route('usuarios.create') }}" class="inline-block mb-4 px-4 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded">+ Nuevo usuario</a>

                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="py-2 pr-4">Nombre</th>
                            <th class="py-2 pr-4">Correo</th>
                            <th class="py-2 pr-4">Rol</th>
                            <th class="py-2 pr-4">Estado</th>
                            <th class="py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $usuario)
                            <tr class="border-b border-gray-800">
                                <td class="py-2 pr-4">{{ $usuario->name }}</td>
                                <td class="py-2 pr-4">{{ $usuario->email }}</td>
                                <td class="py-2 pr-4">{{ ucfirst($usuario->rol) }}</td>
                                <td class="py-2 pr-4">{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</td>
                                <td class="py-2">
                                    @if ($usuario->activo)
                                        <form action="{{ route('usuarios.desactivar', $usuario) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-yellow-400 font-semibold">Desactivar</button>
                                        </form>
                                    @else
                                        <form action="{{ route('usuarios.activar', $usuario) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-400 font-semibold">Activar</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="inline ml-2" onsubmit="return confirm('¿Eliminar este usuario?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 font-semibold">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>