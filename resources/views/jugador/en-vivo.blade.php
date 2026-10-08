<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h2 class="font-semibold text-xl text-dorado-400 leading-tight">📺 Sorteo en vivo</h2>
            <span id="pill-estado" class="hidden items-center gap-2 px-3 py-1 rounded-full text-sm font-bold"></span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Cargando -->
            <div id="vista-cargando" class="text-center text-gray-400 py-16">Conectando con el sorteo...</div>

            <!-- Sin sorteo -->
            <div id="vista-sin-sorteo" class="hidden bg-gray-900 border border-dorado-700 rounded-2xl p-8 text-center text-gray-300">
                <div class="text-5xl mb-3">🎱</div>
                <p class="text-lg">Todavía no hay sorteos programados. ¡Vuelve pronto!</p>
            </div>

            <!-- Esperando el inicio -->
            <div id="vista-esperando" class="hidden bg-gradient-to-br from-gray-900 to-black border-2 border-dorado-600 rounded-3xl p-6 sm:p-8 text-center text-white">
                <p class="text-dorado-400 text-sm uppercase tracking-[0.25em] font-bold mb-2">Próximo sorteo</p>
                <p class="text-gray-300 mb-4"><span id="esperando-fecha"></span></p>
                <p class="text-gray-400 text-sm mb-1">Pozo acumulado</p>
                <p class="text-4xl sm:text-5xl font-black text-dorado-400 mb-6">S/ <span id="esperando-pozo"></span></p>
                <div id="cuenta-regresiva" class="grid grid-cols-4 gap-2 max-w-sm mx-auto"></div>
                <p id="esperando-mensaje" class="text-gray-400 text-sm mt-4">Esta pantalla se actualizará sola cuando empiece el sorteo.</p>
            </div>

            <!-- En vivo / finalizado -->
            <div id="vista-sorteo" class="hidden space-y-6">
                <div class="bg-gradient-to-br from-gray-900 to-black border-2 border-dorado-600 rounded-3xl p-5 sm:p-8 text-center text-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_40%,rgba(251,191,36,0.12),transparent_65%)] pointer-events-none"></div>
                    <div class="relative">
                        <p class="text-gray-400 text-sm mb-1">Sorteo del <span id="sorteo-fecha"></span> · Pozo S/ <span id="sorteo-pozo"></span></p>
                        <p id="titulo-ultimo" class="text-dorado-400 text-xs sm:text-sm uppercase tracking-[0.25em] font-bold mt-3">Último número</p>
                        <div id="ultimo-numero" class="text-[7rem] sm:text-[9rem] leading-none font-black text-transparent bg-clip-text bg-gradient-to-b from-yellow-300 via-dorado-400 to-yellow-600 drop-shadow-[0_0_30px_rgba(251,191,36,0.6)] my-2">?</div>

                        <div id="casillas" class="flex justify-center gap-2 sm:gap-3 mt-4"></div>
                        <p class="text-gray-400 text-xs sm:text-sm mt-4">
                            <span id="stat-jugadas">0</span> jugadas participando ·
                            <span id="stat-carrera" class="text-dorado-300 font-semibold">0</span> aún pueden ganar el pozo
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Mis jugadas -->
                    <div class="bg-gray-900 border border-dorado-700 rounded-2xl p-5 text-white">
                        <h3 class="font-bold text-dorado-400 text-lg mb-4">🎟️ Mis jugadas</h3>
                        <div id="mis-jugadas" class="space-y-3"></div>
                    </div>

                    <!-- Ranking -->
                    <div class="bg-gray-900 border border-dorado-700 rounded-2xl p-5 text-white">
                        <h3 class="font-bold text-dorado-400 text-lg mb-4 flex items-center gap-2">
                            <span id="ranking-punto" class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                            <span id="ranking-titulo">¿Quién va ganando?</span>
                        </h3>
                        <ol id="ranking" class="space-y-2"></ol>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <canvas id="confetti-en-vivo" class="fixed inset-0 pointer-events-none z-50 hidden"></canvas>

    <style>
        @keyframes revelar-numero {
            0% { transform: scale(0.2) rotate(-20deg); opacity: 0; }
            60% { transform: scale(1.25) rotate(4deg); opacity: 1; }
            100% { transform: scale(1) rotate(0); }
        }
        .revelar { animation: revelar-numero 0.8s cubic-bezier(.2,1.4,.4,1); }
        @keyframes latido { 0%,100% { transform: scale(1); } 50% { transform: scale(1.12); } }
        .latido { animation: latido 0.6s ease-in-out 2; }
    </style>

    <script>
        (function () {
            const URL_ESTADO = @json(route('en-vivo.estado'));
            const $ = id => document.getElementById(id);

            let ultimoConteo = null;
            let estadoAnterior = null;
            let temporizador = null;
            let intervaloCuenta = null;

            function mostrarVista(nombre) {
                ['cargando', 'sin-sorteo', 'esperando', 'sorteo'].forEach(v =>
                    $('vista-' + v).classList.toggle('hidden', v !== nombre));
            }

            function pintarPill(estado) {
                const pill = $('pill-estado');
                const estilos = {
                    en_vivo: ['bg-red-600 text-white', '<span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>EN VIVO'],
                    esperando: ['bg-gray-800 text-dorado-300 border border-dorado-700', '⏳ Próximamente'],
                    finalizado: ['bg-green-700 text-white', '✅ Finalizado'],
                };
                if (!estilos[estado]) { pill.classList.add('hidden'); return; }
                pill.className = 'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-bold ' + estilos[estado][0];
                pill.innerHTML = estilos[estado][1];
            }

            function bola(numero, acertado, tamano = 'w-9 h-9 text-sm') {
                const el = document.createElement('span');
                el.className = `${tamano} inline-flex items-center justify-center rounded-full font-black ` +
                    (acertado ? 'bg-gradient-to-br from-yellow-300 to-dorado-500 text-black shadow-lg shadow-dorado-500/40'
                              : 'bg-gray-800 text-gray-400 border border-gray-700');
                el.textContent = numero;
                return el;
            }

            function pintarCasillas(extraidos, hayNuevo) {
                const cont = $('casillas');
                cont.innerHTML = '';
                for (let i = 0; i < 6; i++) {
                    const el = document.createElement('span');
                    const numero = extraidos[i];
                    el.className = 'w-11 h-11 sm:w-14 sm:h-14 inline-flex items-center justify-center rounded-xl font-black text-lg sm:text-2xl border-2 ' +
                        (numero ? 'bg-dorado-500/15 border-dorado-500 text-dorado-300' : 'border-dashed border-gray-700 text-gray-600');
                    el.textContent = numero ?? (i + 1);
                    if (hayNuevo && i === extraidos.length - 1) el.classList.add('revelar');
                    cont.appendChild(el);
                }
            }

            function pintarMisJugadas(jugadas, extraidos, estado) {
                const cont = $('mis-jugadas');
                cont.innerHTML = '';

                if (!jugadas.length) {
                    cont.innerHTML = `<p class="text-gray-400 text-sm">No tienes jugadas en este sorteo.</p>
                        <a href="{{ route('boletos.create') }}" class="inline-block mt-3 px-4 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded">🎟️ Jugar ahora</a>`;
                    return;
                }

                jugadas.forEach(j => {
                    const fila = document.createElement('div');
                    fila.className = 'rounded-xl p-3 border ' + (j.participa ? 'border-gray-700 bg-gray-800/40' : 'border-gray-800 opacity-60');

                    const bolas = document.createElement('div');
                    bolas.className = 'flex flex-wrap gap-1.5';
                    j.numeros.forEach(n => bolas.appendChild(bola(n, j.participa && extraidos.includes(n))));

                    const pie = document.createElement('div');
                    pie.className = 'flex justify-between items-center mt-2 text-sm';
                    const izq = document.createElement('span');
                    const der = document.createElement('span');

                    if (!j.participa) {
                        izq.className = 'text-yellow-400';
                        izq.textContent = '⏳ Pago no validado: no participa';
                    } else {
                        izq.className = j.aciertos >= 3 ? 'text-green-400 font-bold' : 'text-gray-300';
                        izq.textContent = `${j.aciertos} de ${extraidos.length} aciertos`;
                    }

                    if (estado === 'finalizado' && j.premio) {
                        der.className = 'text-dorado-300 font-black';
                        der.textContent = `🏆 S/ ${j.premio}`;
                    } else if (estado === 'finalizado' && j.jugada_gratis) {
                        der.className = 'text-dorado-300 font-bold';
                        der.textContent = '🎟️ Jugada gratis';
                    } else if (j.participa && estado === 'en_vivo' && j.aciertos === extraidos.length && extraidos.length > 0) {
                        der.className = 'text-green-400 font-bold';
                        der.textContent = '🔥 ¡Sigues en carrera!';
                    }

                    pie.append(izq, der);
                    fila.append(bolas, pie);
                    cont.appendChild(fila);
                });
            }

            function pintarRanking(ranking, extraidos, estado) {
                const cont = $('ranking');
                cont.innerHTML = '';
                $('ranking-titulo').textContent = estado === 'finalizado' ? 'Resultados del sorteo' : '¿Quién va ganando?';
                $('ranking-punto').classList.toggle('hidden', estado !== 'en_vivo');

                if (!extraidos.length) {
                    cont.innerHTML = '<li class="text-gray-400 text-sm">El ranking aparecerá con la primera bolilla.</li>';
                    return;
                }
                if (!ranking.length) {
                    cont.innerHTML = '<li class="text-gray-400 text-sm">🎲 Nadie ha acertado todavía...</li>';
                    return;
                }

                const medallas = ['🥇', '🥈', '🥉'];
                ranking.forEach((r, i) => {
                    const li = document.createElement('li');
                    li.className = 'flex items-center gap-3 rounded-xl px-3 py-2 ' +
                        (r.es_mio ? 'bg-dorado-500/15 border border-dorado-500' : 'bg-gray-800/50');

                    const pos = document.createElement('span');
                    pos.className = 'w-7 text-center font-bold text-gray-300';
                    pos.textContent = medallas[i] ?? (i + 1);

                    const nombre = document.createElement('span');
                    nombre.className = 'flex-1 font-semibold truncate';
                    nombre.textContent = r.nombre;
                    if (r.es_mio) {
                        const tu = document.createElement('span');
                        tu.className = 'ml-2 text-xs bg-dorado-500 text-black rounded px-1.5 py-0.5 font-bold';
                        tu.textContent = 'TÚ';
                        nombre.appendChild(tu);
                    }

                    const barra = document.createElement('span');
                    barra.className = 'hidden sm:flex gap-0.5';
                    for (let k = 0; k < 6; k++) {
                        const p = document.createElement('span');
                        p.className = 'w-2 h-4 rounded-sm ' + (k < r.aciertos ? 'bg-dorado-400' : 'bg-gray-700');
                        barra.appendChild(p);
                    }

                    const valor = document.createElement('span');
                    valor.className = 'font-black text-right min-w-[4.5rem] ' + (r.aciertos >= 3 ? 'text-green-400' : 'text-dorado-300');
                    valor.textContent = r.premio ? `S/ ${r.premio}` : (r.jugada_gratis ? '🎟️ Gratis' : `${r.aciertos} acierto${r.aciertos === 1 ? '' : 's'}`);

                    li.append(pos, nombre, barra, valor);
                    cont.appendChild(li);
                });
            }

            function pintarCuentaRegresiva(inicioIso) {
                clearInterval(intervaloCuenta);
                const inicio = new Date(inicioIso).getTime();

                const tick = () => {
                    const restante = Math.max(0, inicio - Date.now());
                    const partes = [
                        [Math.floor(restante / 86400000), 'días'],
                        [Math.floor(restante / 3600000) % 24, 'horas'],
                        [Math.floor(restante / 60000) % 60, 'min'],
                        [Math.floor(restante / 1000) % 60, 'seg'],
                    ];
                    $('cuenta-regresiva').innerHTML = partes.map(([v, l]) =>
                        `<div class="bg-gray-800 border border-dorado-700 rounded-xl py-2"><div class="text-2xl sm:text-3xl font-black text-white">${String(v).padStart(2, '0')}</div><div class="text-xs text-gray-400">${l}</div></div>`
                    ).join('');
                    $('esperando-mensaje').textContent = restante === 0
                        ? '¡El sorteo está por comenzar! Esta pantalla se actualizará sola.'
                        : 'Esta pantalla se actualizará sola cuando empiece el sorteo.';
                };
                tick();
                intervaloCuenta = setInterval(tick, 1000);
            }

            function confetti() {
                const canvas = $('confetti-en-vivo');
                const ctx = canvas.getContext('2d');
                canvas.width = innerWidth; canvas.height = innerHeight;
                canvas.classList.remove('hidden');
                const piezas = Array.from({ length: 120 }, () => ({
                    x: Math.random() * canvas.width, y: -20 - Math.random() * canvas.height,
                    v: 2 + Math.random() * 4, r: 4 + Math.random() * 5,
                    c: ['#fbbf24', '#f59e0b', '#fde68a', '#22c55e', '#ffffff'][Math.floor(Math.random() * 5)],
                }));
                let cuadros = 0;
                (function animar() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    piezas.forEach(p => { p.y += p.v; ctx.fillStyle = p.c; ctx.fillRect(p.x, p.y, p.r, p.r); });
                    if (++cuadros < 240) requestAnimationFrame(animar); else canvas.classList.add('hidden');
                })();
            }

            function render(data) {
                pintarPill(data.estado);

                if (data.estado === 'sin_sorteo') { mostrarVista('sin-sorteo'); return; }

                if (data.estado === 'esperando') {
                    mostrarVista('esperando');
                    $('esperando-fecha').textContent = `${data.sorteo.fecha} · ${data.sorteo.hora} h`;
                    $('esperando-pozo').textContent = data.sorteo.premio_mayor;
                    pintarCuentaRegresiva(data.sorteo.inicio);
                    ultimoConteo = 0;
                    estadoAnterior = data.estado;
                    return;
                }

                clearInterval(intervaloCuenta);
                mostrarVista('sorteo');

                const extraidos = data.extraidos;
                const hayNuevo = ultimoConteo !== null && extraidos.length > ultimoConteo;

                $('sorteo-fecha').textContent = data.sorteo.fecha;
                $('sorteo-pozo').textContent = data.sorteo.premio_mayor;
                $('titulo-ultimo').textContent = data.estado === 'finalizado' ? 'Último número del sorteo' : 'Último número extraído';
                $('stat-jugadas').textContent = data.total_jugadas;
                $('stat-carrera').textContent = data.total_en_carrera;

                const ultimo = $('ultimo-numero');
                ultimo.textContent = extraidos.length ? extraidos[extraidos.length - 1] : '?';
                if (hayNuevo) {
                    ultimo.classList.remove('revelar'); void ultimo.offsetWidth; ultimo.classList.add('revelar');
                    if (navigator.vibrate) navigator.vibrate([80, 40, 80]);
                }

                pintarCasillas(extraidos, hayNuevo);
                pintarMisJugadas(data.mis_jugadas, extraidos, data.estado);
                pintarRanking(data.ranking, extraidos, data.estado);

                if (data.estado === 'finalizado' && estadoAnterior === 'en_vivo') confetti();

                ultimoConteo = extraidos.length;
                estadoAnterior = data.estado;
            }

            async function consultar() {
                clearTimeout(temporizador);
                let espera = 15000;
                try {
                    const res = await fetch(URL_ESTADO, { headers: { 'Accept': 'application/json' } });
                    if (res.status === 401 || res.status === 419) { location.reload(); return; }
                    if (res.ok) {
                        const data = await res.json();
                        render(data);
                        espera = data.estado === 'en_vivo' ? 3000 : (data.estado === 'esperando' ? 10000 : 30000);
                    }
                } catch (e) { /* sin conexión: se reintenta */ }
                if (!document.hidden) temporizador = setTimeout(consultar, espera);
            }

            // Al volver a la pestaña se actualiza al instante; en segundo plano no se consulta
            document.addEventListener('visibilitychange', () => { if (!document.hidden) consultar(); });

            consultar();
        })();
    </script>
</x-app-layout>
