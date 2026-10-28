<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">📢 Publicidad del sorteo</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">

                @if (session('exito'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('exito') }}</div>
                @endif

                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <p class="text-sm text-gray-400 max-w-xl">
                        Las publicidades <strong class="text-gray-200">activas</strong> se muestran a pantalla completa, en este orden,
                        cuando los jugadores entran al sorteo en vivo antes de que salga el primer número,
                        y cada vez que presionas <strong class="text-gray-200">“📢 Pasar publicidad”</strong> en la pantalla del sorteo.
                    </p>
                    <a href="{{ route('publicidades.create') }}" class="px-4 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded">+ Nueva publicidad</a>
                </div>

                <div class="space-y-3">
                    @forelse ($publicidades as $p)
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-3 rounded-lg border {{ $p->activo ? 'border-gray-700 bg-gray-800/50' : 'border-gray-800 opacity-60' }}">
                            @if ($p->imagen)
                                <img src="{{ Storage::disk('public')->url($p->imagen) }}" alt="" class="w-full sm:w-28 h-28 sm:h-20 object-cover rounded">
                            @else
                                <div class="w-full sm:w-28 h-20 rounded bg-gray-800 flex items-center justify-center text-3xl">📢</div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-dorado-300">{{ $p->orden }}. {{ $p->titulo }}</p>
                                @if ($p->texto)
                                    <p class="text-sm text-gray-300 line-clamp-2">{{ $p->texto }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1">
                                    ⏱️ {{ $p->duracion_segundos }} s
                                    · {{ $p->audio ? '🎵 Audio propio' : ($p->con_voz ? '🗣️ Con voz' : '🔇 Sin voz') }}
                                    · {{ $p->activo ? '✅ Activa' : '⏸️ Inactiva' }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3 text-sm">
                                <form action="{{ route('publicidades.alternar', $p) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-dorado-400 hover:underline">{{ $p->activo ? 'Desactivar' : 'Activar' }}</button>
                                </form>
                                <a href="{{ route('publicidades.edit', $p) }}" class="text-blue-400 hover:underline">Editar</a>
                                <form action="{{ route('publicidades.destroy', $p) }}" method="POST" onsubmit="return confirm('¿Eliminar esta publicidad?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:underline">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center py-8">Todavía no hay publicidades. Crea la primera, por ejemplo: “Este sorteo llega gracias a tushpa.app”.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
