<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">Clientes frecuentes</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">

                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Cliente</th>
                            <th class="py-2">DNI</th>
                            <th class="py-2">Jugadas totales</th>
                            <th class="py-2">Total gastado</th>
                            <th class="py-2">Veces ganó</th>
                            <th class="py-2">Última participación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clientes as $cliente)
                            <tr class="border-b">
                                <td class="py-2">{{ $cliente->nombre }}</td>
                                <td class="py-2">{{ $cliente->dni }}</td>
                                <td class="py-2">{{ $cliente->boletos_count }}</td>
                                <td class="py-2">S/ {{ number_format($cliente->boletos_sum_monto ?? 0, 2) }}</td>
                                <td class="py-2">{{ $cliente->veces_ganadas }}</td>
                                <td class="py-2">{{ $cliente->boletos_max_created_at ? \Carbon\Carbon::parse($cliente->boletos_max_created_at)->format('d/m/Y') : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">{{ $clientes->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>