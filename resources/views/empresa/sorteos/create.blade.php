<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Programar sorteo</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="mb-4 p-3 bg-dorado-100 border border-dorado-400 rounded text-sm">
                    💰 El premio mayor de este sorteo será: <strong>S/ {{ number_format($premioMayorProximo, 2) }}</strong>
                </div>
                <form action="{{ route('sorteos.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Fecha del sorteo</label>
                        <input type="date" name="fecha" value="{{ old('fecha') }}" class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('fecha') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Hora del sorteo</label>
                        <input type="time" name="hora" value="{{ old('hora', '16:00') }}" class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('hora') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="px-4 py-2 bg-dorado-500 hover:bg-dorado-600 rounded text-black font-semibold">Programar</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>