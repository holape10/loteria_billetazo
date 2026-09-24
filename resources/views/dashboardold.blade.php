<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gana con El Billetazo</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Animación para el pozo mayor - Efecto explosivo */
        @keyframes pulse-gold {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 20px rgba(234, 179, 8, 0.5), 
                            0 0 40px rgba(234, 179, 8, 0.3),
                            0 0 60px rgba(234, 179, 8, 0.1);
            }
            50% {
                transform: scale(1.02);
                box-shadow: 0 0 40px rgba(234, 179, 8, 0.8), 
                            0 0 80px rgba(234, 179, 8, 0.5),
                            0 0 120px rgba(234, 179, 8, 0.3);
            }
        }

        @keyframes shine {
            0% {
                background-position: -200% center;
            }
            100% {
                background-position: 200% center;
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes rotate-3d {
            0% {
                transform: rotateY(0deg) rotateX(10deg);
            }
            100% {
                transform: rotateY(360deg) rotateX(10deg);
            }
        }

        @keyframes flip-number {
            0% {
                transform: rotateX(0deg);
            }
            50% {
                transform: rotateX(90deg);
            }
            100% {
                transform: rotateX(0deg);
            }
        }

        .prize-explosion {
            animation: pulse-gold 2s ease-in-out infinite, float 3s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .prize-explosion::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.4),
                transparent
            );
            animation: shine 3s infinite;
        }

        .number-ball {
            animation: rotate-3d 3s linear infinite;
            transform-style: preserve-3d;
            display: inline-block;
        }

        .number-ball.flip {
            animation: flip-number 0.6s ease-in-out;
        }

        .glow-text {
            text-shadow: 0 0 10px rgba(234, 179, 8, 0.8),
                         0 0 20px rgba(234, 179, 8, 0.6),
                         0 0 30px rgba(234, 179, 8, 0.4);
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #fbbf24;
            border-radius: 50%;
            pointer-events: none;
        }

        @keyframes particle-explode {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translate(var(--tx), var(--ty)) scale(0);
                opacity: 0;
            }
        }
    </style>
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
        <div class="prize-explosion bg-gradient-to-r from-yellow-600 via-dorado-500 to-yellow-600 rounded-2xl p-6 sm:p-8 text-center text-black shadow-2xl border-4 border-dorado-300 relative overflow-hidden">
            <!-- Partículas decorativas -->
            <div class="absolute inset-0 pointer-events-none" id="particles"></div>
            
            @if ($huboGanadorMayorSemanaPasada === false)
                <div class="flex items-center justify-center gap-2 mb-2 animate-pulse">
                    <span class="text-2xl">🔥</span>
                    <p class="text-sm sm:text-base font-bold">¡Esta semana no hubo ganador del premio mayor! El pozo sigue creciendo</p>
                    <span class="text-2xl">🔥</span>
                </div>
            @endif
            
            <p class="text-xs uppercase font-bold tracking-widest mb-2">Pozo acumulado — Próximo sorteo</p>
            
            <div class="relative z-10">
                <p class="text-5xl sm:text-7xl font-extrabold my-4 glow-text" id="premio-mayor">
                    S/ {{ number_format($proximoSorteo->premio_mayor, 2) }}
                </p>
            </div>
            
            <div class="bg-black bg-opacity-20 rounded-lg p-3 mt-4 inline-block">
                <p class="text-xs sm:text-sm font-semibold">💰 Sube S/ 200 cada semana si no hay ganadores con 6 aciertos</p>
            </div>
        </div>
    </section>

    <script>
        // Crear efecto de partículas explosivas
        function createParticles() {
            const container = document.getElementById('particles');
            const particleCount = 20;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                const angle = (Math.PI * 2 * i) / particleCount;
                const velocity = 100 + Math.random() * 100;
                const tx = Math.cos(angle) * velocity;
                const ty = Math.sin(angle) * velocity;
                
                particle.style.setProperty('--tx', `${tx}px`);
                particle.style.setProperty('--ty', `${ty}px`);
                particle.style.left = '50%';
                particle.style.top = '50%';
                particle.style.animation = `particle-explode 2s ease-out ${Math.random() * 2}s infinite`;
                
                container.appendChild(particle);
            }
        }
        
        document.addEventListener('DOMContentLoaded', createParticles);
    </script>
    @endif

    @if ($ultimoSorteoJugado)
    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 -mt-2 mb-4">
        <div class="bg-gradient-to-r from-dorado-600 to-dorado-500 rounded-xl p-5 text-center text-black shadow-lg border-2 border-dorado-300">
            <p class="text-xs uppercase font-semibold tracking-wide mb-3">
                🎰 Último sorteo — {{ $ultimoSorteoJugado->fecha->format('d/m/Y') }}
            </p>
            
            <div class="flex justify-center gap-2 sm:gap-4 flex-wrap" id="numeros-sorteo">
                @php
                    $numeros = [
                        $ultimoSorteoJugado->numero_1,
                        $ultimoSorteoJugado->numero_2,
                        $ultimoSorteoJugado->numero_3,
                        $ultimoSorteoJugado->numero_4,
                        $ultimoSorteoJugado->numero_5,
                        $ultimoSorteoJugado->numero_6,
                    ];
                @endphp
                
                @foreach($numeros as $index => $numero)
                    <div class="number-ball-container" data-index="{{ $index }}">
                        <div class="number-ball bg-white rounded-full w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center text-2xl sm:text-3xl font-extrabold shadow-lg border-4 border-dorado-600">
                            {{ str_pad($numero, 2, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                    @if(!$loop->last)
                        <span class="text-dorado-900 font-bold self-center text-xl">-</span>
                    @endif
                @endforeach
            </div>
            
        </div>
    </section>

    <script>
        // Animación de rotación para los números del sorteo
        document.addEventListener('DOMContentLoaded', function() {
            const numberBalls = document.querySelectorAll('.number-ball');
            
            // Rotación continua cada 3 segundos
            setInterval(() => {
                numberBalls.forEach((ball, index) => {
                    // Pequeño delay entre cada bola para efecto más natural
                    setTimeout(() => {
                        ball.classList.remove('flip');
                        void ball.offsetWidth; // Trigger reflow
                        ball.classList.add('flip');
                    }, index * 100);
                });
            }, 3000);
            
            // Iniciar primera rotación después de 3 segundos
            setTimeout(() => {
                numberBalls.forEach((ball, index) => {
                    setTimeout(() => {
                        ball.classList.add('flip');
                    }, index * 100);
                });
            }, 3000);
        });
    </script>
    @endif

    <!-- Cómo funciona -->
    <section class="bg-black py-16 border-t border-dorado-700">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-dorado-400 mb-10">¿Cómo funciona?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-900 rounded-xl p-6 border border-dorado-700 hover:border-dorado-400 transition-all duration-300 hover:transform hover:scale-105">
                    <div class="text-dorado-400 text-3xl mb-3">1️</div>
                    <h3 class="font-semibold text-lg mb-2">Regístrate</h3>
                    <p class="text-gray-400 text-sm">Con tu DNI y tu número de Yape o Plin. Es rápido y sencillo.</p>
                </div>
                <div class="bg-gray-900 rounded-xl p-6 border border-dorado-700 hover:border-dorado-400 transition-all duration-300 hover:transform hover:scale-105">
                    <div class="text-dorado-400 text-3xl mb-3">2️⃣</div>
                    <h3 class="font-semibold text-lg mb-2">Elige tus números</h3>
                    <p class="text-gray-400 text-sm">Selecciona 6 números del 1 al 60 por solo S/ 3 el boleto.</p>
                </div>
                <div class="bg-gray-900 rounded-xl p-6 border border-dorado-700 hover:border-dorado-400 transition-all duration-300 hover:transform hover:scale-105">
                    <div class="text-dorado-400 text-3xl mb-3">3️</div>
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
            <div class="bg-gradient-to-b from-dorado-500 to-dorado-700 rounded-xl p-6 text-black col-span-2 sm:col-span-1 hover:scale-105 transition-transform duration-300 shadow-lg">
                <p class="text-base sm:text-lg font-semibold">6 aciertos</p>
                <p class="text-4xl sm:text-4xl font-extrabold">S/ 1,000</p>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-dorado-600 hover:border-dorado-400 transition-all duration-300">
                <p class="text-base sm:text-lg font-semibold text-dorado-300">5 aciertos</p>
                <p class="text-3xl sm:text-4xl font-extrabold text-white">S/ 100</p>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-dorado-600 hover:border-dorado-400 transition-all duration-300">
                <p class="text-base sm:text-lg font-semibold text-dorado-300">4 aciertos</p>
                <p class="text-3xl sm:text-4xl font-extrabold text-white">S/ 50</p>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-dorado-600 hover:border-dorado-400 transition-all duration-300">
                <p class="text-base sm:text-lg font-semibold text-dorado-300">3 aciertos</p>
                <p class="text-3xl sm:text-4xl font-extrabold text-white">S/ 10</p>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-dorado-600 hover:border-dorado-400 transition-all duration-300">
                <p class="text-base sm:text-lg font-semibold text-dorado-300">2 aciertos</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-white">Jugada gratis</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black border-t border-dorado-700 py-8 text-center text-gray-500 text-sm">
        <p>&copy; {{ date('Y') }} Gana con El Billetazo. Todos los derechos reservados.</p>
        <p class="mt-2">
            <a href="{{ route('terminos') }}" class="text-dorado-400 hover:text-dorado-300 underline">Términos y Condiciones</a>
        </p>
        <p class="mt-2">
            Desarrollado por
            <a href="https://tushpa.app" target="_blank" rel="noopener" class="text-dorado-400 hover:text-dorado-300 font-semibold underline">TUSHPA</a>
        </p>
    </footer>

</body>
</html>