<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">Mi cuenta</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('exito'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('exito') }}</div>
            @endif

            @if (auth()->user()->rol === 'jugador')
                <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 mb-6 border border-dorado-700">
                    <a href="{{ route('boletos.create') }}" class="inline-block px-4 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded text-lg">🎟️ Jugar ahora</a>
                    @if ($creditosGratis > 0)
                        <span class="ml-3 text-dorado-400 font-semibold">🎁 Tienes {{ $creditosGratis }} jugada(s) gratis</span>
                    @endif
                </div>

                <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">
                    <h3 class="font-semibold mb-4 text-dorado-400 text-lg">Mi historial de jugadas</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-base">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="py-2 pr-4">Sorteo</th>
                                    <th class="py-2 pr-4">Números</th>
                                    <th class="py-2 pr-4">Aciertos</th>
                                    <th class="py-2 pr-4">Premio</th>
                                    <th class="py-2">Estado de pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($boletos as $boleto)
                                    <tr class="border-b border-gray-800">
                                        <td class="py-2 pr-4">{{ $boleto->sorteo->fecha->format('d/m/Y') }}</td>
                                        <td class="py-2 pr-4">{{ collect([$boleto->numero_1,$boleto->numero_2,$boleto->numero_3,$boleto->numero_4,$boleto->numero_5,$boleto->numero_6])->join(' - ') }}</td>
                                        <td class="py-2 pr-4">{{ $boleto->aciertos ?? '—' }}</td>
                                        <td class="py-2 pr-4">
                                            @if ($boleto->premio_ganado)
                                                <span class="text-dorado-400 font-semibold">S/ {{ number_format($boleto->premio_ganado, 2) }}</span>
                                            @elseif ($boleto->jugada_gratis_ganada)
                                                <span class="text-dorado-400 font-semibold">🎟️ Jugada gratis</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="py-2">{{ $boleto->compra ? ucfirst($boleto->compra->estado_pago) : '—' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-4 text-center text-gray-400">Todavía no has jugado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $boletos->links() }}</div>
                </div>
            @else
                <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">
                    <p class="text-gray-300 text-lg">Bienvenido, administrador. Usa el menú de arriba para gestionar clientes, sorteos, compras y reportes.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>