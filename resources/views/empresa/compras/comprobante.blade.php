<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">Comprobante de compra #{{ $compra->id }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">

                @if (session('exito'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('exito') }}</div>
                @endif

                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6 border-b border-gray-800 pb-4">
                    <div>
                        <p class="text-dorado-400 font-extrabold text-lg">💰 El Billetazo</p>
                        <p class="text-sm text-gray-400">Cliente: {{ $compra->cliente->nombre }}</p>
                        <p class="text-sm text-gray-400">Sorteo: {{ $compra->sorteo->fecha->format('d/m/Y') }} — {{ $compra->sorteo->hora }}</p>
                        <p class="text-sm text-gray-400">Fecha de compra: {{ $compra->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <img src="data:image/svg+xml;base64,{{ $qr }}" alt="Código QR" class="w-32 h-32 bg-white p-1 rounded">
                </div>

                <h3 class="font-semibold text-dorado-300 mb-3">Jugadas ({{ $compra->boletos->count() }})</h3>
                <div class="space-y-2 mb-6">
                    @foreach ($compra->boletos as $i => $boleto)
                        <div class="bg-gray-800 border border-gray-700 rounded-lg p-3 flex justify-between items-center">
                            <span class="text-sm text-gray-400">Jugada {{ $i + 1 }}</span>
                            <span class="font-bold text-dorado-400">
                                {{ collect([$boleto->numero_1,$boleto->numero_2,$boleto->numero_3,$boleto->numero_4,$boleto->numero_5,$boleto->numero_6])->join(' - ') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-800 pt-4 flex justify-between items-center mb-6">
                    <span class="text-gray-400">Total pagado</span>
                    <span class="text-2xl font-extrabold text-dorado-400">S/ {{ number_format($compra->monto_total, 2) }}</span>
                </div>

                <p class="text-center text-sm mb-6">
                    Estado del pago:
                    <span class="font-semibold {{ $compra->estado_pago === 'pagado' ? 'text-green-400' : ($compra->estado_pago === 'rechazado' ? 'text-red-400' : 'text-yellow-400') }}">
                        {{ ucfirst($compra->estado_pago) }}
                    </span>
                </p>

                <a href="{{ route('compras.comprobante.pdf', $compra) }}" class="block text-center w-full px-4 py-3 bg-dorado-500 hover:bg-dorado-600 text-black font-bold rounded-xl">
                    ⬇️ Descargar PDF
                </a>

            </div>
        </div>
    </div>
</x-app-layout>