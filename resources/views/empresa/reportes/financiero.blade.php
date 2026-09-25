<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">Reporte Financiero</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">

                <form action="{{ route('reportes.financiero') }}" method="GET" class="mb-6 flex flex-wrap gap-2 items-end">
                    <div>
                        <label class="block text-xs text-gray-400">Desde</label>
                        <input type="date" name="fecha_desde" value="{{ $fechaDesde }}" class="bg-gray-800 border-gray-700 text-white rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400">Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ $fechaHasta }}" class="bg-gray-800 border-gray-700 text-white rounded-md text-sm">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded text-sm">Filtrar</button>
                    @if ($fechaDesde || $fechaHasta)
                        <a href="{{ route('reportes.financiero') }}" class="px-4 py-2 bg-gray-700 text-white rounded text-sm">Limpiar</a>
                    @endif
                </form>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="bg-gray-800 border border-dorado-600 rounded-xl p-5 text-center">
                        <p class="text-xs uppercase text-gray-400 mb-1">Boletos vendidos</p>
                        <p class="text-3xl font-extrabold text-white">{{ $totalBoletos }}</p>
                    </div>
                    <div class="bg-gray-800 border border-dorado-600 rounded-xl p-5 text-center">
                        <p class="text-xs uppercase text-gray-400 mb-1">Ventas (ingresos)</p>
                        <p class="text-3xl font-extrabold text-white">S/ {{ number_format($totalRecaudado, 2) }}</p>
                    </div>
                    <div class="bg-gray-800 border border-dorado-600 rounded-xl p-5 text-center">
                        <p class="text-xs uppercase text-gray-400 mb-1">Pagado en premios</p>
                        <p class="text-3xl font-extrabold text-white">S/ {{ number_format($totalPremiosPagados, 2) }}</p>
                    </div>
                    <div class="bg-gradient-to-b from-dorado-500 to-dorado-700 rounded-xl p-5 text-center text-black">
                        <p class="text-xs uppercase font-semibold mb-1">Balance</p>
                        <p class="text-3xl font-extrabold">S/ {{ number_format($balance, 2) }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>