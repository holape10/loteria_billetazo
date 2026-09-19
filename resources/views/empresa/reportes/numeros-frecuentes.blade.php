<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">Números más frecuentes</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <p class="text-sm text-gray-600 mb-4">Basado en {{ $totalSorteos }} sorteo(s) realizado(s).</p>

                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                    @foreach ($conteo as $numero => $veces)
                        <div class="border rounded p-3 text-center {{ $veces > 0 ? 'bg-dorado-50 border-dorado-400' : '' }}">
                            <p class="text-xl font-extrabold">{{ $numero }}</p>
                            <p class="text-xs text-gray-500">{{ $veces }} {{ $veces === 1 ? 'vez' : 'veces' }}</p>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</x-app-layout>