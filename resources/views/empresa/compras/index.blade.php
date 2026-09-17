<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Compras / Pagos</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @if (session('exito'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('exito') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
                @endif

                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Cliente</th>
                            <th class="py-2">Sorteo</th>
                            <th class="py-2">Jugadas</th>
                            <th class="py-2">Total</th>
                            <th class="py-2">Método</th>
                            <th class="py-2">N° Operación</th>
                            <th class="py-2">Comprobante</th>
                            <th class="py-2">Estado</th>
                            <th class="py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compras as $compra)
                            <tr class="border-b">
                                <td class="py-2">{{ $compra->cliente->nombre }}</td>
                                <td class="py-2">{{ $compra->sorteo->fecha->format('d/m/Y') }}</td>
                                <td class="py-2">{{ $compra->cantidad_jugadas }}</td>
                                <td class="py-2">S/ {{ number_format($compra->monto_total, 2) }}</td>
                                <td class="py-2">{{ ucfirst($compra->metodo_pago) }}</td>
                                <td class="py-2">{{ $compra->numero_operacion }}</td>
                                <td class="py-2">
                                    @if ($compra->comprobante)
                                        <a href="{{ Storage::url($compra->comprobante) }}" target="_blank" class="text-blue-600">Ver imagen</a>
                                    @endif
                                </td>
                                <td class="py-2">{{ ucfirst($compra->estado_pago) }}</td>
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">{{ $compras->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>