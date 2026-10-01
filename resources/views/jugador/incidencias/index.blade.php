<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">🔔 Mis notificaciones</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">

                @forelse ($incidencias as $incidencia)
                    <div class="bg-gray-800 border border-orange-600 rounded-xl p-4 mb-4">
                        <p class="text-orange-400 font-semibold mb-1">🚩 Reporte sobre tu compra del {{ $incidencia->compra->sorteo->fecha->format('d/m/Y') }}</p>
                        <p class="text-gray-300 text-sm">{{ $incidencia->observaciones }}</p>
                        @if ($incidencia->fecha_deposito)
                            <p class="text-gray-500 text-xs mt-2">Fecha de depósito indicada: {{ $incidencia->fecha_deposito->format('d/m/Y') }}</p>
                        @endif
                        <p class="text-gray-500 text-xs mt-1">{{ $incidencia->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-8">No tienes notificaciones por ahora.</p>
                @endforelse

                <a href="{{ route('dashboard') }}" class="inline-block mt-4 text-dorado-400 underline">← Volver a mi cuenta</a>
            </div>
        </div>
    </div>
</x-app-layout>