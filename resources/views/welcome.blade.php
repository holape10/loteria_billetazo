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
           ANIMACIONES GENERALES DE LA PÁGINA
           ========================================== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes glow {
            0%, 100% {
                text-shadow: 0 0 10px rgba(251, 191, 36, 0.5),
                            0 0 20px rgba(251, 191, 36, 0.3),
                            0 0 30px rgba(251, 191, 36, 0.1);
            }
            50% {
                text-shadow: 0 0 20px rgba(251, 191, 36, 0.8),
                            0 0 30px rgba(251, 191, 36, 0.6),
                            0 0 40px rgba(251, 191, 36, 0.4);
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

        @keyframes flip-card {
            0% { transform: rotateY(0deg); }
            100% { transform: rotateY(360deg); }
        }

        /* Clase para animaciones al hacer scroll */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ==========================================
           ANIMACIONES DEL PREMIO MAYOR (EXPLOSIVO)
           ========================================== */
        .jackpot-container {
            position: relative;
            padding: 20px;
        }
        
        /* Anillos de explosión */
        .explosion-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            border: 3px solid #fbbf24;
            animation: explode-ring 3s ease-out infinite;
            pointer-events: none;
        }
        
        .ring-1 { width: 100%; height: 100%; animation-delay: 0s; }
        .ring-2 { width: 100%; height: 100%; animation-delay: 1s; border-color: #f59e0b; }
        .ring-3 { width: 100%; height: 100%; animation-delay: 2s; border-color: #d97706; }
        
        @keyframes explode-ring {
            0% { width: 100%; height: 100%; opacity: 1; border-width: 3px; }
            100% { width: 150%; height: 150%; opacity: 0; border-width: 0px; }
        }
        
        /* Badge JACKPOT */
        .jackpot-badge {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 50;
            animation: badge-bounce 1s ease-in-out infinite;
        }
        
        .jackpot-text {
            background: linear-gradient(90deg, #dc2626, #fbbf24, #dc2626);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.25rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            animation: shine-text 2s linear infinite;
            white-space: nowrap;
            filter: drop-shadow(0 0 10px rgba(251, 191, 36, 0.8));
        }
        
        @keyframes badge-bounce {
            0%, 100% { transform: translateX(-50%) translateY(0) scale(1); }
            50% { transform: translateX(-50%) translateY(-10px) scale(1.05); }
        }
        
        @keyframes shine-text {
            to { background-position: 200% center; }
        }
        
        /* Caja principal del jackpot */
        .jackpot-box {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            position: relative;
            animation: jackpot-pulse 2s ease-in-out infinite;
            box-shadow: 
                0 0 60px rgba(251, 191, 36, 0.8),
                0 0 100px rgba(251, 191, 36, 0.6),
                0 0 140px rgba(251, 191, 36, 0.4),
                inset 0 0 60px rgba(255, 255, 255, 0.3);
            border: 4px solid #fcd34d;
            overflow: hidden;
        }
        
        @keyframes jackpot-pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 60px rgba(251, 191, 36, 0.8), 0 0 100px rgba(251, 191, 36, 0.6), 0 0 140px rgba(251, 191, 36, 0.4), inset 0 0 60px rgba(255, 255, 255, 0.3);
            }
            50% {
                transform: scale(1.03);
                box-shadow: 0 0 80px rgba(251, 191, 36, 1), 0 0 120px rgba(251, 191, 36, 0.8), 0 0 180px rgba(251, 191, 36, 0.6), inset 0 0 80px rgba(255, 255, 255, 0.4);
            }
        }
        
        /* Gradiente animado de fondo */
        .animated-gradient {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(45deg, #fbbf24, #f59e0b, #d97706, #fbbf24, #f59e0b);
            background-size: 400% 400%;
            animation: gradient-shift 5s ease infinite;
            opacity: 0.5;
            z-index: 0;
        }
        
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Efecto de brillo que cruza */
        .shine-effect {
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
            animation: shine-sweep 3s ease-in-out infinite;
            z-index: 1;
        }
        
        @keyframes shine-sweep {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }
        
        /* Alerta de jackpot */
        .alerta-jackpot {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 10px;
            margin-bottom: 15px;
            backdrop-filter: blur(10px);
        }
        
        .fire-icon {
            font-size: 1.5rem;
            animation: fire-flicker 0.5s ease-in-out infinite;
        }
        
        @keyframes fire-flicker {
            0%, 100% { transform: scale(1); filter: brightness(1); }
            50% { transform: scale(1.2); filter: brightness(1.3); }
        }
        
        /* Monto del premio con efecto 3D */
        .prize-amount-container {
            position: relative;
            z-index: 10;
        }
        
        .prize-amount-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .currency {
            font-size: 2.5rem;
            font-weight: 900;
            color: #000;
            animation: currency-bounce 2s ease-in-out infinite;
        }
        
        @media (min-width: 640px) {
            .currency { font-size: 4rem; }
            .prize-text-3d { font-size: 4.5rem; }
        }
        
        @keyframes currency-bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        
        .prize-text-3d {
            font-size: 3rem;
            font-weight: 900;
            color: #000;
            text-shadow: 
                0 1px 0 #fff, 0 2px 0 #fff, 0 3px 0 #fff, 0 4px 0 #fff,
                0 5px 0 #fff, 0 6px 0 #fff, 0 7px 0 #fff, 0 8px 0 #fff,
                0 10px 20px rgba(0, 0, 0, 0.4), 0 15px 30px rgba(0, 0, 0, 0.3),
                0 0 40px rgba(251, 191, 36, 0.8);
            animation: amount-pulse 1.5s ease-in-out infinite;
            display: inline-block;
        }
        
        @keyframes amount-pulse {
            0%, 100% { 
                transform: scale(1);
                text-shadow: 0 1px 0 #fff, 0 2px 0 #fff, 0 3px 0 #fff, 0 4px 0 #fff, 0 5px 0 #fff, 0 6px 0 #fff, 0 7px 0 #fff, 0 8px 0 #fff, 0 10px 20px rgba(0,0,0,0.4), 0 15px 30px rgba(0,0,0,0.3), 0 0 40px rgba(251, 191, 36, 0.8);
            }
            50% { 
                transform: scale(1.05);
                text-shadow: 0 1px 0 #fff, 0 2px 0 #fff, 0 3px 0 #fff, 0 4px 0 #fff, 0 5px 0 #fff, 0 6px 0 #fff, 0 7px 0 #fff, 0 8px 0 #fff, 0 10px 20px rgba(0,0,0,0.5), 0 15px 30px rgba(0,0,0,0.4), 0 0 60px rgba(251, 191, 36, 1), 0 0 80px rgba(251, 191, 36, 0.8);
            }
        }
        
        /* Caja de información */
        .prize-info-box {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: rgba(0, 0, 0, 0.1);
            padding: 10px 20px;
            border-radius: 10px;
            backdrop-filter: blur(5px);
            margin-top: 1rem;
        }
        
        .money-bag {
            font-size: 1.5rem;
            animation: money-shake 2s ease-in-out infinite;
        }
        
        @keyframes money-shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }
        
        /* Indicador de crecimiento */
        .growth-indicator {
            text-align: center;
            margin-top: 1rem;
        }
        
        .growth-bar {
            width: 100%;
            height: 8px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }
        
        .growth-fill {
            height: 100%;
            background: linear-gradient(90deg, #dc2626, #fbbf24, #dc2626);
            background-size: 200% 100%;
            border-radius: 4px;
            animation: growth-fill 3s ease-in-out infinite, growth-shine 2s linear infinite;
            width: 100%;
        }
        
        @keyframes growth-fill {
            0%, 100% { width: 60%; }
            50% { width: 100%; }
        }
        
        @keyframes growth-shine {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Fuegos artificiales */
        .fireworks-container {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            pointer-events: none;
            overflow: visible;
            z-index: 50;
        }
        
        .firework {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            animation: firework-explode 2s ease-out infinite;
        }
        
        @keyframes firework-explode {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(var(--tx), var(--ty)) scale(0); opacity: 0; }
        }

        /* ==========================================
           ANIMACIONES DEL CRONÓMETRO
           ========================================== */
        .countdown-box {
            animation: fadeInDown 1s ease-out;
        }

        .countdown-item {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            border: 2px solid #fbbf24;
            border-radius: 12px;
            padding: 1rem;
            min-width: 80px;
            transition: all 0.3s ease;
            animation: scaleIn 0.8s ease-out backwards;
            box-shadow: 0 0 20px rgba(251, 191, 36, 0.3);
        }

        .countdown-item:nth-child(1) { animation-delay: 0.1s; }
        .countdown-item:nth-child(2) { animation-delay: 0.2s; }
        .countdown-item:nth-child(3) { animation-delay: 0.3s; }
        .countdown-item:nth-child(4) { animation-delay: 0.4s; }

        .countdown-item:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 10px 30px rgba(251, 191, 36, 0.6);
            border-color: #fcd34d;
        }

        .countdown-number {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: glow 2s ease-in-out infinite;
        }

        @media (min-width: 640px) {
            .countdown-number {
                font-size: 3.5rem;
            }
        }

        .countdown-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #9ca3af;
            margin-top: 0.5rem;
        }

        /* ==========================================
           ANIMACIONES DEL HERO
           ========================================== */
        .hero-section {
            animation: fadeInUp 1s ease-out;
        }

        .hero-title {
            animation: fadeInDown 1s ease-out 0.3s backwards;
        }

        .hero-title-word {
            display: inline-block;
            animation: float 3s ease-in-out infinite;
        }

        .hero-title-word:nth-child(1) { animation-delay: 0s; }
        .hero-title-word:nth-child(2) { animation-delay: 0.5s; }
        .hero-title-word:nth-child(3) { animation-delay: 1s; }

        .hero-description {
            animation: fadeInUp 1s ease-out 0.6s backwards;
        }

        .hero-button {
            animation: scaleIn 1s ease-out 0.9s backwards;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .hero-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .hero-button:hover::before {
            width: 300px;
            height: 300px;
        }

        .hero-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(251, 191, 36, 0.5);
        }

        /* ==========================================
           ANIMACIONES DE LOS NÚMEROS DEL SORTEO
           ========================================== */
        .last-draw-section {
            animation: fadeInUp 1s ease-out;
        }

        @keyframes flip-number {
            0% { transform: rotateX(0deg); }
            50% { transform: rotateX(90deg); }
            100% { transform: rotateX(0deg); }
        }

        .number-ball {
            display: inline-block;
            transform-style: preserve-3d;
            animation: scaleIn 0.5s ease-out backwards;
            position: relative;
            overflow: hidden;
        }

        .number-ball::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: rotate(45deg);
            animation: ball-shine 3s ease-in-out infinite;
        }

        @keyframes ball-shine {
            0%, 100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .number-ball:nth-child(1) { animation-delay: 0.1s; }
        .number-ball:nth-child(3) { animation-delay: 0.2s; }
        .number-ball:nth-child(5) { animation-delay: 0.3s; }
        .number-ball:nth-child(7) { animation-delay: 0.4s; }
        .number-ball:nth-child(9) { animation-delay: 0.5s; }
        .number-ball:nth-child(11) { animation-delay: 0.6s; }

        .number-ball.flip {
            animation: flip-number 0.6s ease-in-out;
        }

        .number-ball:hover {
            transform: scale(1.2) rotateY(180deg);
            transition: all 0.5s ease;
        }

        /* ==========================================
           ANIMACIONES DE CÓMO FUNCIONA
           ========================================== */
        .how-it-works-section {
            animation: fadeInUp 1s ease-out;
        }

        .how-it-works-title {
            animation: fadeInDown 1s ease-out;
        }

        .step-card {
            animation: slideInLeft 0.8s ease-out backwards;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .step-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(251, 191, 36, 0.1), transparent);
            transition: left 0.5s;
        }

        .step-card:hover::before {
            left: 100%;
        }

        .step-card:nth-child(1) { animation-delay: 0.1s; }
        .step-card:nth-child(2) { animation-delay: 0.3s; }
        .step-card:nth-child(3) { animation-delay: 0.5s; }

        .step-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(251, 191, 36, 0.4);
            border-color: #fbbf24;
        }

        .step-icon {
            font-size: 3rem;
            animation: float 3s ease-in-out infinite;
            display: inline-block;
        }

        .step-card:nth-child(1) .step-icon { animation-delay: 0s; }
        .step-card:nth-child(2) .step-icon { animation-delay: 1s; }
        .step-card:nth-child(3) .step-icon { animation-delay: 2s; }

        .step-number {
            display: inline-block;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #000;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            font-weight: 900;
            margin-bottom: 1rem;
            animation: scaleIn 0.5s ease-out backwards;
        }

        /* ==========================================
           ANIMACIONES DE PREMIOS
           ========================================== */
        .prizes-section {
            animation: fadeInUp 1s ease-out;
        }

        .prizes-title {
            animation: fadeInDown 1s ease-out;
        }

        .prize-card {
            animation: scaleIn 0.6s ease-out backwards;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .prize-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(251, 191, 36, 0.2) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .prize-card:hover::after {
            opacity: 1;
        }

        .prize-card:nth-child(1) { animation-delay: 0.1s; }
        .prize-card:nth-child(2) { animation-delay: 0.2s; }
        .prize-card:nth-child(3) { animation-delay: 0.3s; }
        .prize-card:nth-child(4) { animation-delay: 0.4s; }
        .prize-card:nth-child(5) { animation-delay: 0.5s; }

        .prize-card:hover {
            transform: translateY(-10px) scale(1.05);
        }

        .prize-card.jackpot-prize {
            animation: jackpot-prize-pulse 2s ease-in-out infinite, scaleIn 0.6s ease-out backwards;
            border: 3px solid #fbbf24;
            box-shadow: 0 0 30px rgba(251, 191, 36, 0.6);
        }

        @keyframes jackpot-prize-pulse {
            0%, 100% {
                box-shadow: 0 0 30px rgba(251, 191, 36, 0.6);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 50px rgba(251, 191, 36, 0.9);
                transform: scale(1.02);
            }
        }

        .prize-amount {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: glow 2s ease-in-out infinite;
        }

        @media (min-width: 640px) {
            .prize-amount {
                font-size: 3rem;
            }
        }

        /* ==========================================
           ANIMACIONES DEL FOOTER
           ========================================== */
        footer {
            animation: fadeInUp 1s ease-out;
        }

        /* Navbar animations */
        nav {
            animation: fadeInDown 0.8s ease-out;
        }

        .nav-logo {
            animation: float 3s ease-in-out infinite;
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

    <script>
        // Crear fuegos artificiales
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
    </script>
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
            
            <p class="text-xs mt-4 text-dorado-900 font-semibold animate-pulse">
                 Los números giran cada 3 segundos
            </p>
        </div>
    </section>

    <script>
        // Animación de rotación para los números del sorteo
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
    </script>
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
                    <p class="text-gray-400">Todos los domingos a las 4:00 p.m. ¡Gana hasta S/ 1,000!</p>
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