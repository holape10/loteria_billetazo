<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">Sorteos</h2>
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

                <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                    <a href="{{ route('sorteos.create') }}" class="inline-block px-4 py-2 bg-gray-800 text-white rounded">+ Programar sorteo</a>
                </div>

                <form action="{{ route('sorteos.index') }}" method="GET" class="mb-4 flex flex-wrap gap-2 items-end">
                    <div>
                        <label class="block text-xs text-gray-600">Desde</label>
                        <input type="date" name="fecha_desde" value="{{ $fechaDesde }}" class="border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600">Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ $fechaHasta }}" class="border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600">Número</label>
                        <input type="number" name="numero" min="1" max="60" value="{{ $numero }}" placeholder="Ej: 23" class="border-gray-300 rounded-md text-sm w-24">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded text-sm">Buscar</button>
                    @if ($fechaDesde || $fechaHasta || $numero)
                        <a href="{{ route('sorteos.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm">Limpiar</a>
                    @endif
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2 pr-4">Fecha</th>
                                <th class="py-2 pr-4">Hora</th>
                                <th class="py-2 pr-4">Números ganadores</th>
                                <th class="py-2 pr-4">Premio mayor</th>
                                <th class="py-2 pr-4">Ganadores</th>
                                <th class="py-2 pr-4">Estado</th>
                                <th class="py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sorteos as $sorteo)
                                <tr class="border-b align-top">
                                    <td class="py-2 pr-4">{{ $sorteo->fecha->format('d/m/Y') }}</td>
                                    <td class="py-2 pr-4">{{ $sorteo->hora }}</td>
                                    <td class="py-2 pr-4">
                                        @if ($sorteo->numero_1)
                                            {{ collect([$sorteo->numero_1,$sorteo->numero_2,$sorteo->numero_3,$sorteo->numero_4,$sorteo->numero_5,$sorteo->numero_6])->join(' - ') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="py-2 pr-4">S/ {{ number_format($sorteo->premio_mayor, 2) }}</td>
                                    <td class="py-2 pr-4">
                                        @if ($sorteo->estado === 'cerrado')
                                            @php $ganadores = $sorteo->ganadores(); @endphp
                                            @if ($ganadores->isEmpty())
                                                <span class="text-gray-500 text-xs">Sin ganadores (pozo sube +S/200)</span>
                                            @else
                                                <ul class="text-xs space-y-0.5">
                                                    @foreach ($ganadores as $g)
                                                        <li>
                                                            <span class="font-semibold">{{ $g->cliente->nombre }}</span>
                                                            — {{ $g->aciertos }} aciertos
                                                            — S/ {{ number_format($g->premio_ganado, 2) }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        @else
                                            <span class="text-gray-400 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2 pr-4">{{ ucfirst($sorteo->estado) }}</td>
                                    <td class="py-2">
                                        @if ($sorteo->estado === 'pendiente')
                                            <div class="flex flex-col gap-1">
                                                <form action="{{ route('sorteos.realizar', $sorteo) }}" method="POST" onsubmit="return confirm('¿Realizar el sorteo al azar ahora?')">
                                                    @csrf
                                                    <!--<button type="submit" class="text-black bg-dorado-500 hover:bg-dorado-600 px-3 py-1 rounded text-sm font-semibold w-full">🎲 Sortear al azar</button>
                                                </form>
                                                <a href="{{ route('sorteos.realizar-manual.form', $sorteo) }}" class="text-black bg-gray-300 hover:bg-gray-400 px-3 py-1 rounded text-sm font-semibold text-center">✋ Manual</a>-->
                                                <a href="{{ route('sorteos.individual', $sorteo) }}" class="text-black bg-dorado-300 hover:bg-dorado-400 px-3 py-1 rounded text-sm font-semibold text-center">🎱 Inicia Sorteo</a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-4 text-center text-gray-500">No se encontraron sorteos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $sorteos->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>