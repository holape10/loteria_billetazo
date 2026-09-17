<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sorteos</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @if (session('exito'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('exito') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
                @endif

                <a href="{{ route('sorteos.create') }}" class="inline-block mb-4 px-4 py-2 bg-gray-800 text-white rounded">+ Programar sorteo</a>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Fecha</th>
                            <th class="py-2">Hora</th>
                            <th class="py-2">Números ganadores</th>
                            <th class="py-2">Estado</th>
                            <th class="py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sorteos as $sorteo)
                            <tr class="border-b">
                                <td class="py-2">{{ $sorteo->fecha->format('d/m/Y') }}</td>
                                <td class="py-2">{{ $sorteo->hora }}</td>
                                <td class="py-2">
                                    @if ($sorteo->numero_1)
                                        {{ collect([$sorteo->numero_1,$sorteo->numero_2,$sorteo->numero_3,$sorteo->numero_4,$sorteo->numero_5,$sorteo->numero_6])->join(' - ') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-2">{{ ucfirst($sorteo->estado) }}</td>
                                <td class="py-2">
                                    @if ($sorteo->estado === 'pendiente')
                                        <div class="flex flex-col gap-1">
                                            <form action="{{ route('sorteos.realizar', $sorteo) }}" method="POST" onsubmit="return confirm('¿Realizar el sorteo al azar ahora?')">
                                                @csrf
                                                <button type="submit" class="text-black bg-dorado-500 hover:bg-dorado-600 px-3 py-1 rounded text-sm font-semibold w-full">🎲 Sortear al azar</button>
                                            </form>
                                            <a href="{{ route('sorteos.realizar-manual.form', $sorteo) }}" class="text-black bg-gray-300 hover:bg-gray-400 px-3 py-1 rounded text-sm font-semibold text-center">✋ Elegir manualmente</a>
                                            <a href="{{ route('sorteos.individual', $sorteo) }}" class="text-black bg-dorado-300 hover:bg-dorado-400 px-3 py-1 rounded text-sm font-semibold text-center">🎱 Sorteo individual al azar</a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">{{ $sorteos->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>