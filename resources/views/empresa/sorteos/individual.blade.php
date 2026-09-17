<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sorteo individual al azar — {{ $sorteo->fecha->format('d/m/Y') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-black text-white shadow-sm sm:rounded-lg p-6 text-center">

                <div class="mb-4">
                    <p class="text-sm text-gray-400 uppercase tracking-wide">Número extraído</p>
                    <div id="resultado-actual" class="text-6xl font-extrabold text-dorado-400 my-2">?</div>
                </div>

                <div id="lista-extraidos" class="mb-6 min-h-[3rem]"></div>

                <div id="bolas-anfora" class="grid grid-cols-8 sm:grid-cols-10 gap-1 bg-gray-900 border-4 border-dorado-600 rounded-3xl p-4 mb-6 max-h-64 overflow-y-auto">
                    @for ($n = 1; $n <= 60; $n++)
                        <div class="bola w-7 h-7 flex items-center justify-center rounded-full bg-dorado-500 text-black text-xs font-bold" data-numero="{{ $n }}">
                            {{ $n }}
                        </div>
                    @endfor
                </div>

                <button type="button" id="btn-extraer" onclick="iniciarExtraccion()" class="px-6 py-3 bg-dorado-500 hover:bg-dorado-600 text-black font-bold rounded-full">
                    Sacar número 1
                </button>

                <form action="{{ route('sorteos.realizar-manual', $sorteo) }}" method="POST" id="form-confirmar" onsubmit="return confirm('¿Confirmar estos 6 números como ganadores?')">
                    @csrf
                    <div id="numeros-hidden"></div>
                    <button type="submit" id="btn-confirmar" class="hidden px-6 py-3 bg-dorado-500 hover:bg-dorado-600 text-black font-bold rounded-full mt-4">
                        ✅ Confirmar sorteo
                    </button>
                </form>

            </div>
        </div>
    </div>

    <style>
        @keyframes agitar {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(3px, -3px) rotate(8deg); }
            50% { transform: translate(-3px, 3px) rotate(-8deg); }
            75% { transform: translate(3px, 3px) rotate(4deg); }
        }
        #bolas-anfora.agitando .bola:not(.extraida) {
            animation: agitar 0.25s infinite;
        }
        .bola.extraida {
            opacity: 0.15;
            pointer-events: none;
        }
        #resultado-actual.animate-bounce {
            animation: bounce 0.6s;
        }
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.3); }
        }
    </style>

    <script>
        let disponibles = Array.from({ length: 60 }, (_, i) => i + 1);
        let extraidos = [];
        const maxNumeros = 6;

        function iniciarExtraccion() {
            if (extraidos.length >= maxNumeros) return;

            const contenedorBolas = document.getElementById('bolas-anfora');
            contenedorBolas.classList.add('agitando');
            document.getElementById('btn-extraer').disabled = true;
            document.getElementById('resultado-actual').textContent = '?';

            setTimeout(() => {
                const indice = Math.floor(Math.random() * disponibles.length);
                const numeroElegido = disponibles[indice];
                disponibles.splice(indice, 1);
                extraidos.push(numeroElegido);

                contenedorBolas.classList.remove('agitando');

                const bola = document.querySelector(`.bola[data-numero="${numeroElegido}"]`);
                if (bola) bola.classList.add('extraida');

                const resultado = document.getElementById('resultado-actual');
                resultado.textContent = numeroElegido;
                resultado.classList.add('animate-bounce');
                setTimeout(() => resultado.classList.remove('animate-bounce'), 600);

                actualizarListaExtraidos();
                actualizarInputsOcultos();

                if (extraidos.length < maxNumeros) {
                    document.getElementById('btn-extraer').textContent = `Sacar número ${extraidos.length + 1}`;
                    document.getElementById('btn-extraer').disabled = false;
                } else {
                    document.getElementById('btn-extraer').classList.add('hidden');
                    document.getElementById('btn-confirmar').classList.remove('hidden');
                }
            }, 2000);
        }

        function actualizarListaExtraidos() {
            const contenedor = document.getElementById('lista-extraidos');
            contenedor.innerHTML = '';
            extraidos.forEach(n => {
                const span = document.createElement('span');
                span.className = 'inline-flex items-center justify-center w-10 h-10 rounded-full bg-dorado-500 text-black font-bold mx-1';
                span.textContent = n;
                contenedor.appendChild(span);
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
    </script>
</x-app-layout>