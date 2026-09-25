<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">Compras / Pagos</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">

                @if (session('exito'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('exito') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
                @endif

                <form action="{{ route('compras.index') }}" method="GET" class="mb-4 flex gap-2">
                    <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Buscar por DNI, nombre o celular..."
                           class="w-full max-w-sm border-gray-300 rounded-md text-sm">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded text-sm">Buscar</button>
                    @if ($busqueda)
                        <a href="{{ route('compras.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm">Limpiar</a>
                    @endif
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm whitespace-nowrap">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2 pr-4">Cliente</th>
                                <th class="py-2 pr-4">DNI</th>
                                <th class="py-2 pr-4">Celular</th>
                                <th class="py-2 pr-4">Sorteo</th>
                                <th class="py-2 pr-4">Jugadas</th>
                                <th class="py-2 pr-4">Total</th>
                                <th class="py-2 pr-4">Método</th>
                                <th class="py-2 pr-4" hidden="hidden">N° Operación</th>
                                <th class="py-2 pr-4">Comprobante</th>
                                <th class="py-2 pr-4">Estado</th>
                                <th class="py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($compras as $compra)
                                <tr class="border-b">
                                    <td class="py-2 pr-4">{{ $compra->cliente->nombre }}</td>
                                    <td class="py-2 pr-4">{{ $compra->cliente->dni }}</td>
                                    <td class="py-2 pr-4">{{ $compra->cliente->celular }}</td>
                                    <td class="py-2 pr-4">{{ $compra->sorteo->fecha->format('d/m/Y') }}</td>
                                    <td class="py-2 pr-4">{{ $compra->cantidad_jugadas }}</td>
                                    <td class="py-2 pr-4">S/ {{ number_format($compra->monto_total, 2) }}</td>
                                    <td class="py-2 pr-4">{{ ucfirst($compra->metodo_pago) }}</td>
                                    <td class="py-2 pr-4" hidden="hidden">{{ $compra->numero_operacion }}</td>
                                    <td class="py-2 pr-4">
                                        @if ($compra->comprobante)
                                            <a href="{{ Storage::url($compra->comprobante) }}" target="_blank" class="text-blue-600">Ver imagen</a>
                                        @endif
                                    </td>
                                    <td class="py-2 pr-4">{{ ucfirst($compra->estado_pago) }}</td>
                                                                        <td class="py-2">
                                        @if ($compra->estado_pago === 'pendiente')
                                            <form action="{{ route('compras.aprobar', $compra) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-700 font-semibold">Aprobar</button>
                                            </form>
                                            <form action="{{ route('compras.rechazar', $compra) }}" method="POST" class="inline ml-2">
                                                @csrf
                                                <button type="submit" class="text-red-700 font-semibold">Rechazar</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('compras.comprobante', $compra) }}" target="_blank" class="text-blue-600 font-semibold ml-2">👁️ Ver</a>
                                        <a href="{{ route('compras.comprobante.pdf', $compra) }}" target="_blank" class="text-dorado-600 font-semibold ml-2">📄 PDF</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="11" class="py-4 text-center text-gray-500">No se encontraron compras.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $compras->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>