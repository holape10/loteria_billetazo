<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Elegir números ganadores — {{ $sorteo->fecha->format('d/m/Y') }}</h2>
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

                <form action="{{ route('sorteos.realizar-manual', $sorteo) }}" method="POST" id="form-manual" onsubmit="return confirm('¿Confirmar estos números como ganadores? Esta acción no se puede deshacer.')">
                    @csrf

                    <div class="grid grid-cols-6 sm:grid-cols-10 gap-2 mb-4">
                        @for ($n = 1; $n <= 60; $n++)
                            <button type="button" class="numero-btn border rounded py-2 text-sm font-semibold hover:bg-dorado-100" data-numero="{{ $n }}">{{ $n }}</button>
                        @endfor
                    </div>

                    <div id="numeros-hidden"></div>

                    <p class="text-sm text-gray-600 mb-4">Seleccionados: <span id="contador">0</span> / 6</p>

                    <button type="submit" class="px-4 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded">Confirmar números ganadores</button>
                    <a href="{{ route('sorteos.index') }}" class="ml-2 text-gray-600">Cancelar</a>
                </form>

            </div>
        </div>
    </div>

    <script>
        let seleccionados = [];

        function actualizarContador() {
            document.getElementById('contador').textContent = seleccionados.length;
        }

        function actualizarInputsOcultos() {
            const contenedor = document.getElementById('numeros-hidden');
            contenedor.innerHTML = '';
            seleccionados.forEach(n => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'numeros[]';
                input.value = n;
                contenedor.appendChild(input);
            });
        }

        document.querySelectorAll('.numero-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const numero = parseInt(this.dataset.numero);

                if (this.classList.contains('bg-dorado-500')) {
                    seleccionados = seleccionados.filter(n => n !== numero);
                    this.classList.remove('bg-dorado-500', 'text-black');
                } else {
                    if (seleccionados.length >= 6) return;
                    seleccionados.push(numero);
                    this.classList.add('bg-dorado-500', 'text-black');
                }

                actualizarContador();
                actualizarInputsOcultos();
            });
        });

        document.getElementById('form-manual').addEventListener('submit', function (e) {
            if (seleccionados.length !== 6) {
                e.preventDefault();
                alert('Debes elegir exactamente 6 números.');
            }
        });
    </script>
</x-app-layout>