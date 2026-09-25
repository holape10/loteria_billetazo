<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ganadores — El Billetazo</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-white font-sans antialiased">

    <nav class="bg-black border-b border-dorado-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('welcome') }}" class="text-dorado-400 font-extrabold text-xl">💰 El Billetazo</a>
                <a href="{{ route('welcome') }}" class="text-sm text-dorado-300 hover:text-dorado-400">← Volver al inicio</a>
            </div>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-dorado-400 mb-2 text-center">🏆 Ganadores</h1>

        @if ($ultimoSorteoJugado)
            <p class="text-center text-gray-400 mb-8">
                Sorteo del {{ $ultimoSorteoJugado->fecha->format('d/m/Y') }} —
                <span class="text-dorado-300 font-semibold">
                    {{ collect([$ultimoSorteoJugado->numero_1,$ultimoSorteoJugado->numero_2,$ultimoSorteoJugado->numero_3,$ultimoSorteoJugado->numero_4,$ultimoSorteoJugado->numero_5,$ultimoSorteoJugado->numero_6])->join(' - ') }}
                </span>
            </p>

            @if ($ganadores->isEmpty())
                <div class="bg-gray-900 border border-dorado-700 rounded-xl p-8 text-center text-gray-400">
                    Nadie ganó premio en este sorteo. ¡El pozo sigue creciendo para el próximo!
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($ganadores as $g)
                        <div class="bg-gray-900 border border-dorado-700 rounded-xl p-4 flex justify-between items-center">
                            <div>
                                <p class="font-semibold text-white">{{ $g->cliente->nombre }}</p>
                                <p class="text-sm text-gray-400">{{ $g->aciertos }} aciertos</p>
                            </div>
                            <p class="text-dorado-400 font-extrabold text-lg">
                                @if ($g->premio_ganado)
                                    S/ {{ number_format($g->premio_ganado, 2) }}
                                @else
                                    🎟️ Jugada gratis
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <p class="text-center text-gray-400">Todavía no se ha realizado ningún sorteo.</p>
        @endif

        <div class="text-center mt-10">
            <a href="{{ route('welcome') }}" class="inline-block px-6 py-3 bg-dorado-500 hover:bg-dorado-600 text-black font-bold rounded-full">← Volver al inicio</a>
        </div>
    </main>

    <footer class="bg-black border-t border-dorado-700 py-8 text-center text-gray-500 text-sm">
        &copy; {{ date('Y') }} Gana con El Billetazo. Todos los derechos reservados.
    </footer>

</body>
</html>