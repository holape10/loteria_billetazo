<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Elige tus números — S/ 1.00 por jugada</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('boletos.store', $sorteo) }}" method="POST" enctype="multipart/form-data" id="form-boleto">
                    @csrf

                    <div id="contenedor-jugadas"></div>

                    <button type="button" id="btn-agregar-jugada" class="px-4 py-2 bg-gray-700 text-white rounded mb-4">+ Agregar otra jugada</button>

                    <p class="text-sm font-semibold text-gray-700 mb-4">Total: S/ <span id="total-monto">1.00</span> (<span id="total-jugadas">1</span> jugada)</p>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Método de pago</label>
                        <select name="metodo_pago" class="mt-1 block w-full border-gray-300 rounded-md">
                            <option value="yape">Yape</option>
                            <option value="plin">Plin</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Número de operación (opcional)</label>
                        <input type="text" name="numero_operacion" class="mt-1 block w-full border-gray-300 rounded-md">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Foto del comprobante de pago (opcional)</label>
                        <input type="file" name="comprobante" accept="image/*" class="mt-1 block w-full">
                    </div>

                    <button type="submit" class="px-4 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded">Confirmar compra</button>
                </form>

            </div>
        </div>
    </div>

    <template id="plantilla-jugada">
        <div class="jugada-block border rounded p-4 mb-4">
            <div class="flex justify-between items-center mb-2">
                <p class="font-semibold text-sm text-gray-700">Jugada <span class="numero-jugada"></span></p>
                <button type="button" class="btn-quitar-jugada text-red-600 text-sm hidden">Quitar</button>
            </div>
            <div class="grid grid-cols-6 sm:grid-cols-10 gap-1 mb-2 grid-numeros">
                @for ($n = 1; $n <= 60; $n++)
                    <button type="button" class="numero-btn border rounded py-1 text-xs font-semibold hover:bg-dorado-100" data-numero="{{ $n }}">{{ $n }}</button>
                @endfor
            </div>
            <div class="numeros-hidden"></div>
            <div class="flex justify-between items-center">
                <p class="text-xs text-gray-600">Seleccionados: <span class="contador">0</span> / 6</p>
                <button type="button" class="btn-azar px-3 py-1 bg-gray-600 text-white rounded text-xs">🎲 Al azar</button>
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
                    e.target.classList.remove('bg-dorado-500', 'text-black');
                } else {
                    if (seleccionados >= 6) return;
                    e.target.classList.add('bg-dorado-500', 'text-black');
                }

                actualizarInputsOcultos(bloque);
            }

            if (e.target.classList.contains('btn-azar')) {
                bloque.querySelectorAll('.numero-btn').forEach(b => b.classList.remove('bg-dorado-500', 'text-black'));

                const todos = Array.from({ length: 60 }, (_, i) => i + 1);
                for (let i = todos.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [todos[i], todos[j]] = [todos[j], todos[i]];
                }
                const elegidos = todos.slice(0, 6);

                elegidos.forEach(n => {
                    bloque.querySelector(`.numero-btn[data-numero="${n}"]`).classList.add('bg-dorado-500', 'text-black');
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