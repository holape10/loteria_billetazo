<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">Mi cuenta</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('exito'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('exito') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
            @endif
            @if ($errors->has('comprobante'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ $errors->first('comprobante') }}</div>
            @endif

            @if (auth()->user()->rol === 'jugador')
                <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 mb-6 border border-dorado-700">
                    <a href="{{ route('boletos.create') }}" class="inline-block px-4 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded text-lg">🎟️ Jugar ahora</a>
                    @if ($creditosGratis > 0)
                        <span class="ml-3 text-dorado-400 font-semibold">🎁 Tienes {{ $creditosGratis }} jugada(s) gratis</span>
                    @endif
                </div>

                @if ($comprasSinComprobante->isNotEmpty())
                    <div class="bg-yellow-900/40 text-yellow-100 sm:rounded-lg p-4 mb-6 border border-yellow-500">
                        <p class="font-semibold mb-3">⚠️ Tienes {{ $comprasSinComprobante->count() }} compra(s) pendiente(s) sin comprobante de pago. Súbelo para que validemos tu pago más rápido.</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($comprasSinComprobante as $compraPendiente)
                                <button type="button"
                                        onclick="abrirModalSubir('{{ route('compras.comprobante.subir', $compraPendiente) }}', '{{ $compraPendiente->sorteo->fecha->format('d/m/Y') }}', '{{ number_format($compraPendiente->monto_total, 2) }}', false)"
                                        class="px-3 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded text-sm">
                                    ⬆️ Subir comprobante · Sorteo {{ $compraPendiente->sorteo->fecha->format('d/m/Y') }} · S/ {{ number_format($compraPendiente->monto_total, 2) }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">
                    <h3 class="font-semibold mb-4 text-dorado-400 text-lg">Mi historial de jugadas</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-base">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="py-2 pr-4">Sorteo</th>
                                    <th class="py-2 pr-4">Números</th>
                                    <th class="py-2 pr-4">Aciertos</th>
                                    <th class="py-2 pr-4">Premio</th>
                                    <th class="py-2 pr-4">Estado de pago</th>
                                    <th class="py-2">Comprobante</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($boletos as $boleto)
                                    <tr class="border-b border-gray-800">
                                        <td class="py-2 pr-4">{{ $boleto->sorteo->fecha->format('d/m/Y') }}</td>
                                        <td class="py-2 pr-4">{{ collect([$boleto->numero_1,$boleto->numero_2,$boleto->numero_3,$boleto->numero_4,$boleto->numero_5,$boleto->numero_6])->join(' - ') }}</td>
                                        <td class="py-2 pr-4">{{ $boleto->aciertos ?? '—' }}</td>
                                        <td class="py-2 pr-4">
                                            @if ($boleto->premio_ganado)
                                                <span class="text-dorado-400 font-semibold">S/ {{ number_format($boleto->premio_ganado, 2) }}</span>
                                            @elseif ($boleto->jugada_gratis_ganada)
                                                <span class="text-dorado-400 font-semibold">🎟️ Jugada gratis</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="py-2 pr-4">{{ $boleto->compra ? ucfirst($boleto->compra->estado_pago) : '—' }}</td>
                                        <td class="py-2">
                                            @if ($boleto->compra)
                                                <a href="{{ route('compras.comprobante', $boleto->compra) }}" target="_blank" class="text-blue-400">👁️</a>
                                                <a href="{{ route('compras.comprobante.pdf', $boleto->compra) }}" target="_blank" class="text-dorado-400 ml-1">📄</a>
                                                @if ($boleto->compra->comprobante)
                                                    <a href="{{ route('compras.comprobante.imagen', $boleto->compra) }}" target="_blank" class="text-green-400 ml-1" title="Ver mi captura de pago">🧾</a>
                                                @endif
                                                @if ($boleto->compra->estado_pago === 'pendiente' && $boleto->compra->monto_total > 0)
                                                    <button type="button"
                                                            onclick="abrirModalSubir('{{ route('compras.comprobante.subir', $boleto->compra) }}', '{{ $boleto->sorteo->fecha->format('d/m/Y') }}', '{{ number_format($boleto->compra->monto_total, 2) }}', {{ $boleto->compra->comprobante ? 'true' : 'false' }})"
                                                            class="ml-1 px-2 py-0.5 rounded text-xs font-semibold {{ $boleto->compra->comprobante ? 'border border-gray-600 text-gray-300 hover:text-dorado-400' : 'bg-dorado-500 hover:bg-dorado-600 text-black' }}"
                                                            title="{{ $boleto->compra->comprobante ? 'Reemplazar comprobante' : 'Subir comprobante' }}">
                                                        ⬆️ {{ $boleto->compra->comprobante ? 'Cambiar' : 'Subir' }}
                                                    </button>
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-4 text-center text-gray-400">Todavía no has jugado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $boletos->links() }}</div>
                </div>

                <div class="mt-6 text-center text-gray-400 text-sm border-t border-gray-800 pt-4">
                    ¿Necesitas ayuda? Escríbenos a
                    <a href="mailto:loteriabilletazo@gmail.com" class="text-dorado-400 underline">loteriabilletazo@gmail.com</a>
                </div>

                <!-- Modal: subir comprobante de una compra pendiente -->
                <div id="modal-subir" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4" onclick="cerrarModalSubir()">
                    <div class="w-full max-w-md bg-gray-900 border-2 border-dorado-500 rounded-2xl p-6 text-white shadow-2xl" onclick="event.stopPropagation()" role="dialog" aria-modal="true" aria-labelledby="titulo-modal-subir">
                        <div class="flex justify-between items-start mb-2">
                            <h3 id="titulo-modal-subir" class="text-xl font-bold text-dorado-400">🧾 <span id="modal-subir-titulo">Subir comprobante</span></h3>
                            <button type="button" onclick="cerrarModalSubir()" class="text-gray-400 hover:text-white text-2xl leading-none" aria-label="Cerrar">&times;</button>
                        </div>
                        <p class="text-gray-300 text-sm mb-4">
                            Sorteo del <span id="modal-subir-fecha" class="text-dorado-300 font-semibold"></span>
                            · Monto: <span class="text-dorado-300 font-semibold">S/ <span id="modal-subir-monto"></span></span>
                        </p>

                        <form id="form-subir" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="comprobante" id="input-subir" accept="image/jpeg,image/png,image/webp" required
                                   class="w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-dorado-500 file:text-black file:font-bold file:cursor-pointer">
                            <img id="preview-subir" class="hidden mt-4 max-h-64 mx-auto rounded-lg border border-gray-700" alt="Vista previa del comprobante">
                            <p class="text-xs text-gray-500 mt-2">JPG, PNG o WEBP · máximo 4 MB</p>

                            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-6">
                                <button type="button" onclick="cerrarModalSubir()" class="flex-1 px-4 py-3 rounded-xl border-2 border-gray-600 text-gray-200 font-semibold hover:bg-gray-800">Cancelar</button>
                                <button type="submit" id="btn-enviar-subir" class="flex-1 px-4 py-3 rounded-xl bg-dorado-500 hover:bg-dorado-600 text-black font-bold">Enviar comprobante</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    function abrirModalSubir(accion, fecha, monto, reemplazar) {
                        const form = document.getElementById('form-subir');
                        form.action = accion;
                        form.reset();
                        document.getElementById('preview-subir').classList.add('hidden');
                        document.getElementById('modal-subir-fecha').textContent = fecha;
                        document.getElementById('modal-subir-monto').textContent = monto;
                        document.getElementById('modal-subir-titulo').textContent = reemplazar ? 'Reemplazar comprobante' : 'Subir comprobante';
                        document.getElementById('modal-subir').classList.remove('hidden');
                    }

                    function cerrarModalSubir() {
                        document.getElementById('modal-subir').classList.add('hidden');
                    }

                    document.getElementById('input-subir').addEventListener('change', function () {
                        const preview = document.getElementById('preview-subir');
                        if (this.files[0]) {
                            preview.src = URL.createObjectURL(this.files[0]);
                            preview.classList.remove('hidden');
                        } else {
                            preview.classList.add('hidden');
                        }
                    });

                    document.getElementById('form-subir').addEventListener('submit', function () {
                        const btn = document.getElementById('btn-enviar-subir');
                        btn.disabled = true;
                        btn.textContent = 'Enviando...';
                    });

                    document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModalSubir(); });
                </script>
            @elseif (auth()->user()->rol === 'moderador')
                <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">
                    <p class="text-gray-300 text-lg">Bienvenido, moderador. Usa el menú de arriba para ver y realizar los sorteos, y consultar los ganadores.</p>
                </div>
            @else
                <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">
                    <p class="text-gray-300 text-lg">Bienvenido, administrador. Usa el menú de arriba para gestionar clientes, sorteos, compras y reportes.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>