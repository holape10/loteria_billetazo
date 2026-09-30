<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">Editar sorteo</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">
                <form action="{{ route('sorteos.update', $sorteo) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300">Fecha del sorteo</label>
                        <input type="date" name="fecha" value="{{ old('fecha', $sorteo->fecha->format('Y-m-d')) }}" class="mt-1 block w-full bg-gray-800 border-gray-700 text-white rounded-md">
                        @error('fecha') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300">Hora del sorteo</label>
                        <input type="time" name="hora" value="{{ old('hora', $sorteo->hora) }}" class="mt-1 block w-full bg-gray-800 border-gray-700 text-white rounded-md">
                        @error('hora') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="px-4 py-2 bg-dorado-500 hover:bg-dorado-600 rounded text-black font-semibold">Actualizar</button>
                    <a href="{{ route('sorteos.index') }}" class="ml-2 text-gray-400">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>