<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">
            {{ $publicidad->exists ? 'Editar publicidad' : 'Nueva publicidad' }}
        </h2>
    </x-slot>

    @php
        $campo = 'mt-1 block w-full bg-gray-800 border-gray-700 text-white rounded-md focus:border-dorado-500 focus:ring-dorado-500';
        $etiqueta = 'block text-sm font-medium text-gray-300';
    @endphp

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                        @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                    </div>
                @endif

                <form action="{{ $publicidad->exists ? route('publicidades.update', $publicidad) : route('publicidades.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @if ($publicidad->exists) @method('PUT') @endif

                    <div>
                        <label class="{{ $etiqueta }}" for="titulo">Título <span class="text-dorado-500">*</span></label>
                        <input id="titulo" type="text" name="titulo" value="{{ old('titulo', $publicidad->titulo) }}" required maxlength="150" class="{{ $campo }}" placeholder="tushpa.app">
                    </div>

                    <div>
                        <label class="{{ $etiqueta }}" for="texto">Texto</label>
                        <textarea id="texto" name="texto" rows="3" maxlength="1000" class="{{ $campo }}" placeholder="Sistema de facturación electrónica. ¡Factura fácil desde tu celular!">{{ old('texto', $publicidad->texto) }}</textarea>
                    </div>

                    <div>
                        <label class="{{ $etiqueta }}" for="imagen">Imagen <span class="text-gray-500">(JPG, PNG o WEBP · máx. 3 MB)</span></label>
                        @if ($publicidad->imagen)
                            <div class="flex items-center gap-3 mt-2">
                                <img src="{{ Storage::disk('public')->url($publicidad->imagen) }}" alt="" class="h-20 rounded">
                                <label class="text-sm text-gray-300"><input type="checkbox" name="quitar_imagen" value="1" class="rounded bg-gray-800 border-gray-600"> Quitar imagen</label>
                            </div>
                        @endif
                        <input id="imagen" type="file" name="imagen" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm text-gray-300 file:mr-3 file:py-2 file:px-4 file:rounded file:border-0 file:bg-dorado-500 file:text-black file:font-semibold">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $etiqueta }}" for="duracion_segundos">Segundos en pantalla</label>
                            <input id="duracion_segundos" type="number" name="duracion_segundos" min="3" max="60" value="{{ old('duracion_segundos', $publicidad->duracion_segundos ?? 10) }}" class="{{ $campo }}">
                        </div>
                        <div>
                            <label class="{{ $etiqueta }}" for="orden">Orden</label>
                            <input id="orden" type="number" name="orden" min="0" max="999" value="{{ old('orden', $publicidad->orden ?? 0) }}" class="{{ $campo }}">
                        </div>
                    </div>

                    <fieldset class="border border-gray-700 rounded-lg p-4 space-y-4">
                        <legend class="px-2 text-sm font-semibold text-dorado-400">🗣️ Voz</legend>

                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="con_voz" value="1" id="con_voz" @checked(old('con_voz', $publicidad->con_voz)) class="rounded bg-gray-800 border-gray-600 text-dorado-500">
                            Leer esta publicidad en voz alta
                        </label>

                        <div>
                            <label class="{{ $etiqueta }}" for="texto_voz">Texto que dirá la voz <span class="text-gray-500">(si lo dejas vacío, lee el título y el texto)</span></label>
                            <textarea id="texto_voz" name="texto_voz" rows="2" maxlength="1000" class="{{ $campo }}" placeholder="Este sorteo llega gracias a tushpa punto app, tu sistema de facturación electrónica.">{{ old('texto_voz', $publicidad->texto_voz) }}</textarea>
                            <button type="button" id="probar-voz" class="mt-2 px-3 py-1.5 rounded bg-gray-700 hover:bg-gray-600 text-sm">▶️ Probar voz</button>
                            <span id="nombre-voz" class="ml-2 text-xs text-gray-500"></span>
                        </div>

                        <div>
                            <label class="{{ $etiqueta }}" for="audio">O sube un audio grabado <span class="text-gray-500">(MP3, M4A, OGG o WAV · máx. 5 MB · reemplaza a la voz automática)</span></label>
                            @if ($publicidad->audio)
                                <div class="flex items-center gap-3 mt-2">
                                    <audio controls src="{{ Storage::disk('public')->url($publicidad->audio) }}" class="h-9"></audio>
                                    <label class="text-sm text-gray-300"><input type="checkbox" name="quitar_audio" value="1" class="rounded bg-gray-800 border-gray-600"> Quitar audio</label>
                                </div>
                            @endif
                            <input id="audio" type="file" name="audio" accept="audio/*" class="mt-2 block w-full text-sm text-gray-300 file:mr-3 file:py-2 file:px-4 file:rounded file:border-0 file:bg-gray-700 file:text-white file:font-semibold">
                        </div>
                    </fieldset>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="activo" value="1" @checked(old('activo', $publicidad->activo)) class="rounded bg-gray-800 border-gray-600 text-dorado-500">
                        Activa (se muestra en el sorteo)
                    </label>

                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit" class="px-5 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded">Guardar</button>
                        <a href="{{ route('publicidades.index') }}" class="text-gray-400 hover:text-white">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/locutor.js') }}"></script>
    <script>
        document.getElementById('probar-voz').addEventListener('click', () => {
            const texto = document.getElementById('texto_voz').value.trim()
                || [document.getElementById('titulo').value, document.getElementById('texto').value].filter(Boolean).join('. ');
            Locutor.hablar(texto || 'Escribe un texto para probar la voz.', { forzar: true });
            document.getElementById('nombre-voz').textContent = 'Voz: ' + Locutor.nombreVoz();
        });
    </script>
</x-app-layout>
