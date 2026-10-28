{{-- Publicidad de auspiciadores en la página de inicio: ventana emergente (sin sonido) + sección fija --}}
@php
    $auspiciadores = \App\Models\Publicidad::paraInicio();
    $frecuencia = \App\Models\Configuracion::publicidadInicioFrecuencia();
@endphp

@if (count($auspiciadores))
    {{-- Sección fija: siempre visible en la portada --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12" aria-labelledby="titulo-auspiciadores">
        <h2 id="titulo-auspiciadores" class="text-center text-2xl sm:text-3xl font-extrabold text-dorado-400 mb-2">🤝 Nuestros auspiciadores</h2>
        <p class="text-center text-gray-400 mb-8">Gracias a ellos El Billetazo llega a ti cada semana</p>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($auspiciadores as $a)
                @php $etiqueta = $a['enlace'] ? 'a' : 'div'; @endphp
                <{{ $etiqueta }} @if ($a['enlace']) href="{{ $a['enlace'] }}" target="_blank" rel="noopener sponsored" @endif
                    class="group block rounded-2xl border border-dorado-700 bg-gray-900 overflow-hidden {{ $a['enlace'] ? 'hover:border-dorado-400 transition' : '' }}">
                    <div class="aspect-[4/3] bg-black flex items-center justify-center">
                        @if ($a['imagen'])
                            <img src="{{ $a['imagen'] }}" alt="{{ $a['titulo'] }}" loading="lazy" class="w-full h-full" style="{{ $a['estilo'] }}">
                        @else
                            <span class="px-3 text-center text-lg font-black text-dorado-300">{{ $a['titulo'] }}</span>
                        @endif
                    </div>
                    <div class="p-3 text-center">
                        <p class="font-bold text-white text-sm truncate">{{ $a['titulo'] }}</p>
                        @if ($a['enlace'])
                            <p class="text-xs text-dorado-400 group-hover:underline">Visitar →</p>
                        @endif
                    </div>
                </{{ $etiqueta }}>
            @endforeach
        </div>
    </section>

    {{-- Ventana emergente --}}
    @if ($frecuencia !== 'nunca')
        <div id="modal-auspiciador" class="hidden fixed inset-0 z-[60] bg-black/80 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="auspiciador-titulo">
            <div class="relative w-full max-w-md bg-gray-900 border-2 border-dorado-500 rounded-2xl overflow-hidden shadow-2xl shadow-dorado-500/20 text-white">
                <button type="button" id="auspiciador-cerrar" class="absolute top-2 right-2 z-10 w-10 h-10 rounded-full bg-black/70 hover:bg-black text-white text-2xl leading-none" aria-label="Cerrar publicidad">&times;</button>

                <div id="auspiciador-marco" class="aspect-[4/3] bg-black">
                    <img id="auspiciador-imagen" class="w-full h-full" alt="">
                </div>

                <div class="p-5 text-center">
                    <p class="text-[10px] uppercase tracking-[0.25em] text-gray-500 mb-1">Auspiciador</p>
                    <h3 id="auspiciador-titulo" class="text-xl font-black text-dorado-300"></h3>
                    <p id="auspiciador-texto" class="mt-2 text-sm text-gray-300 whitespace-pre-line"></p>
                    <a id="auspiciador-enlace" href="#" target="_blank" rel="noopener sponsored" class="hidden mt-4 inline-block px-6 py-2.5 rounded-full bg-dorado-500 hover:bg-dorado-600 text-black font-bold">Visitar</a>

                    <div id="auspiciador-controles" class="hidden mt-4 flex items-center justify-center gap-3">
                        <button type="button" id="auspiciador-anterior" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-gray-700" aria-label="Anterior">‹</button>
                        <div id="auspiciador-puntos" class="flex gap-1.5"></div>
                        <button type="button" id="auspiciador-siguiente" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-gray-700" aria-label="Siguiente">›</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const anuncios = @json($auspiciadores);
                const frecuencia = @json($frecuencia);
                const $ = id => document.getElementById(id);
                let actual = 0;
                let temporizador = null;

                // ¿Ya se mostró? (según la frecuencia elegida en Configuración)
                function yaSeMostro() {
                    try {
                        if (frecuencia === 'sesion') return sessionStorage.getItem('billetazo:auspiciador') === '1';
                        if (frecuencia === 'dia') return localStorage.getItem('billetazo:auspiciador') === new Date().toDateString();
                    } catch (e) {}
                    return false;
                }

                function marcarMostrado() {
                    try {
                        if (frecuencia === 'sesion') sessionStorage.setItem('billetazo:auspiciador', '1');
                        if (frecuencia === 'dia') localStorage.setItem('billetazo:auspiciador', new Date().toDateString());
                    } catch (e) {}
                }

                function pintar(indice) {
                    actual = (indice + anuncios.length) % anuncios.length;
                    const a = anuncios[actual];
                    const img = $('auspiciador-imagen');
                    $('auspiciador-marco').classList.toggle('hidden', !a.imagen);
                    if (a.imagen) { img.src = a.imagen; img.alt = a.titulo; img.style.cssText = a.estilo; }
                    $('auspiciador-titulo').textContent = a.titulo;
                    $('auspiciador-texto').textContent = a.texto || '';
                    const enlace = $('auspiciador-enlace');
                    enlace.classList.toggle('hidden', !a.enlace);
                    if (a.enlace) enlace.href = a.enlace;

                    $('auspiciador-puntos').innerHTML = '';
                    anuncios.forEach((_, i) => {
                        const punto = document.createElement('button');
                        punto.type = 'button';
                        punto.className = 'w-2.5 h-2.5 rounded-full ' + (i === actual ? 'bg-dorado-400' : 'bg-gray-600');
                        punto.setAttribute('aria-label', 'Ver auspiciador ' + (i + 1));
                        punto.addEventListener('click', () => { pintar(i); programar(); });
                        $('auspiciador-puntos').appendChild(punto);
                    });
                }

                // Con varios auspiciadores pasan solos, cada uno el tiempo configurado
                function programar() {
                    clearTimeout(temporizador);
                    if (anuncios.length > 1) {
                        temporizador = setTimeout(() => { pintar(actual + 1); programar(); }, (anuncios[actual].segundos || 8) * 1000);
                    }
                }

                function cerrar() {
                    clearTimeout(temporizador);
                    $('modal-auspiciador').classList.add('hidden');
                }

                if (!anuncios.length || yaSeMostro()) return;

                $('auspiciador-cerrar').addEventListener('click', cerrar);
                $('modal-auspiciador').addEventListener('click', e => { if (e.target.id === 'modal-auspiciador') cerrar(); });
                document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrar(); });
                $('auspiciador-anterior').addEventListener('click', () => { pintar(actual - 1); programar(); });
                $('auspiciador-siguiente').addEventListener('click', () => { pintar(actual + 1); programar(); });
                $('auspiciador-controles').classList.toggle('hidden', anuncios.length < 2);

                // Aparece un momento después de cargar, para que primero se vea la página
                setTimeout(() => {
                    pintar(0);
                    $('modal-auspiciador').classList.remove('hidden');
                    $('auspiciador-cerrar').focus();
                    marcarMostrado();
                    programar();
                }, 1200);
            })();
        </script>
    @endif
@endif
