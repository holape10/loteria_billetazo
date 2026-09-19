<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gana con El Billetazo</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-white font-sans antialiased">

    <!-- Navbar -->
    <nav class="bg-black border-b border-dorado-600">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <span class="text-dorado-400 font-extrabold text-xl">💰 El Billetazo</span>

                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm text-dorado-300 hover:text-dorado-400">Mi cuenta</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-dorado-300 hover:text-dorado-400">Iniciar sesión</a>
                        <a href="{{ route('register') }}" class="text-sm bg-dorado-500 hover:bg-dorado-600 text-black font-semibold px-4 py-2 rounded-full">Regístrate</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

        @if ($proximoSorteo)
        <section class="bg-black py-10 border-t border-b border-dorado-700 text-center">
            <p class="text-dorado-400 font-semibold mb-4 uppercase tracking-wide text-sm">Próximo sorteo</p>
            <div id="cronometro" class="flex justify-center gap-4 sm:gap-8" data-fecha="{{ $proximoSorteo->fecha->format('Y-m-d') }}T{{ $proximoSorteo->hora }}">
                <div class="text-center">
                    <div class="text-3xl sm:text-5xl font-extrabold text-white" id="dias">00</div>
                    <div class="text-xs text-gray-400 uppercase">Días</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl sm:text-5xl font-extrabold text-white" id="horas">00</div>
                    <div class="text-xs text-gray-400 uppercase">Horas</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl sm:text-5xl font-extrabold text-white" id="minutos">00</div>
                    <div class="text-xs text-gray-400 uppercase">Min</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl sm:text-5xl font-extrabold text-dorado-400" id="segundos">00</div>
                    <div class="text-xs text-gray-400 uppercase">Seg</div>
                </div>
            </div>
        </section>

        <script>
            const cronometroEl = document.getElementById('cronometro');
            const fechaObjetivo = new Date(cronometroEl.dataset.fecha).getTime();

            const intervalo = setInterval(function () {
                const ahora = new Date().getTime();
                const distancia = fechaObjetivo - ahora;

                if (distancia <= 0) {
                    clearInterval(intervalo);
                    document.getElementById('dias').textContent = '00';
                    document.getElementById('horas').textContent = '00';
                    document.getElementById('minutos').textContent = '00';
                    document.getElementById('segundos').textContent = '00';
                    return;
                }

                const dias = Math.floor(distancia / (1000 * 60 * 60 * 24));
                const horas = Math.floor((distancia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutos = Math.floor((distancia % (1000 * 60 * 60)) / (1000 * 60));
                const segundos = Math.floor((distancia % (1000 * 60)) / 1000);

                document.getElementById('dias').textContent = String(dias).padStart(2, '0');
                document.getElementById('horas').textContent = String(horas).padStart(2, '0');
                document.getElementById('minutos').textContent = String(minutos).padStart(2, '0');
                document.getElementById('segundos').textContent = String(segundos).padStart(2, '0');
            }, 1000);
        </script>
    @endif

    <!-- Hero -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <h1 class="text-4xl sm:text-6xl font-extrabold text-dorado-400 leading-tight">
            Gana <span class="text-white">con El Billetazo</span>
        </h1>
        <p class="mt-4 text-gray-300 text-lg max-w-2xl mx-auto">
            El sorteo semanal más fácil y confiable del Perú. Elige tus 6 números, paga con Yape o Plin, y gana todos los domingos.
        </p>
        <div class="mt-8 flex justify-center gap-4">
            @guest
                <a href="{{ route('register') }}" class="bg-dorado-500 hover:bg-dorado-600 text-black font-bold px-6 py-3 rounded-full">Participar ahora</a>
            @endguest
        </div>
    </section>

        @if ($proximoSorteo)
        <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mb-10">
            <div class="bg-gradient-to-r from-yellow-600 via-dorado-500 to-yellow-600 rounded-2xl p-6 sm:p-8 text-center text-black shadow-xl border-4 border-dorado-300">
                @if ($huboGanadorMayorSemanaPasada === false)
                    <p class="text-sm sm:text-base font-semibold mb-1">🔥 ¡Esta semana no hubo ganador del premio mayor! El pozo sigue creciendo</p>
                @endif
                <p class="text-xs uppercase font-bold tracking-widest">Pozo acumulado — Próximo sorteo</p>
                <p class="text-4xl sm:text-6xl font-extrabold my-2">S/ {{ number_format($proximoSorteo->premio_mayor, 2) }}</p>
                <p class="text-xs sm:text-sm">Sube S/ 200 cada semana que nadie hace los 6 aciertos</p>
            </div>
        </section>
    @endif

        @if ($ultimoSorteoJugado)
        <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 -mt-2 mb-4">
            <div class="bg-gradient-to-r from-dorado-600 to-dorado-500 rounded-xl p-5 text-center text-black shadow-lg">
                <p class="text-xs uppercase font-semibold tracking-wide">Último sorteo — {{ $ultimoSorteoJugado->fecha->format('d/m/Y') }}</p>
                <p class="text-2xl sm:text-3xl font-extrabold mt-1">
                    {{ collect([
                        $ultimoSorteoJugado->numero_1,
                        $ultimoSorteoJugado->numero_2,
                        $ultimoSorteoJugado->numero_3,
                        $ultimoSorteoJugado->numero_4,
                        $ultimoSorteoJugado->numero_5,
                        $ultimoSorteoJugado->numero_6,
                    ])->join(' - ') }}
                </p>
            </div>
        </section>
    @endif

    <!-- Cómo funciona -->
    <section class="bg-black py-16 border-t border-dorado-700">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-dorado-400 mb-10">¿Cómo funciona?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-900 rounded-xl p-6 border border-dorado-700">
                    <div class="text-dorado-400 text-3xl mb-3">1️⃣</div>
                    <h3 class="font-semibold text-lg mb-2">Regístrate</h3>
                    <p class="text-gray-400 text-sm">Con tu DNI y tu número de Yape o Plin. Es rápido y sencillo.</p>
                </div>
                <div class="bg-gray-900 rounded-xl p-6 border border-dorado-700">
                    <div class="text-dorado-400 text-3xl mb-3">2️⃣</div>
                    <h3 class="font-semibold text-lg mb-2">Elige tus números</h3>
                    <p class="text-gray-400 text-sm">Selecciona 6 números del 1 al 60 por solo S/ 1 el boleto.</p>
                </div>
                <div class="bg-gray-900 rounded-xl p-6 border border-dorado-700">
                    <div class="text-dorado-400 text-3xl mb-3">3️⃣</div>
                    <h3 class="font-semibold text-lg mb-2">Espera el sorteo</h3>
                    <p class="text-gray-400 text-sm">Todos los domingos a las 4:00 p.m. ¡Gana hasta S/ 1,000!</p>
                </div>
            </div>
        </div>
    </section>

        <!-- Premios -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-dorado-400 mb-10">Premios</h2>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 sm:gap-6 text-center">
            <div class="bg-gradient-to-b from-dorado-500 to-dorado-700 rounded-xl p-6 text-black col-span-2 sm:col-span-1">
                <p class="text-base sm:text-lg font-semibold">6 aciertos</p>
                <p class="text-4xl sm:text-4xl font-extrabold">S/ 1,000</p>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-dorado-600">
                <p class="text-base sm:text-lg font-semibold text-dorado-300">5 aciertos</p>
                <p class="text-3xl sm:text-4xl font-extrabold text-white">S/ 100</p>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-dorado-600">
                <p class="text-base sm:text-lg font-semibold text-dorado-300">4 aciertos</p>
                <p class="text-3xl sm:text-4xl font-extrabold text-white">S/ 50</p>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-dorado-600">
                <p class="text-base sm:text-lg font-semibold text-dorado-300">3 aciertos</p>
                <p class="text-3xl sm:text-4xl font-extrabold text-white">S/ 10</p>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-dorado-600">
                <p class="text-base sm:text-lg font-semibold text-dorado-300">2 aciertos</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-white">Jugada gratis</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black border-t border-dorado-700 py-8 text-center text-gray-500 text-sm">
        &copy; {{ date('Y') }} Gana con El Billetazo. Todos los derechos reservados.
    </footer>

</body>
</html>