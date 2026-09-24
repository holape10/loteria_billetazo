<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight animate-pulse">
            🎰 Sorteo EN VIVO — {{ $sorteo->fecha->format('d/m/Y') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- LAYOUT PRINCIPAL: 2 COLUMNAS -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- COLUMNA IZQUIERDA: Número extraído + En juego + Botón -->
                <div class="lg:col-span-5 space-y-6">
                    
                    <!-- NÚMERO EXTRAÍDO ACTUAL -->
                    <div class="bg-gradient-to-br from-gray-900 to-black border-4 border-dorado-600 rounded-3xl p-8 text-center shadow-2xl relative overflow-hidden">
                        <!-- Efecto de brillo de fondo -->
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(251,191,36,0.1),transparent_70%)]"></div>
                        
                        <div class="relative z-10">
                            <p class="text-dorado-400 text-sm uppercase tracking-[0.3em] mb-6 font-bold flex items-center justify-center gap-2">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                </span>
                                Número Extraído
                            </p>
                            
                            <!-- Contenedor del número gigante -->
                            <div class="relative inline-block">
                                <!-- Glow exterior -->
                                <div id="numero-glow" class="absolute inset-0 bg-dorado-500 rounded-full blur-3xl opacity-0 transition-opacity duration-500"></div>
                                
                                <!-- El número GIGANTE -->
                                <div id="resultado-actual" class="relative text-[10rem] sm:text-[12rem] font-black text-transparent bg-clip-text bg-gradient-to-b from-yellow-300 via-dorado-400 to-yellow-600 drop-shadow-[0_0_40px_rgba(251,191,36,0.8)] transform transition-all duration-300 leading-none">
                                    ?
                                </div>
                                
                                <!-- Partículas explosivas -->
                                <div id="partículas-explosión" class="absolute inset-0 pointer-events-none"></div>
                            </div>
                            
                            <!-- Mensaje de anticipación -->
                            <div id="mensaje-anticipacion" class="hidden mt-6 text-2xl font-bold text-white animate-bounce">
                                🎲 Saliendo... 🎲
                            </div>
                        </div>
                    </div>

                    <!-- NÚMEROS EN JUEGO -->
                    <div id="contenedor-extraidos" class="hidden bg-gradient-to-br from-gray-900 to-black border-4 border-dorado-600 rounded-3xl p-6 shadow-2xl">
                        <p class="text-gray-400 text-sm uppercase tracking-wide mb-4 text-center font-semibold">Números en juego</p>
                        <div id="lista-extraidos" class="flex flex-wrap justify-center gap-3 min-h-[80px]">
                            <!-- Los números aparecerán aquí -->
                        </div>
                    </div>

                    <!-- BOTÓN EXTRAER -->
                    <button type="button" 
                            id="btn-extraer" 
                            onclick="iniciarExtraccion()" 
                            class="group relative w-full px-8 py-5 bg-gradient-to-r from-dorado-500 via-yellow-500 to-dorado-500 hover:from-dorado-400 hover:via-yellow-400 hover:to-dorado-400 text-black font-black text-xl rounded-2xl shadow-2xl transform transition-all duration-300 hover:scale-105 hover:shadow-[0_0_40px_rgba(251,191,36,0.6)] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none overflow-hidden">
                        
                        <!-- Efecto de brillo -->
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-30 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                        
                        <span class="relative flex items-center justify-center gap-3">
                            <span id="btn-icon" class="text-2xl">🎱</span>
                            <span id="btn-text">Sacar número 1</span>
                            <span id="btn-sparkles" class="text-2xl">✨</span>
                        </span>
                    </button>

                    <!-- BOTÓN CONFIRMAR -->
                    <form action="{{ route('sorteos.realizar-manual', $sorteo) }}" method="POST" id="form-confirmar" onsubmit="return confirmarSorteo()">
                        @csrf
                        <div id="numeros-hidden"></div>
                        <button type="submit" 
                                id="btn-confirmar" 
                                class="hidden w-full px-8 py-5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-400 hover:to-emerald-500 text-white font-black text-xl rounded-2xl shadow-2xl transform transition-all duration-300 hover:scale-105 hover:shadow-[0_0_40px_rgba(34,197,94,0.6)] animate-pulse">
                            ✅ CONFIRMAR SORTEO COMPLETO
                        </button>
                    </form>
                </div>

                <!-- COLUMNA DERECHA: Ánfora de bolas + EN VIVO -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <!-- ÁNFORA DE BOLAS -->
                    <div class="bg-gradient-to-br from-gray-900 to-black border-4 border-dorado-600 rounded-3xl p-6 shadow-2xl">
                        <div id="bolas-anfora" class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2 sm:gap-3 max-h-[500px] overflow-y-auto pr-2">
                            @for ($n = 1; $n <= 60; $n++)
                                <div class="bola w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-dorado-400 to-dorado-600 text-black text-sm sm:text-base font-black shadow-lg transform transition-all duration-300 hover:scale-125 cursor-default" 
                                     data-numero="{{ $n }}"
                                     style="animation: float 3s ease-in-out infinite; animation-delay: {{ rand(0, 3000) }}ms">
                                    {{ $n }}
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- EN VIVO - JUGADORES EN CARRERA (debajo de las bolillas) -->
                    <div id="en-vivo" class="hidden bg-gradient-to-br from-gray-900 to-black border-4 border-dorado-600 rounded-3xl p-6 shadow-2xl">
                        <p class="text-dorado-400 font-bold text-lg mb-4 text-center flex items-center justify-center gap-2">
                            <span class="relative flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                            </span>
                             EN VIVO — Jugadores en carrera
                        </p>
                        <div id="en-vivo-lista" class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm max-h-[300px] overflow-y-auto"></div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- CONFETTI CANVAS -->
    <canvas id="confetti-canvas" class="fixed inset-0 pointer-events-none z-50 hidden"></canvas>

    <style>
        /* Animación de flotación para las bolas */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        
        /* Animación de explosión */
        @keyframes explode {
            0% {
                transform: scale(0) rotate(0deg);
                opacity: 1;
            }
            50% {
                opacity: 1;
            }
            100% {
                transform: scale(2) rotate(720deg);
                opacity: 0;
            }
        }
        
        /* Animación de partículas */
        @keyframes particle-fly {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translate(var(--tx), var(--ty)) scale(0);
                opacity: 0;
            }
        }
        
        /* Animación de agitación intensa */
        @keyframes shake-intense {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            10% { transform: translate(-15px, -15px) rotate(-15deg); }
            20% { transform: translate(15px, 15px) rotate(15deg); }
            30% { transform: translate(-15px, 15px) rotate(-15deg); }
            40% { transform: translate(15px, -15px) rotate(15deg); }
            50% { transform: translate(-10px, 10px) rotate(-10deg); }
            60% { transform: translate(10px, -10px) rotate(10deg); }
            70% { transform: translate(-10px, -10px) rotate(-10deg); }
            80% { transform: translate(10px, 10px) rotate(10deg); }
            90% { transform: translate(0, 0) rotate(0deg); }
        }
        
        /* Animación de revelación */
        @keyframes reveal {
            0% {
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }
            50% {
                transform: scale(1.5) rotate(10deg);
            }
            70% {
                transform: scale(0.8) rotate(-5deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }
        
        /* Animación de número extraído */
        @keyframes number-pop {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }
            50% {
                transform: scale(1.3);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        #bolas-anfora.agitando .bola:not(.extraida) {
            animation: shake-intense 0.3s infinite;
        }
        
        .bola.extraida {
            opacity: 0.15;
            transform: scale(0.8);
            filter: grayscale(100%);
            pointer-events: none;
            transition: all 0.5s ease;
        }
        
        .bola.saliente {
            animation: explode 0.6s ease-out;
            background: linear-gradient(135deg, #ff0000, #ff6600);
            color: white;
            transform: scale(2);
            z-index: 50;
            box-shadow: 0 0 30px rgba(255, 100, 0, 0.8);
        }
        
        #resultado-actual.reveal {
            animation: reveal 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        #resultado-actual.pop {
            animation: number-pop 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        .numero-extraido-gigante {
            animation: number-pop 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) backwards;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 0 20px rgba(251, 191, 36, 0.8));
            transform: scale(1);
            transition: transform 0.3s ease;
        }
        
        .numero-extraido-gigante:hover {
            transform: scale(1.2) rotate(5deg);
        }
        
        /* Efecto de pulso para el glow */
        .glow-active {
            animation: glow-pulse 1s ease-out;
        }
        
        @keyframes glow-pulse {
            0% { opacity: 1; transform: scale(1); }
            100% { opacity: 0; transform: scale(2); }
        }
        
        /* Scrollbar personalizada */
        #bolas-anfora::-webkit-scrollbar,
        #en-vivo-lista::-webkit-scrollbar {
            width: 10px;
        }
        
        #bolas-anfora::-webkit-scrollbar-track,
        #en-vivo-lista::-webkit-scrollbar-track {
            background: #1f2937;
            border-radius: 10px;
        }
        
        #bolas-anfora::-webkit-scrollbar-thumb,
        #en-vivo-lista::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            border-radius: 10px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 1024px) {
            #resultado-actual {
                font-size: 8rem;
            }
        }
    </style>

    <script>
        let disponibles = Array.from({ length: 60 }, (_, i) => i + 1);
        let extraidos = [];
        const maxNumeros = 6;

        // Crear partículas de explosión
        function crearExplosion() {
            const contenedor = document.getElementById('partículas-explosión');
            const colores = ['#fbbf24', '#f59e0b', '#fcd34d', '#ffffff', '#dc2626'];
            
            for (let i = 0; i < 30; i++) {
                const partícula = document.createElement('div');
                partícula.className = 'absolute w-3 h-3 rounded-full';
                partícula.style.background = colores[Math.floor(Math.random() * colores.length)];
                partícula.style.left = '50%';
                partícula.style.top = '50%';
                
                const angle = (Math.PI * 2 * i) / 30;
                const velocity = 100 + Math.random() * 150;
                const tx = Math.cos(angle) * velocity;
                const ty = Math.sin(angle) * velocity;
                
                partícula.style.setProperty('--tx', `${tx}px`);
                partícula.style.setProperty('--ty', `${ty}px`);
                partícula.style.animation = `particle-fly 1s ease-out forwards`;
                
                contenedor.appendChild(partícula);
                
                setTimeout(() => partícula.remove(), 1000);
            }
        }

        // Efecto de confetti
        function lanzarConfetti() {
            const canvas = document.getElementById('confetti-canvas');
            canvas.classList.remove('hidden');
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            
            const partículas = [];
            const colores = ['#fbbf24', '#f59e0b', '#fcd34d', '#dc2626', '#ffffff'];
            
            for (let i = 0; i < 150; i++) {
                partículas.push({
                    x: canvas.width / 2,
                    y: canvas.height / 2,
                    vx: (Math.random() - 0.5) * 20,
                    vy: (Math.random() - 0.5) * 20,
                    color: colores[Math.floor(Math.random() * colores.length)],
                    size: Math.random() * 8 + 4,
                    vida: 1
                });
            }
            
            function animar() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                partículas.forEach(p => {
                    p.x += p.vx;
                    p.y += p.vy;
                    p.vy += 0.3;
                    p.vida -= 0.01;
                    
                    ctx.globalAlpha = p.vida;
                    ctx.fillStyle = p.color;
                    ctx.fillRect(p.x, p.y, p.size, p.size);
                });
                
                if (partículas.some(p => p.vida > 0)) {
                    requestAnimationFrame(animar);
                } else {
                    canvas.classList.add('hidden');
                }
            }
            
            animar();
        }

        function iniciarExtraccion() {
            if (extraidos.length >= maxNumeros) return;

            const contenedorBolas = document.getElementById('bolas-anfora');
            const btnExtraer = document.getElementById('btn-extraer');
            const resultado = document.getElementById('resultado-actual');
            const mensaje = document.getElementById('mensaje-anticipacion');
            
            contenedorBolas.classList.add('agitando');
            btnExtraer.disabled = true;
            btnExtraer.innerHTML = '<span class="animate-spin text-2xl">🎲</span><span>Mezclando...</span>';
            
            mensaje.classList.remove('hidden');
            resultado.textContent = '?';
            resultado.classList.remove('reveal', 'pop');
            
            // Countdown visual
            let countdown = 3;
            const countdownInterval = setInterval(() => {
                if (countdown > 0) {
                    resultado.textContent = countdown;
                    resultado.classList.add('pop');
                    setTimeout(() => resultado.classList.remove('pop'), 600);
                    countdown--;
                }
            }, 500);
            
            setTimeout(() => {
                clearInterval(countdownInterval);
                mensaje.classList.add('hidden');
                contenedorBolas.classList.remove('agitando');
                
                const indice = Math.floor(Math.random() * disponibles.length);
                const numeroElegido = disponibles[indice];
                disponibles.splice(indice, 1);
                extraidos.push(numeroElegido);
                
                const bola = document.querySelector(`.bola[data-numero="${numeroElegido}"]`);
                if (bola) {
                    bola.classList.add('saliente');
                    setTimeout(() => bola.classList.add('extraida'), 600);
                }
                
                setTimeout(() => {
                    resultado.textContent = numeroElegido;
                    resultado.classList.add('reveal');
                    
                    const glow = document.getElementById('numero-glow');
                    glow.classList.remove('glow-active');
                    void glow.offsetWidth;
                    glow.classList.add('glow-active');
                    glow.style.opacity = '1';
                    setTimeout(() => glow.style.opacity = '0', 1000);
                    
                    crearExplosion();
                    
                    if (navigator.vibrate) {
                        navigator.vibrate([100, 50, 100]);
                    }
                    
                    if (extraidos.length === 6) {
                        setTimeout(lanzarConfetti, 500);
                    }
                    
                }, 300);
                
                actualizarListaExtraidos();
                actualizarInputsOcultos();
                
                if (extraidos.length >= 3) {
                    verificarEnVivo();
                }
                
                setTimeout(() => {
                    if (extraidos.length < maxNumeros) {
                        btnExtraer.disabled = false;
                        btnExtraer.innerHTML = `
                            <span class="text-2xl">🎱</span>
                            <span>Sacar número ${extraidos.length + 1}</span>
                            <span class="text-2xl">✨</span>
                        `;
                    } else {
                        btnExtraer.classList.add('hidden');
                        document.getElementById('btn-confirmar').classList.remove('hidden');
                    }
                }, 1000);
                
            }, 2500);
        }

        function actualizarListaExtraidos() {
            const contenedor = document.getElementById('contenedor-extraidos');
            const lista = document.getElementById('lista-extraidos');
            
            if (extraidos.length > 0) {
                contenedor.classList.remove('hidden');
            }
            
            lista.innerHTML = '';
            extraidos.forEach((n, index) => {
                const div = document.createElement('div');
                div.className = 'numero-extraido-gigante inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 rounded-2xl font-black text-3xl sm:text-4xl shadow-2xl border-4 border-dorado-400/50';
                div.style.animationDelay = `${index * 100}ms`;
                div.textContent = n;
                div.title = `Número ${index + 1}`;
                
                div.addEventListener('mouseenter', () => {
                    div.style.transform = 'scale(1.2) rotate(5deg)';
                });
                div.addEventListener('mouseleave', () => {
                    div.style.transform = 'scale(1) rotate(0deg)';
                });
                
                lista.appendChild(div);
            });
        }

        function actualizarInputsOcultos() {
            const contenedor = document.getElementById('numeros-hidden');
            contenedor.innerHTML = '';
            extraidos.forEach(n => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'numeros[]';
                input.value = n;
                contenedor.appendChild(input);
            });
        }

        function confirmarSorteo() {
            return confirm('¿CONFIRMAR estos 6 números como resultado oficial del sorteo?');
        }

        function verificarEnVivo() {
            fetch('{{ route('sorteos.verificar-parcial', $sorteo) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ numeros: extraidos }),
            })
                .then(res => res.json())
                .then(data => {
                    const contenedor = document.getElementById('en-vivo');
                    const lista = document.getElementById('en-vivo-lista');
                    lista.innerHTML = '';
                    
                    if (data.en_carrera.length === 0) {
                        lista.innerHTML = '<p class="text-gray-400 col-span-full text-center py-4">🎲 Nadie en carrera por ahora...</p>';
                    } else {
                        data.en_carrera.forEach((item, index) => {
                            const div = document.createElement('div');
                            div.className = 'bg-gray-800/50 border-2 border-dorado-600/30 rounded-xl p-4 flex items-center justify-between';
                            div.style.animation = `fade-in 0.5s ease-out ${index * 100}ms backwards`;
                            
                            const aciertosColor = item.coincidencias >= 3 ? 'text-green-400' : 'text-dorado-400';
                            const emoji = item.coincidencias >= 4 ? '' : (item.coincidencias >= 3 ? '⭐' : '🎯');
                            
                            div.innerHTML = `
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">${emoji}</span>
                                    <span class="font-bold text-white">${item.cliente}</span>
                                </div>
                                <div class="${aciertosColor} font-black text-lg">
                                    ${item.coincidencias}/6 aciertos
                                </div>
                            `;
                            lista.appendChild(div);
                        });
                    }
                    
                    contenedor.classList.remove('hidden');
                })
                .catch(() => {});
        }
    </script>
</x-app-layout>