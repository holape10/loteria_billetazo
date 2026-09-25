<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gana con El Billetazo</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
        <style>
        /* ==========================================
           ANIMACIONES DE ENTRADA (una sola vez, livianas)
           ========================================== */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }

        .animate-on-scroll { opacity: 0; transform: translateY(20px); transition: opacity 0.6s ease-out, transform 0.6s ease-out; }
        .animate-on-scroll.visible { opacity: 1; transform: translateY(0); }

        /* ==========================================
           PREMIO ACUMULADO (versión liviana)
           ========================================== */
        .jackpot-container { position: relative; padding: 20px; }

        .jackpot-badge {
            position: absolute; top: -18px; left: 50%; transform: translateX(-50%);
            z-index: 10;
        }

        .jackpot-text {
            color: #fbbf24;
            font-size: 1.1rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            white-space: nowrap;
        }

        .jackpot-box {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            position: relative;
            box-shadow: 0 0 40px rgba(251, 191, 36, 0.5);
            border: 4px solid #fcd34d;
            overflow: hidden;
            animation: jackpot-pulse 3s ease-in-out infinite;
        }

        @keyframes jackpot-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.015); }
        }

        .alerta-jackpot {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            background: rgba(255, 255, 255, 0.3);
            padding: 8px 16px; border-radius: 10px; margin-bottom: 15px;
        }

        .fire-icon { font-size: 1.4rem; }

        .prize-amount-wrapper { display: flex; align-items: center; justify-content: center; gap: 10px; flex-wrap: wrap; }

        .currency { font-size: 2.2rem; font-weight: 900; color: #000; }

        .prize-text-3d {
            font-size: 2.6rem;
            font-weight: 900;
            color: #000;
            text-shadow: 0 4px 0 #fff, 0 8px 16px rgba(0,0,0,0.35);
            display: inline-block;
        }

        @media (min-width: 640px) {
            .currency { font-size: 3.5rem; }
            .prize-text-3d { font-size: 4rem; }
        }

        .prize-info-box {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            background: rgba(0, 0, 0, 0.1); padding: 10px 20px; border-radius: 10px; margin-top: 1rem;
        }

        .money-bag { font-size: 1.4rem; }

        .growth-indicator { text-align: center; margin-top: 1rem; }
        .growth-bar { width: 100%; height: 6px; background: rgba(0,0,0,0.2); border-radius: 4px; overflow: hidden; }
        .growth-fill { height: 100%; background: #dc2626; border-radius: 4px; width: 75%; }

        /* ==========================================
           CRONÓMETRO
           ========================================== */
        .countdown-item {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            border: 2px solid #fbbf24; border-radius: 12px; padding: 1rem; min-width: 80px;
            box-shadow: 0 0 12px rgba(251, 191, 36, 0.25);
        }

        .countdown-number { font-size: 2.2rem; font-weight: 900; color: #fbbf24; }

        @media (min-width: 640px) { .countdown-number { font-size: 3.2rem; } }

        .countdown-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 2px; color: #9ca3af; margin-top: 0.5rem; }

        /* ==========================================
           HERO
           ========================================== */
        .hero-section { animation: fadeInUp 0.8s ease-out; }
        .hero-title { animation: fadeInDown 0.8s ease-out; }
        .hero-description { animation: fadeInUp 0.8s ease-out 0.15s backwards; }
        .hero-button { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hero-button:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(251, 191, 36, 0.4); }

        /* ==========================================
           NÚMEROS DEL ÚLTIMO SORTEO
           ========================================== */
        .last-draw-section { animation: fadeInUp 0.8s ease-out; }
        .number-ball { display: inline-block; animation: scaleIn 0.4s ease-out backwards; }
        .number-ball:nth-child(1) { animation-delay: 0.05s; }
        .number-ball:nth-child(3) { animation-delay: 0.1s; }
        .number-ball:nth-child(5) { animation-delay: 0.15s; }
        .number-ball:nth-child(7) { animation-delay: 0.2s; }
        .number-ball:nth-child(9) { animation-delay: 0.25s; }
        .number-ball:nth-child(11) { animation-delay: 0.3s; }

        /* ==========================================
           CÓMO FUNCIONA
           ========================================== */
        .step-card { transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease; }
        .step-card:hover { transform: translateY(-6px); box-shadow: 0 15px 30px rgba(251, 191, 36, 0.25); border-color: #fbbf24; }
        .step-icon { font-size: 3rem; display: inline-block; }
        .step-number {
            display: inline-block; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #000; width: 40px; height: 40px; border-radius: 50%;
            text-align: center; line-height: 40px; font-weight: 900; margin-bottom: 1rem;
        }

        /* ==========================================
           PREMIOS
           ========================================== */
        .prize-card { transition: transform 0.3s ease; }
        .prize-card:hover { transform: translateY(-6px); }
        .prize-card.jackpot-prize { border: 3px solid #fbbf24; box-shadow: 0 0 20px rgba(251, 191, 36, 0.4); }
        .prize-amount { font-size: 2.2rem; font-weight: 900; color: #fbbf24; }
        @media (min-width: 640px) { .prize-amount { font-size: 2.8rem; } }

        /* Respeta a quienes prefieren menos movimiento (accesibilidad y batería) */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body class="bg-gray-950 text-white font-sans antialiased">

    <!-- Navbar -->
    <nav class="bg-black border-b border-dorado-600">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <span class="text-dorado-400 font-extrabold text-xl nav-logo">💰 El Billetazo</span>

                <div class="flex items-center gap-4">
                    <a href="{{ route('ganadores.index') }}" class="text-sm text-dorado-300 hover:text-dorado-400 transition-all duration-300 hover:scale-110">🏆 Ganadores</a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm text-dorado-300 hover:text-dorado-400 transition-all duration-300 hover:scale-110">Mi cuenta</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-dorado-300 hover:text-dorado-400 transition-all duration-300 hover:scale-110">Iniciar sesión</a>
                        <a href="{{ route('register') }}" class="text-sm bg-dorado-500 hover:bg-dorado-600 text-black font-semibold px-4 py-2 rounded-full transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-dorado-500/50">Regístrate</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @if ($proximoSorteo)
    <!-- Countdown Section -->
    <section class="bg-black py-10 border-t border-b border-dorado-700 text-center countdown-box">
        <p class="text-dorado-400 font-semibold mb-6 uppercase tracking-wide text-sm animate-pulse">⏰ Próximo sorteo</p>
        <div id="cronometro" class="flex justify-center gap-3 sm:gap-6" data-fecha="{{ $proximoSorteo->fecha->format('Y-m-d') }}T{{ $proximoSorteo->hora }}">
            <div class="countdown-item">
                <div class="countdown-number" id="dias">00</div>
                <div class="countdown-label">Días</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-number" id="horas">00</div>
                <div class="countdown-label">Horas</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-number" id="minutos">00</div>
                <div class="countdown-label">Min</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-number text-red-500" id="segundos">00</div>
                <div class="countdown-label">Seg</div>
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

    <!-- Hero Section -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center hero-section">
        <h1 class="text-4xl sm:text-6xl font-extrabold text-dorado-400 leading-tight hero-title">
            <span class="hero-title-word">Gana</span>
            <span class="hero-title-word text-white">con</span>
            <span class="hero-title-word">El Billetazo</span>
        </h1>
        <p class="mt-6 text-gray-300 text-lg max-w-2xl mx-auto hero-description">
            El sorteo semanal más fácil y confiable del Perú. Elige tus 6 números, paga con Yape o Plin, y gana todos los domingos.
        </p>
        <div class="mt-10 flex justify-center gap-4">
            @guest
                <a href="{{ route('register') }}" class="hero-button bg-dorado-500 hover:bg-dorado-600 text-black font-bold px-8 py-4 rounded-full text-lg inline-block">
                    🎯 Participar ahora
                </a>
            @endguest
        </div>
    </section>

    @if ($proximoSorteo)
    <!-- Jackpot Section -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mb-10 animate-on-scroll">
        <div class="jackpot-container relative">
            <!-- Efecto de explosión exterior -->
            <div class="explosion-ring ring-1"></div>
            <div class="explosion-ring ring-2"></div>
            <div class="explosion-ring ring-3"></div>
            
            <!-- Partículas de fuegos artificiales -->
            <div class="fireworks-container" id="fireworks"></div>
            
            <!-- Badge JACKPOT animado -->
            <div class="jackpot-badge">
                <span class="jackpot-text">🔥 PREMIO ACUMULADO 🔥</span>
            </div>
            
            <!-- Contenedor principal del premio -->
            <div class="jackpot-box relative overflow-hidden">
                <!-- Fondo animado con gradiente -->
                <div class="animated-gradient"></div>
                
                <!-- Efecto de brillo que cruza -->
                <div class="shine-effect"></div>
                
                <!-- Contenido -->
                <div class="jackpot-content relative z-10">
                    @if ($huboGanadorMayorSemanaPasada === false)
                        <div class="alerta-jackpot">
                            <span class="fire-icon">🔥</span>
                            <p class="text-sm sm:text-base font-bold text-red-700 uppercase tracking-wide">
                                ¡Esta semana no hubo ganador! El pozo sigue creciendo
                            </p>
                            <span class="fire-icon">🔥</span>
                        </div>
                    @endif
                    
                    <p class="text-xs uppercase font-bold tracking-widest text-gray-800 mb-2">
                        Pozo Acumulado — Próximo Sorteo
                    </p>
                    
                    <!-- Monto con efecto 3D explosivo -->
                    <div class="prize-amount-container">
                        <div class="prize-amount-wrapper">
                            <span class="currency">S/</span>
                            <span class="prize-text-3d" id="premio-mayor">
                                {{ number_format($proximoSorteo->premio_mayor, 2) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Info adicional -->
                    <div class="prize-info-box mt-4">
                        <span class="money-bag">💰</span>
                        <p class="text-xs sm:text-sm font-bold text-gray-800">
                            Sube S/ 200 cada semana si no hay ganadores con 6 aciertos
                        </p>
                        <span class="money-bag">💰</span>
                    </div>
                    
                    <!-- Contador de crecimiento -->
                    <div class="growth-indicator mt-3">
                        <div class="growth-bar">
                            <div class="growth-fill"></div>
                        </div>
                        <p class="text-xs font-semibold text-red-700 mt-1 animate-pulse">
                            ⚡ ¡CRECIENDO AHORA! 
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--<script>
        
        function createFireworks() {
            const container = document.getElementById('fireworks');
            if (!container) return;
            
            const colors = ['#fbbf24', '#f59e0b', '#dc2626', '#fcd34d', '#ffffff'];
            const fireworkCount = 30;
            
            for (let i = 0; i < fireworkCount; i++) {
                setTimeout(() => {
                    const firework = document.createElement('div');
                    firework.className = 'firework';
                    
                    const centerX = container.offsetWidth / 2;
                    const centerY = container.offsetHeight / 2;
                    
                    const angle = (Math.PI * 2 * Math.random());
                    const velocity = 100 + Math.random() * 150;
                    const tx = Math.cos(angle) * velocity;
                    const ty = Math.sin(angle) * velocity;
                    
                    const color = colors[Math.floor(Math.random() * colors.length)];
                    
                    firework.style.background = color;
                    firework.style.boxShadow = `0 0 10px ${color}, 0 0 20px ${color}`;
                    firework.style.setProperty('--tx', `${tx}px`);
                    firework.style.setProperty('--ty', `${ty}px`);
                    firework.style.left = `${centerX}px`;
                    firework.style.top = `${centerY}px`;
                    firework.style.animationDelay = `${Math.random() * 2}s`;
                    
                    container.appendChild(firework);
                    
                    setTimeout(() => {
                        firework.remove();
                    }, 2000);
                }, i * 100);
            }
            
            setTimeout(createFireworks, 3000);
        }
        
        document.addEventListener('DOMContentLoaded', createFireworks);
    </script>-->
    @endif

    @if ($ultimoSorteoJugado)
    <!-- Last Draw Section -->
    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 -mt-2 mb-4 last-draw-section animate-on-scroll">
        <div class="bg-gradient-to-r from-dorado-600 to-dorado-500 rounded-xl p-5 text-center text-black shadow-lg border-2 border-dorado-300">
            <p class="text-xs uppercase font-semibold tracking-wide mb-3 animate-pulse">
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
                        <span class="text-dorado-900 font-bold self-center text-xl animate-pulse">-</span>
                    @endif
                @endforeach
            </div>
            
        </div>
    </section>

    <!--<script>
        
        document.addEventListener('DOMContentLoaded', function() {
            const numberBalls = document.querySelectorAll('.number-ball');
            
            setInterval(() => {
                numberBalls.forEach((ball, index) => {
                    setTimeout(() => {
                        ball.classList.remove('flip');
                        void ball.offsetWidth;
                        ball.classList.add('flip');
                    }, index * 100);
                });
            }, 3000);
            
            setTimeout(() => {
                numberBalls.forEach((ball, index) => {
                    setTimeout(() => {
                        ball.classList.add('flip');
                    }, index * 100);
                });
            }, 3000);
        });
    </script>-->
    @endif

    <!-- Cómo funciona Section -->
    <section class="bg-black py-16 border-t border-dorado-700 how-it-works-section animate-on-scroll">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold text-center text-dorado-400 mb-12 how-it-works-title">
                ✨ ¿Cómo funciona?
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="step-card bg-gray-900 rounded-xl p-8 border-2 border-dorado-700">
                    <div class="step-number">1</div>
                    <div class="step-icon mb-4"></div>
                    <h3 class="font-bold text-xl mb-3 text-dorado-400">Regístrate</h3>
                    <p class="text-gray-400">Con tu DNI y tu número de Yape o Plin. Es rápido y sencillo.</p>
                </div>
                <div class="step-card bg-gray-900 rounded-xl p-8 border-2 border-dorado-700">
                    <div class="step-number">2</div>
                    <div class="step-icon mb-4"></div>
                    <h3 class="font-bold text-xl mb-3 text-dorado-400">Elige tus números</h3>
                    <p class="text-gray-400">Selecciona 6 números del 1 al 60 por solo S/ 3 el boleto.</p>
                </div>
                <div class="step-card bg-gray-900 rounded-xl p-8 border-2 border-dorado-700">
                    <div class="step-number">3</div>
                    <div class="step-icon mb-4">🏆</div>
                    <h3 class="font-bold text-xl mb-3 text-dorado-400">Espera el sorteo</h3>
                    <p class="text-gray-400">Todos los domingos a las 4:00 p.m. ¡Gana hasta S/ 1,000! + el pozo acumulado</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Premios Section -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 prizes-section animate-on-scroll">
        <h2 class="text-3xl sm:text-4xl font-bold text-center text-dorado-400 mb-12 prizes-title">
            💎 Premios
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 sm:gap-6 text-center">
            <div class="prize-card jackpot-prize bg-gradient-to-b from-dorado-500 to-dorado-700 rounded-xl p-6 text-black col-span-2 sm:col-span-1">
                <p class="text-base sm:text-lg font-bold mb-2">6 aciertos</p>
                <p class="prize-amount">S/ 1,000</p>
            </div>
            <div class="prize-card bg-gray-800 rounded-xl p-6 border-2 border-dorado-600">
                <p class="text-base sm:text-lg font-semibold text-dorado-300 mb-2">5 aciertos</p>
                <p class="text-3xl sm:text-4xl font-extrabold text-white">S/ 100</p>
            </div>
            <div class="prize-card bg-gray-800 rounded-xl p-6 border-2 border-dorado-600">
                <p class="text-base sm:text-lg font-semibold text-dorado-300 mb-2">4 aciertos</p>
                <p class="text-3xl sm:text-4xl font-extrabold text-white">S/ 50</p>
            </div>
            <div class="prize-card bg-gray-800 rounded-xl p-6 border-2 border-dorado-600">
                <p class="text-base sm:text-lg font-semibold text-dorado-300 mb-2">3 aciertos</p>
                <p class="text-3xl sm:text-4xl font-extrabold text-white">S/ 10</p>
            </div>
            <div class="prize-card bg-gray-800 rounded-xl p-6 border-2 border-dorado-600">
                <p class="text-base sm:text-lg font-semibold text-dorado-300 mb-2">2 aciertos</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-white">Jugada gratis</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black border-t border-dorado-700 py-8 text-center text-gray-500 text-sm">
        <p>&copy; {{ date('Y') }} Gana con El Billetazo. Todos los derechos reservados.</p>
        <p class="mt-2">
            <a href="{{ route('terminos') }}" class="text-dorado-400 hover:text-dorado-300 underline transition-all duration-300">Términos y Condiciones</a>
        </p>
        <p class="mt-2">
            Desarrollado por
            <a href="https://tushpa.app" target="_blank" rel="noopener" class="text-dorado-400 hover:text-dorado-300 font-semibold underline transition-all duration-300">TUSHPA</a>
        </p>
    </footer>

    <!-- Script para animaciones al hacer scroll -->
    <script>
        // Intersection Observer para animaciones al hacer scroll
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });
    </script>

</body>
</html>