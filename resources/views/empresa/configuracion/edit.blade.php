<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">⚙️ Configuración</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">

                @if (session('exito'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('exito') }}</div>
                @endif

                <form action="{{ route('configuracion.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h3 class="text-lg font-bold text-dorado-300 mb-1">🎁 Regalo de bienvenida</h3>
                    <p class="text-sm text-gray-400 mb-4">
                        Jugadas gratis que recibe cada jugador nuevo al registrarse. Cada DNI lo recibe una sola vez.
                        Pon <strong class="text-gray-200">0</strong> para dejar de regalar.
                    </p>

                    <label for="jugadas_gratis_registro" class="block text-sm font-medium text-gray-300">Jugadas gratis al registrarse</label>
                    <div class="mt-1 flex items-center gap-3">
                        <input id="jugadas_gratis_registro" type="number" name="jugadas_gratis_registro" min="0" max="10" required
                               value="{{ old('jugadas_gratis_registro', $jugadasGratisRegistro) }}"
                               class="w-28 bg-gray-800 border-gray-700 text-white text-2xl font-bold text-center rounded-md focus:border-dorado-500 focus:ring-dorado-500">
                        <span class="text-sm {{ $jugadasGratisRegistro > 0 ? 'text-green-400' : 'text-gray-400' }}">
                            {{ $jugadasGratisRegistro > 0 ? '✅ Activo' : '⏸️ Desactivado' }}
                        </span>
                    </div>
                    @error('jugadas_gratis_registro') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror

                    <p class="text-xs text-gray-500 mt-3">Regalos entregados a nuevos registros hasta hoy: {{ $regalosEntregados }}</p>

                    <button type="submit" class="mt-6 px-5 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
