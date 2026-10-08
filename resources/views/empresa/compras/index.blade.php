<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">Compras / Pagos</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">

                @if (session('exito'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('exito') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
                @endif

                <form action="{{ route('compras.index') }}" method="GET" class="mb-4 flex gap-2">
                    <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Buscar por DNI, nombre, celular o N° de operación..."
                           class="w-full max-w-sm border-gray-300 rounded-md text-sm">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded text-sm">Buscar</button>
                    @if ($busqueda)
                        <a href="{{ route('compras.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm">Limpiar</a>
                    @endif
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm whitespace-nowrap">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2 pr-4">Cliente</th>
                                <th class="py-2 pr-4">DNI</th>
                                <th class="py-2 pr-4">Celular</th>
                                <th class="py-2 pr-4">Sorteo</th>
                                <th class="py-2 pr-4">Jugadas</th>
                                <th class="py-2 pr-4">Total</th>
                                <th class="py-2 pr-4">Método</th>
                                <th class="py-2 pr-4">N° Operación</th>
                                <th class="py-2 pr-4">Comprobante</th>
                                <th class="py-2 pr-4">Estado</th>
                                <th class="py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($compras as $compra)
                                <tr class="border-b">
                                    <td class="py-2 pr-4">{{ $compra->cliente->nombre }}</td>
                                    <td class="py-2 pr-4">{{ $compra->cliente->dni }}</td>
                                    <td class="py-2 pr-4">{{ $compra->cliente->celular }}</td>
                                    <td class="py-2 pr-4">{{ $compra->sorteo->fecha->format('d/m/Y') }}</td>
                                    <td class="py-2 pr-4">{{ $compra->cantidad_jugadas }}</td>
                                    <td class="py-2 pr-4">
                                        S/ {{ number_format($compra->monto_total, 2) }}
                                        @if ($compra->requiere_revision && $compra->monto_detectado !== null && abs($compra->monto_detectado - $compra->monto_total) >= 0.01)
                                            <br>
                                            <span class="text-red-400 text-xs font-semibold">
                                                ⚠️ No coincide con la captura (detectado: S/ {{ number_format($compra->monto_detectado, 2) }})
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2 pr-4">{{ ucfirst($compra->metodo_pago) }}</td>
                                    <td class="py-2 pr-4">
                                        @if ($compra->numero_operacion)
                                            <span class="font-mono select-all">{{ $compra->numero_operacion }}</span>
                                            @if ($operacionesRepetidas->contains($compra->numero_operacion))
                                                <br>
                                                <a href="{{ route('compras.index', ['buscar' => $compra->numero_operacion]) }}" class="text-red-400 text-xs font-semibold" title="Este número de operación aparece en más de una compra">🔁 Repetido</a>
                                            @endif
                                        @else
                                            <span class="text-gray-500">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2 pr-4">
                                        @if ($compra->comprobante)
                                            <a href="{{ route('compras.comprobante.imagen', $compra) }}" target="_blank" class="text-blue-600">Ver imagen</a>
                                        @endif
                                    </td>
                                    <td class="py-2 pr-4">{{ ucfirst($compra->estado_pago) }}</td>
                                                                        <td class="py-2">
                                        @if ($compra->estado_pago === 'pendiente')
                                            <form action="{{ route('compras.aprobar', $compra) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-700 font-semibold">Aprobar</button>
                                            </form>
                                            <form action="{{ route('compras.rechazar', $compra) }}" method="POST" class="inline ml-2">
                                                @csrf
                                                <button type="submit" class="text-red-700 font-semibold">Rechazar</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('compras.comprobante', $compra) }}" target="_blank" class="text-blue-600 font-semibold ml-2">👁️ Ver</a>
                                        <a href="{{ route('compras.comprobante.pdf', $compra) }}" target="_blank" class="text-dorado-600 font-semibold ml-2">📄 PDF</a>
                                        <button type="button" onclick="abrirModalReporte({{ $compra->id }}, '{{ $compra->sorteo->fecha->format('d/m/Y') }}', '{{ number_format($compra->monto_total, 2) }}', '{{ $compra->cantidad_jugadas }}')" class="text-orange-500 font-semibold ml-2">🚩 Reporte</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="11" class="py-4 text-center text-gray-500">No se encontraron compras.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $compras->links() }}</div>
            </div>
        </div>
    </div>
        <!-- Modal de Reporte / Incidencia -->
    <div id="modal-reporte" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4" data-url-template="{{ route('incidencias.store', ['compra' => 'ID_PLACEHOLDER']) }}">
        <div class="bg-gray-900 border-2 border-dorado-600 rounded-2xl p-6 max-w-md w-full text-white">
            <h3 class="text-xl font-bold text-dorado-400 mb-4">🚩 Reportar incidencia</h3>

            <div class="bg-gray-800 rounded-lg p-3 mb-4 text-sm space-y-1">
                <p><span class="text-gray-400">Sorteo:</span> <span id="reporte-sorteo" class="font-semibold"></span></p>
                <p><span class="text-gray-400">Jugadas:</span> <span id="reporte-jugadas" class="font-semibold"></span></p>
                <p><span class="text-gray-400">Monto de la compra:</span> S/ <span id="reporte-monto" class="font-semibold"></span></p>
            </div>

            <form id="form-incidencia" action="" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Fecha del depósito (opcional)</label>
                    <input type="date" name="fecha_deposito" class="w-full bg-gray-800 border-gray-700 text-white rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Observaciones / motivo del reporte</label>
                    <textarea name="observaciones" rows="4" required placeholder="Ej: El depósito no corresponde a la cantidad de jugadas registradas..." class="w-full bg-gray-800 border-gray-700 text-white rounded-md"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="cerrarModalReporte()" class="px-4 py-2 bg-gray-700 text-white rounded">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded">Guardar incidencia</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalReporte(compraId, sorteoFecha, monto, jugadas) {
            const modal = document.getElementById('modal-reporte');
            const url = modal.dataset.urlTemplate.replace('ID_PLACEHOLDER', compraId);

            document.getElementById('form-incidencia').action = url;
            document.getElementById('reporte-sorteo').textContent = sorteoFecha;
            document.getElementById('reporte-monto').textContent = monto;
            document.getElementById('reporte-jugadas').textContent = jugadas;

            modal.classList.remove('hidden');
        }

        function cerrarModalReporte() {
            document.getElementById('modal-reporte').classList.add('hidden');
        }
    </script>
</x-app-layout>