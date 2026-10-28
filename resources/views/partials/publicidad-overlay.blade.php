{{-- Publicidad a pantalla completa antes del sorteo. Se muestra con window.Publicidad.mostrar(). --}}
@php $anuncios = \App\Models\Publicidad::paraSorteo(); @endphp

<div id="publicidad-overlay" class="hidden fixed inset-0 z-[60] bg-black/95 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="publicidad-titulo">
    <div class="w-full max-w-lg text-center text-white">
        <p class="text-dorado-400 text-xs uppercase tracking-[0.3em] font-bold mb-4">Este sorteo llega gracias a</p>
        <div id="publicidad-marco" class="hidden mx-auto w-full max-w-[66vh] aspect-[4/3] rounded-2xl border-2 border-dorado-500 bg-black overflow-hidden shadow-2xl shadow-dorado-500/20 mb-5">
            <img id="publicidad-imagen" class="w-full h-full" alt="">
        </div>
        <h3 id="publicidad-titulo" class="text-2xl sm:text-3xl font-black text-dorado-300"></h3>
        <p id="publicidad-texto" class="mt-3 text-gray-200 text-base sm:text-lg whitespace-pre-line"></p>
        <div class="mt-6 h-1.5 bg-gray-800 rounded-full overflow-hidden">
            <div id="publicidad-progreso" class="h-full bg-dorado-500" style="width:0%"></div>
        </div>
        <button type="button" id="publicidad-saltar" class="mt-5 text-sm text-gray-400 hover:text-white underline">Saltar publicidad</button>
    </div>
    <audio id="publicidad-audio" preload="none"></audio>
</div>

<script>
    window.Publicidad = (function () {
        const anuncios = @json($anuncios);
        const $ = id => document.getElementById(id);
        let enCurso = null;
        let saltar = null;

        function esperar(ms) {
            return new Promise(resolve => {
                const t = setTimeout(resolve, ms);
                saltar = () => { clearTimeout(t); resolve(); };
            });
        }

        function animarProgreso(ms) {
            const barra = $('publicidad-progreso');
            barra.style.transition = 'none';
            barra.style.width = '0%';
            void barra.offsetWidth;
            barra.style.transition = `width ${ms}ms linear`;
            barra.style.width = '100%';
        }

        function reproducirAudio(url) {
            const audio = $('publicidad-audio');
            return new Promise(resolve => {
                audio.src = url;
                audio.onended = audio.onerror = () => resolve();
                audio.play().catch(() => resolve());
            });
        }

        async function mostrarAnuncio(a) {
            $('publicidad-titulo').textContent = a.titulo;
            $('publicidad-texto').textContent = a.texto || '';
            const img = $('publicidad-imagen');
            const marco = $('publicidad-marco');
            if (a.imagen) { img.src = a.imagen; img.alt = a.titulo; img.style.cssText = a.estilo; marco.classList.remove('hidden'); }
            else { marco.classList.add('hidden'); img.removeAttribute('src'); }

            const ms = a.segundos * 1000;
            animarProgreso(ms);

            // Se queda en pantalla el tiempo configurado o hasta que termine el audio / la voz (lo que sea más largo)
            const sonido = a.audio ? reproducirAudio(a.audio)
                : (a.voz && window.Locutor ? Locutor.hablar(a.voz) : Promise.resolve());
            const tiempo = esperar(ms);
            await Promise.race([Promise.all([sonido, tiempo]), new Promise(r => { const s = saltar; saltar = () => { s && s(); r(); }; })]);
        }

        function cerrar() {
            $('publicidad-overlay').classList.add('hidden');
            const audio = $('publicidad-audio');
            audio.pause();
            if (window.Locutor) Locutor.callar();
        }

        $('publicidad-saltar').addEventListener('click', () => { cancelado = true; saltar && saltar(); });
        let cancelado = false;

        return {
            hay: anuncios.length > 0,
            // Devuelve una promesa que se cumple cuando termina (o la saltan)
            mostrar() {
                if (!anuncios.length) return Promise.resolve();
                if (enCurso) return enCurso;
                cancelado = false;
                $('publicidad-overlay').classList.remove('hidden');
                enCurso = (async () => {
                    for (const a of anuncios) {
                        if (cancelado) break;
                        await mostrarAnuncio(a);
                    }
                    cerrar();
                    enCurso = null;
                })();
                return enCurso;
            },
            cerrar() { cancelado = true; saltar && saltar(); },
        };
    })();
</script>
