// Registro del service worker y botón "Instalar app".
// Cualquier elemento con el atributo data-instalar-app funciona como botón de instalación.
(function () {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }

    var instalada = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    var esIOS = /iphone|ipad|ipod/i.test(navigator.userAgent)
        || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    var eventoInstalacion = null;

    function botones() {
        return document.querySelectorAll('[data-instalar-app]');
    }

    function mostrarBotones(visible) {
        botones().forEach(function (boton) {
            boton.classList.toggle('hidden', !visible);
        });
    }

    // Chrome / Edge / Android: el navegador avisa cuando la app se puede instalar
    window.addEventListener('beforeinstallprompt', function (evento) {
        evento.preventDefault();
        eventoInstalacion = evento;
        mostrarBotones(true);
    });

    window.addEventListener('appinstalled', function () {
        eventoInstalacion = null;
        mostrarBotones(false);
    });

    function instrucciones(titulo, pasos) {
        var fondo = document.createElement('div');
        fondo.setAttribute('role', 'dialog');
        fondo.setAttribute('aria-modal', 'true');
        fondo.style.cssText = 'position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.8);display:flex;align-items:flex-end;justify-content:center;padding:16px;';

        var caja = document.createElement('div');
        caja.style.cssText = 'width:100%;max-width:420px;background:#111827;color:#fff;border:2px solid #f59e0b;border-radius:16px;padding:20px;font-family:inherit;';

        var h = document.createElement('h3');
        h.textContent = titulo;
        h.style.cssText = 'margin:0 0 12px;color:#fbbf24;font-size:1.15rem;font-weight:700;';

        var lista = document.createElement('ol');
        lista.style.cssText = 'margin:0 0 16px;padding-left:20px;line-height:1.7;color:#e5e7eb;';
        pasos.forEach(function (paso) {
            var li = document.createElement('li');
            li.innerHTML = paso;
            lista.appendChild(li);
        });

        var cerrar = document.createElement('button');
        cerrar.type = 'button';
        cerrar.textContent = 'Entendido';
        cerrar.style.cssText = 'width:100%;background:#f59e0b;color:#000;border:0;border-radius:10px;padding:12px;font-weight:700;font-size:1rem;cursor:pointer;';

        function quitar() { fondo.remove(); }
        cerrar.addEventListener('click', quitar);
        fondo.addEventListener('click', function (e) { if (e.target === fondo) quitar(); });

        caja.append(h, lista, cerrar);
        fondo.appendChild(caja);
        document.body.appendChild(fondo);
        cerrar.focus();
    }

    document.addEventListener('click', function (evento) {
        var boton = evento.target.closest('[data-instalar-app]');
        if (!boton) return;
        evento.preventDefault();

        if (eventoInstalacion) {
            eventoInstalacion.prompt();
            eventoInstalacion.userChoice.finally(function () { eventoInstalacion = null; });
            return;
        }

        if (esIOS) {
            instrucciones('Instalar en tu iPhone', [
                'Abre esta página en <strong>Safari</strong>.',
                'Toca el botón <strong>Compartir</strong> <span aria-hidden="true">⬆️</span> (abajo en la pantalla).',
                'Elige <strong>"Agregar a pantalla de inicio"</strong>.',
                'Toca <strong>Agregar</strong>. ¡Listo! El Billetazo aparecerá con tus apps.',
            ]);
            return;
        }

        instrucciones('Instalar la app', [
            'Abre el menú del navegador <strong>⋮</strong> (arriba a la derecha).',
            'Toca <strong>"Instalar aplicación"</strong> o <strong>"Agregar a pantalla principal"</strong>.',
            'Confirma con <strong>Instalar</strong>.',
        ]);
    });

    // En celulares mostramos el botón aunque el navegador todavía no haya avisado (iPhone nunca avisa,
    // y algunos navegadores de Android tampoco): si no hay aviso, el botón explica cómo instalarla a mano.
    // Solo se puede instalar desde https (o localhost), por eso no se muestra en páginas sin candado.
    var esCelular = esIOS || /android|mobile/i.test(navigator.userAgent);

    document.addEventListener('DOMContentLoaded', function () {
        if (!instalada && window.isSecureContext && esCelular) mostrarBotones(true);
    });
})();
