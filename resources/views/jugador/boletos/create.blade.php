<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Elige tus números</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-950 text-white rounded-2xl shadow-lg p-4 sm:p-8">

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Tarjeta de pago con QR -->
                <div class="bg-gray-900 border border-dorado-600 rounded-xl p-4 sm:p-6 mb-6 flex flex-col sm:flex-row items-center gap-5 sm:gap-6 text-center sm:text-left">
                    <img src="{{ asset('images/qr-billetazo.png') }}" alt="QR de pago Yape/Plin"
                         class="w-36 h-36 sm:w-44 sm:h-44 rounded-lg border-4 border-dorado-500 bg-white p-1 shrink-0">
                    <div>
                        <p class="text-dorado-400 font-extrabold text-lg">💰 Paga aquí con Yape o Plin</p>
                        <p class="text-gray-300 text-sm mt-1">Escanea el código QR o transfiere al número:</p>
                        <p class="text-2xl sm:text-3xl font-extrabold text-white mt-1">964 382 212</p>
                        <p class="text-gray-400 text-sm mt-1">A nombre de: Gabriel Jesus Rios Rojas</p>
                    </div>
                </div>

                <form action="{{ route('boletos.store', $sorteo) }}" method="POST" enctype="multipart/form-data" id="form-boleto">
                    @csrf

                    <div id="contenedor-jugadas"></div>

                    <button type="button" id="btn-agregar-jugada" class="w-full sm:w-auto px-4 py-2 bg-gray-800 border border-dorado-600 hover:bg-gray-700 rounded-lg mb-6 font-semibold text-sm">
                        + Agregar otra jugada
                    </button>

                    <!-- Barra de total -->
                    <div class="bg-dorado-500 text-black rounded-xl p-4 mb-6 flex justify-between items-center">
                        <span class="font-semibold text-sm sm:text-base">
                            <span id="total-jugadas">1</span> jugada(s)
                        </span>
                        <span class="text-xl sm:text-2xl font-extrabold">
                            S/ <span id="total-monto">1.00</span>
                        </span>
                    </div>

                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 sm:p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Método de pago</label>
                            <select name="metodo_pago" class="w-full bg-gray-800 border-gray-700 text-white rounded-md">
                                <option value="yape">Yape 964382212</option>
                                <option value="plin">Plin 964382212</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Número de operación (opcional)</label>
                            <input type="text" name="numero_operacion" class="w-full bg-gray-800 border-gray-700 text-white rounded-md">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Foto del comprobante de pago (opcional)</label>
                            <input type="file" name="comprobante" accept="image/*" class="w-full text-sm text-gray-300 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-dorado-500 file:text-black file:font-semibold">
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-6 px-4 py-3 bg-dorado-500 hover:bg-dorado-600 text-black font-bold rounded-xl text-lg">
                        Confirmar compra
                    </button>
                </form>

            </div>
        </div>
    </div>

    <template id="plantilla-jugada">
        <div class="jugada-block bg-gray-900 border border-gray-800 rounded-xl p-4 mb-4">
            <div class="flex justify-between items-center mb-3">
                <p class="font-semibold text-sm text-dorado-400">Jugada <span class="numero-jugada"></span></p>
                <button type="button" class="btn-quitar-jugada text-red-400 text-xs hidden">✕ Quitar</button>
            </div>
            <div class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 gap-1.5 mb-3 grid-numeros">
                @for ($n = 1; $n <= 60; $n++)
                    <button type="button" class="numero-btn border border-gray-700 bg-gray-800 text-gray-200 rounded-md py-2 text-xs font-semibold hover:border-dorado-400 transition-all duration-150" data-numero="{{ $n }}">{{ $n }}</button>
                @endfor
            </div>
            <div class="numeros-hidden"></div>
            <div class="flex justify-between items-center">
                <p class="text-xs text-gray-400">Seleccionados: <span class="contador">0</span> / 6</p>
                <button type="button" class="btn-azar px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-white rounded-md text-xs font-semibold">🎲 Al azar</button>
            </div>
        </div>
    </template>

    <script>
        let totalJugadas = 0;

        function crearJugada() {
            const plantilla = document.getElementById('plantilla-jugada').content.cloneNode(true);
            totalJugadas++;

            const bloque = plantilla.querySelector('.jugada-block');
            bloque.dataset.indice = totalJugadas - 1;
            plantilla.querySelector('.numero-jugada').textContent = totalJugadas;

            if (totalJugadas > 1) {
                plantilla.querySelector('.btn-quitar-jugada').classList.remove('hidden');
            }

            document.getElementById('contenedor-jugadas').appendChild(plantilla);
            actualizarTotales();
        }

        function actualizarTotales() {
            const bloques = document.querySelectorAll('.jugada-block');
            document.getElementById('total-jugadas').textContent = bloques.length;
            document.getElementById('total-monto').textContent = (bloques.length * 1).toFixed(2);
        }

        function seleccionarBoton(btn) {
            btn.classList.remove('bg-gray-800', 'text-gray-200', 'border-gray-700');
            btn.classList.add('bg-dorado-500', 'text-black', 'border-dorado-500', 'scale-110', 'shadow-lg');
        }

        function deseleccionarBoton(btn) {
            btn.classList.remove('bg-dorado-500', 'text-black', 'border-dorado-500', 'scale-110', 'shadow-lg');
            btn.classList.add('bg-gray-800', 'text-gray-200', 'border-gray-700');
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

            bloque.querySelector('.contador').textContent = seleccionados.length;
        }

        document.getElementById('contenedor-jugadas').addEventListener('click', function (e) {
            const bloque = e.target.closest('.jugada-block');
            if (! bloque) return;

            if (e.target.classList.contains('numero-btn')) {
                const seleccionados = bloque.querySelectorAll('.numero-btn.bg-dorado-500').length;

                if (e.target.classList.contains('bg-dorado-500')) {
                    deseleccionarBoton(e.target);
                } else {
                    if (seleccionados >= 6) return;
                    seleccionarBoton(e.target);
                }

                actualizarInputsOcultos(bloque);
            }

            if (e.target.classList.contains('btn-azar')) {
                bloque.querySelectorAll('.numero-btn').forEach(b => deseleccionarBoton(b));

                const todos = Array.from({ length: 60 }, (_, i) => i + 1);
                for (let i = todos.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [todos[i], todos[j]] = [todos[j], todos[i]];
                }
                const elegidos = todos.slice(0, 6);

                elegidos.forEach(n => {
                    seleccionarBoton(bloque.querySelector(`.numero-btn[data-numero="${n}"]`));
                });

                actualizarInputsOcultos(bloque);
            }

            if (e.target.classList.contains('btn-quitar-jugada')) {
                bloque.remove();
                actualizarTotales();
            }
        });

        document.getElementById('btn-agregar-jugada').addEventListener('click', crearJugada);

        document.getElementById('form-boleto').addEventListener('submit', function (e) {
            const bloques = document.querySelectorAll('.jugada-block');
            for (const bloque of bloques) {
                const seleccionados = bloque.querySelectorAll('.numero-btn.bg-dorado-500').length;
                if (seleccionados !== 6) {
                    e.preventDefault();
                    alert('Cada jugada debe tener exactamente 6 números.');
                    return;
                }
            }
        });

        crearJugada();
    </script>
</x-app-layout>