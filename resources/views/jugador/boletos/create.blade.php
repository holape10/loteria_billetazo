<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight animate-pulse"> Elige tus números de la suerte</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <!-- POZO ACUMULADO - Versión sutil pero atractiva -->
            @if(isset($sorteo))
            <div class="mb-6 animate-on-scroll">
                <div class="relative overflow-hidden bg-gradient-to-r from-yellow-600 via-dorado-500 to-yellow-600 rounded-2xl p-6 shadow-2xl border-4 border-dorado-300">
                    <div class="absolute inset-0 opacity-30">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.3),transparent_50%)] animate-pulse"></div>
                    </div>
                    <div class="relative z-10 text-center">
                        <p class="text-black font-bold text-sm uppercase tracking-widest mb-2">💰 Pozo Acumulado - Próximo Sorteo</p>
                        <p class="text-4xl sm:text-5xl font-black text-black drop-shadow-lg">
                            S/ {{ number_format($sorteo->premio_mayor, 2) }}
                        </p>
                        <p class="text-black text-sm mt-2 font-semibold opacity-80">
                            📅 {{ $sorteo->fecha->format('d/m/Y') }} - {{ $sorteo->hora }}
                        </p>
                    </div>
                    <!-- Efecto de brillo -->
                    <div class="absolute top-0 -left-full w-1/2 h-full bg-gradient-to-r from-transparent via-white to-transparent opacity-20 animate-shine"></div>
                </div>
            </div>
            @endif

            <div class="bg-gray-950 text-white rounded-2xl shadow-2xl p-4 sm:p-8 border border-dorado-800 animate-on-scroll">

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-900/30 border border-red-500 text-red-300 rounded-xl text-sm animate-bounce">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xl">⚠️</span>
                            <span class="font-semibold">Por favor corrige los siguientes errores:</span>
                        </div>
                        <ul class="list-disc list-inside ml-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($creditosDisponibles > 0)
                    <div class="bg-gradient-to-r from-green-600 to-emerald-500 text-white rounded-xl p-5 mb-6 text-center font-semibold shadow-lg border-2 border-green-400 animate-fade-in-up">
                        <div class="flex items-center justify-center gap-2 flex-wrap">
                            <span class="text-2xl">🎁</span>
                            <span>¡Tienes {{ $creditosDisponibles }} jugada(s) gratis disponible(s)!</span>
                            <span class="text-2xl">🎁</span>
                        </div>
                        <p class="text-sm mt-2 opacity-90">Marca la casilla en la jugada que quieras canjear.</p>
                    </div>
                @endif

                <!-- Tarjeta de pago con QR - Mejorada -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border-2 border-dorado-600 rounded-2xl p-6 mb-8 flex flex-col sm:flex-row items-center gap-6 text-center sm:text-left shadow-xl hover:shadow-2xl transition-all duration-300 hover:border-dorado-400 group animate-on-scroll">
                    <div class="relative">
                        <div class="absolute inset-0 bg-dorado-500 rounded-lg blur-lg opacity-50 group-hover:opacity-75 transition-opacity duration-300"></div>
                        <img src="{{ asset('images/qr-billetazo.png') }}" alt="QR de pago Yape/Plin"
                             class="relative w-40 h-40 sm:w-48 sm:h-48 rounded-lg border-4 border-dorado-500 bg-white p-2 shrink-0 transform group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute -top-2 -right-2 bg-dorado-500 text-black rounded-full p-2 animate-bounce">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-center sm:justify-start gap-2 mb-2">
                            <span class="text-2xl">💰</span>
                            <p class="text-dorado-400 font-extrabold text-xl sm:text-2xl">Paga aquí con Yape o Plin</p>
                        </div>
                        <p class="text-gray-300 text-sm mt-2">Escanea el código QR o transfiere al número:</p>
                        <div class="mt-3 inline-block bg-gray-800 rounded-xl px-6 py-3 border-2 border-dorado-500">
                            <p class="text-3xl sm:text-4xl font-black text-white tracking-wider">964 382 212</p>
                        </div>
                        <p class="text-gray-400 text-sm mt-3 flex items-center justify-center sm:justify-start gap-2">
                            <span>👤</span>
                            <span>A nombre de: <span class="text-dorado-300 font-semibold">Gabriel Jesus Rios Rojas</span></span>
                        </p>
                    </div>
                </div>

                <form action="{{ route('boletos.store', $sorteo) }}" method="POST" enctype="multipart/form-data" id="form-boleto" class="animate-on-scroll">
                    @csrf

                    <div id="contenedor-jugadas" class="space-y-6"></div>

                    <button type="button" id="btn-agregar-jugada" class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-gray-800 to-gray-700 hover:from-dorado-600 hover:to-dorado-500 border-2 border-dorado-600 hover:border-dorado-400 rounded-xl mb-6 font-bold text-sm sm:text-base transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center justify-center gap-2 group">
                        <span class="text-xl group-hover:rotate-12 transition-transform">+</span>
                        <span>Agregar otra jugada</span>
                    </button>

                    <!-- Barra de total - Mejorada -->
                    <div class="bg-gradient-to-r from-dorado-500 via-yellow-500 to-dorado-500 text-black rounded-2xl p-6 mb-8 flex justify-between items-center shadow-xl border-4 border-dorado-300 animate-pulse-slow">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">🎫</span>
                            <div>
                                <span class="font-bold text-sm sm:text-base block">Total de jugadas:</span>
                                <span class="text-2xl font-black" id="total-jugadas">1</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-semibold block opacity-80">Monto total:</span>
                            <span class="text-3xl sm:text-4xl font-black">S/ <span id="total-monto">3.00</span></span>
                        </div>
                    </div>

                    <!-- Sección de pago -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border-2 border-gray-700 rounded-2xl p-6 sm:p-8 space-y-6 animate-on-scroll">
                        <h3 class="text-xl font-bold text-dorado-400 mb-4 flex items-center gap-2">
                            <span>💳</span>
                            <span>Información de pago</span>
                        </h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-300 mb-2 group-focus-within:text-dorado-400 transition-colors">
                                    Método de pago <span class="text-dorado-500">*</span>
                                </label>
                                <select name="metodo_pago" class="w-full bg-gray-800 border-2 border-gray-700 focus:border-dorado-500 text-white rounded-xl px-4 py-3 transition-all duration-300 outline-none focus:ring-4 focus:ring-dorado-500/20">
                                    <option value="yape">💜 Yape - 964382212</option>
                                    <option value="plin">💚 Plin - 964382212</option>
                                </select>
                            </div>

                            <div class="group">
                                <label class="block text-sm font-medium text-gray-300 mb-2 group-focus-within:text-dorado-400 transition-colors">
                                    Número de operación <span class="text-gray-500">(opcional)</span>
                                </label>
                                <input type="text" name="numero_operacion" placeholder="Ej: 123456789" 
                                       class="w-full bg-gray-800 border-2 border-gray-700 focus:border-dorado-500 text-white rounded-xl px-4 py-3 transition-all duration-300 outline-none focus:ring-4 focus:ring-dorado-500/20 placeholder-gray-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Foto del comprobante de pago <span class="text-gray-500">(opcional pero recomendado)</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="comprobante" accept="image/*" 
                                       class="w-full text-sm text-gray-300 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:bg-gradient-to-r file:from-dorado-500 file:to-dorado-600 file:text-black file:font-bold file:cursor-pointer hover:file:from-dorado-600 hover:file:to-dorado-700 transition-all duration-300 cursor-pointer">
                            </div>
                            <p class="text-xs text-gray-500 mt-2">📸 Sube una captura de tu transferencia para validar más rápido</p>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-8 px-6 py-4 bg-gradient-to-r from-dorado-500 via-yellow-500 to-dorado-500 hover:from-dorado-600 hover:via-yellow-600 hover:to-dorado-600 text-black font-black rounded-2xl text-lg sm:text-xl shadow-2xl hover:shadow-dorado-500/50 transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-3 group">
                        <span>✅</span>
                        <span>Confirmar compra</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </button>
                </form>

            </div>
        </div>
    </div>

    <template id="plantilla-jugada">
        <div class="jugada-block bg-gradient-to-br from-gray-900 to-gray-800 border-2 border-gray-700 rounded-2xl p-6 mb-6 shadow-xl hover:border-dorado-600 transition-all duration-300 animate-fade-in">
            <div class="flex justify-between items-center mb-4 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-r from-dorado-500 to-yellow-500 text-black rounded-full w-10 h-10 flex items-center justify-center font-black text-lg shadow-lg">
                        <span class="numero-jugada"></span>
                    </div>
                    <p class="font-bold text-lg text-dorado-400">Jugada</p>
                </div>
                <button type="button" class="btn-quitar-jugada text-red-400 hover:text-red-300 hover:bg-red-900/30 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-300 hidden flex items-center gap-1">
                    <span>✕</span>
                    <span>Quuitar</span>
                </button>
            </div>
            
            <!-- Grid de números mejorado -->
            <div class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 gap-2 mb-4 grid-numeros">
                @for ($n = 1; $n <= 60; $n++)
                    <button type="button" 
                            class="numero-btn border-2 border-gray-700 bg-gray-800 text-gray-300 rounded-lg py-2.5 text-sm font-bold hover:border-dorado-400 hover:text-dorado-400 hover:scale-110 hover:shadow-lg hover:shadow-dorado-500/30 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-dorado-500" 
                            data-numero="{{ $n }}">
                        {{ $n }}
                    </button>
                @endfor
            </div>
            
            <div class="numeros-hidden"></div>
            <div class="gratis-hidden"></div>
            
            <div class="flex justify-between items-center flex-wrap gap-3 pt-4 border-t-2 border-gray-700">
                <div class="flex items-center gap-2 bg-gray-800 rounded-lg px-4 py-2">
                    <span class="text-gray-400 text-sm">Seleccionados:</span>
                    <span class="contador font-bold text-dorado-400 text-lg">0</span>
                    <span class="text-gray-500 text-sm">/ 6</span>
                </div>
                
                <div class="flex items-center gap-3 flex-wrap">
                    <label class="checkbox-gratis-wrapper hidden flex items-center gap-2 bg-gradient-to-r from-green-900/50 to-emerald-900/50 border-2 border-green-600 rounded-lg px-4 py-2 cursor-pointer hover:border-green-400 transition-all duration-300">
                        <input type="checkbox" class="checkbox-gratis w-5 h-5 rounded accent-green-500">
                        <span class="text-sm text-green-300 font-semibold flex items-center gap-1">
                            <span>🎁</span>
                            <span>Usar jugada gratis</span>
                        </span>
                    </label>
                    
                    <button type="button" class="btn-azar px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white rounded-lg text-sm font-bold transition-all duration-300 hover:scale-105 hover:shadow-lg flex items-center gap-2">
                        <span>🎲</span>
                        <span>Al azar</span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <style>
        /* Animaciones personalizadas */
        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }
        
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fade-in {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes pulse-slow {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.02);
            }
        }
        
        .animate-shine {
            animation: shine 3s infinite;
        }
        
        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out;
        }
        
        .animate-fade-in {
            animation: fade-in 0.5s ease-out;
        }
        
        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }
        
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        
        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Efecto para números seleccionados */
        .numero-btn.bg-dorado-500 {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #000;
            border-color: #fbbf24;
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.5);
            transform: scale(1.15);
            font-weight: 900;
        }
        
        /* Scrollbar personalizada */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #111827;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
    </style>

    <script>
        const creditosDisponibles = {{ $creditosDisponibles }};
        let totalJugadas = 0;
        let jugadaCounter = 0;

        // Intersection Observer para animaciones al scroll
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });

        function crearJugada() {
            const plantilla = document.getElementById('plantilla-jugada').content.cloneNode(true);
            jugadaCounter++;
            totalJugadas++;

            const bloque = plantilla.querySelector('.jugada-block');
            bloque.dataset.indice = jugadaCounter - 1;
            plantilla.querySelector('.numero-jugada').textContent = totalJugadas;

            if (totalJugadas > 1) {
                plantilla.querySelector('.btn-quitar-jugada').classList.remove('hidden');
            }

            document.getElementById('contenedor-jugadas').appendChild(plantilla);
            actualizarTotales();
            
            // Animación de entrada
            setTimeout(() => {
                bloque.style.opacity = '1';
                bloque.style.transform = 'translateY(0)';
            }, 10);
        }

        function actualizarTotales() {
            const bloques = document.querySelectorAll('.jugada-block');
            const marcadosGratis = document.querySelectorAll('.checkbox-gratis:checked').length;

            document.getElementById('total-jugadas').textContent = bloques.length;
            document.getElementById('total-monto').textContent = ((bloques.length - marcadosGratis) * 3).toFixed(2);

            document.querySelectorAll('.checkbox-gratis-wrapper').forEach(wrapper => {
                const checkbox = wrapper.querySelector('.checkbox-gratis');

                if (creditosDisponibles <= 0) {
                    wrapper.classList.add('hidden');
                    return;
                }

                wrapper.classList.remove('hidden');
                checkbox.disabled = !checkbox.checked && marcadosGratis >= creditosDisponibles;
                
                if (checkbox.disabled) {
                    wrapper.style.opacity = '0.5';
                    wrapper.style.cursor = 'not-allowed';
                } else {
                    wrapper.style.opacity = '1';
                    wrapper.style.cursor = 'pointer';
                }
            });
        }

        function actualizarGratisHidden(bloque) {
            const checkbox = bloque.querySelector('.checkbox-gratis');
            const contenedor = bloque.querySelector('.gratis-hidden');
            contenedor.innerHTML = '';

            if (checkbox.checked) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `gratis[${bloque.dataset.indice}]`;
                input.value = '1';
                contenedor.appendChild(input);
            }
        }

        function seleccionarBoton(btn) {
            btn.classList.remove('bg-gray-800', 'text-gray-300', 'border-gray-700');
            btn.classList.add('bg-dorado-500', 'text-black', 'border-dorado-500', 'shadow-lg');
            
            // Efecto de sonido visual (pequeña animación)
            btn.style.transform = 'scale(0.9)';
            setTimeout(() => {
                btn.style.transform = 'scale(1.15)';
            }, 50);
        }

        function deseleccionarBoton(btn) {
            btn.classList.remove('bg-dorado-500', 'text-black', 'border-dorado-500', 'shadow-lg', 'scale-110');
            btn.classList.add('bg-gray-800', 'text-gray-300', 'border-gray-700');
            btn.style.transform = 'scale(1)';
        }

        function actualizarInputsOcultos(bloque) {
            const seleccionados = Array.from(bloque.querySelectorAll('.numero-btn.bg-dorado-500'))
                .map(b => b.dataset.numero);

            const indice = bloque.dataset.indice;
            const contenedor = bloque.querySelector('.numeros-hidden');
            contenedor.innerHTML = '';

            seleccionados.forEach(n => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `jugadas[${indice}][]`;
                input.value = n;
                contenedor.appendChild(input);
            });

            const contador = bloque.querySelector('.contador');
            contador.textContent = seleccionados.length;
            
            // Animación del contador
            contador.style.transform = 'scale(1.3)';
            setTimeout(() => {
                contador.style.transform = 'scale(1)';
            }, 200);
        }

        document.getElementById('contenedor-jugadas').addEventListener('click', function (e) {
            const bloque = e.target.closest('.jugada-block');
            if (!bloque) return;

            if (e.target.classList.contains('numero-btn')) {
                const seleccionados = bloque.querySelectorAll('.numero-btn.bg-dorado-500').length;

                if (e.target.classList.contains('bg-dorado-500')) {
                    deseleccionarBoton(e.target);
                } else {
                    if (seleccionados >= 6) {
                        // Animación de error - shake
                        e.target.style.animation = 'shake 0.5s';
                        setTimeout(() => {
                            e.target.style.animation = '';
                        }, 500);
                        return;
                    }
                    seleccionarBoton(e.target);
                }

                actualizarInputsOcultos(bloque);
            }

            if (e.target.classList.contains('btn-azar') || e.target.closest('.btn-azar')) {
                const btn = e.target.classList.contains('btn-azar') ? e.target : e.target.closest('.btn-azar');
                const bloque = btn.closest('.jugada-block');
                
                // Animación de loading
                btn.innerHTML = '<span class="animate-spin">🎲</span><span>Mezclando...</span>';
                btn.disabled = true;
                
                bloque.querySelectorAll('.numero-btn').forEach(b => deseleccionarBoton(b));

                const todos = Array.from({ length: 60 }, (_, i) => i + 1);
                for (let i = todos.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [todos[i], todos[j]] = [todos[j], todos[i]];
                }
                const elegidos = todos.slice(0, 6).sort((a, b) => a - b);

                elegidos.forEach((n, index) => {
                    setTimeout(() => {
                        const boton = bloque.querySelector(`.numero-btn[data-numero="${n}"]`);
                        seleccionarBoton(boton);
                        actualizarInputsOcultos(bloque);
                    }, index * 100);
                });

                setTimeout(() => {
                    btn.innerHTML = '<span></span><span>Al azar</span>';
                    btn.disabled = false;
                }, 800);
            }

            if (e.target.classList.contains('btn-quitar-jugada') || e.target.closest('.btn-quitar-jugada')) {
                const btn = e.target.classList.contains('btn-quitar-jugada') ? e.target : e.target.closest('.btn-quitar-jugada');
                const bloque = btn.closest('.jugada-block');
                
                // Animación de salida
                bloque.style.transition = 'all 0.3s ease';
                bloque.style.opacity = '0';
                bloque.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    bloque.remove();
                    actualizarTotales();
                }, 300);
            }
        });

        document.getElementById('contenedor-jugadas').addEventListener('change', function (e) {
            if (e.target.classList.contains('checkbox-gratis')) {
                const bloque = e.target.closest('.jugada-block');
                actualizarGratisHidden(bloque);
                actualizarTotales();
            }
        });

        document.getElementById('btn-agregar-jugada').addEventListener('click', function() {
            // Animación del botón
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
            
            crearJugada();
            
            // Scroll suave a la nueva jugada
            setTimeout(() => {
                const ultimaJugada = document.querySelector('.jugada-block:last-child');
                if (ultimaJugada) {
                    ultimaJugada.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 100);
        });

        document.getElementById('form-boleto').addEventListener('submit', function (e) {
            const bloques = document.querySelectorAll('.jugada-block');
            for (const bloque of bloques) {
                const seleccionados = bloque.querySelectorAll('.numero-btn.bg-dorado-500').length;
                if (seleccionados !== 6) {
                    e.preventDefault();
                    
                    // Animación de error en la jugada
                    bloque.style.animation = 'shake 0.5s';
                    bloque.style.borderColor = '#ef4444';
                    
                    setTimeout(() => {
                        bloque.style.animation = '';
                        bloque.style.borderColor = '';
                    }, 500);
                    
                    alert('⚠️ Cada jugada debe tener exactamente 6 números seleccionados.');
                    return;
                }
            }
            
            // Animación de loading en el botón de submit
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<span class="animate-spin"></span><span>Procesando...</span>';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
        });

        // Agregar animación shake al CSS dinámicamente
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-10px); }
                75% { transform: translateX(10px); }
            }
        `;
        document.head.appendChild(style);

        // Crear primera jugada
        crearJugada();
    </script>
</x-app-layout>