<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">
            Clientes
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">

                @if (session('exito'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                        {{ session('exito') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
                @endif

                @if ($temporal = session('password_temporal'))
                    @php
                        $mensajeWhatsapp = "Hola {$temporal['cliente']}, te escribimos de El Billetazo.\n\n"
                            . "Tu contraseña temporal es: {$temporal['password']}\n\n"
                            . "Ingresa con tu DNI o tu correo en " . route('login') . "\n"
                            . "Al entrar, el sistema te pedirá crear una contraseña nueva.";
                    @endphp
                    <div class="mb-4 p-4 rounded-lg border-2 border-dorado-500 bg-gray-800">
                        <p class="font-semibold text-dorado-400 mb-2">🔑 Contraseña temporal para {{ $temporal['cliente'] }}</p>
                        <div class="flex items-center gap-3 flex-wrap">
                            <code id="password-temporal" class="text-2xl font-black tracking-widest bg-black px-4 py-2 rounded select-all">{{ $temporal['password'] }}</code>
                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('password-temporal').textContent.trim()); this.textContent = '✅ Copiada'" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 rounded text-sm font-semibold">📋 Copiar</button>
                            @if ($temporal['celular'])
                                <a href="https://wa.me/{{ $temporal['celular'] }}?text={{ rawurlencode($mensajeWhatsapp) }}" target="_blank" rel="noopener" class="px-3 py-2 bg-green-600 hover:bg-green-700 rounded text-sm font-semibold">💬 Enviar por WhatsApp</a>
                            @else
                                <span class="text-sm text-yellow-400">Este cliente no tiene un celular válido registrado: entrégale la contraseña por otro medio.</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Solo se muestra esta vez. El jugador ingresa con su DNI o correo ({{ $temporal['usuario'] }}) y el sistema le pedirá crear una contraseña propia.</p>
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
                                    @if ($cliente->usuario)
                                        <form action="{{ route('clientes.restablecer-password', $cliente) }}" method="POST" class="inline" onsubmit="return confirm('¿Generar una contraseña temporal para {{ addslashes($cliente->nombre) }}? Su contraseña actual dejará de funcionar.')">
                                            @csrf
                                            <button type="submit" class="text-dorado-400 ml-2" title="Generar contraseña temporal y enviarla por WhatsApp">🔑 Contraseña</button>
                                        </form>
                                    @endif
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