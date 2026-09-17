<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mi cuenta</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('exito'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('exito') }}</div>
            @endif

            @if (auth()->user()->rol === 'jugador')
                <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                    <a href="{{ route('boletos.create') }}" class="inline-block px-4 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded">🎟️ Jugar ahora</a>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold mb-4">Mi historial de jugadas</h3>

                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Sorteo</th>
                                <th class="py-2">Números</th>
                                <th class="py-2">Aciertos</th>
                                <th class="py-2">Premio</th>
                                <th class="py-2">Estado de pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($boletos as $boleto)
                                <tr class="border-b">
                                    <td class="py-2">{{ $boleto->sorteo->fecha->format('d/m/Y') }}</td>
                                    <td class="py-2">{{ collect([$boleto->numero_1,$boleto->numero_2,$boleto->numero_3,$boleto->numero_4,$boleto->numero_5,$boleto->numero_6])->join(' - ') }}</td>
                                    <td class="py-2">{{ $boleto->aciertos ?? '—' }}</td>
                                    <td class="py-2">{{ $boleto->premio_ganado ? 'S/ '.number_format($boleto->premio_ganado, 2) : '—' }}</td>
                                    <td class="py-2">{{ $boleto->compra ? ucfirst($boleto->compra->estado_pago) : '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-4 text-center text-gray-500">Todavía no has jugado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $boletos->links() }}</div>
                </div>
            @else
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="mb-4">Panel de administrador:</p>
                        <div class="flex gap-4 flex-wrap">
                            <a href="{{ route('clientes.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded">Clientes</a>
                            <a href="{{ route('sorteos.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded">Sorteos</a>
                            <a href="{{ route('compras.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded">Compras / Pagos</a>
                            <a href="{{ route('reportes.clientes-frecuentes') }}" class="px-4 py-2 bg-gray-800 text-white rounded">Clientes frecuentes</a>
                            <a href="{{ route('reportes.numeros-frecuentes') }}" class="px-4 py-2 bg-gray-800 text-white rounded">Números frecuentes</a>
                        </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>