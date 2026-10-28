// Locutor del sorteo: lee en voz alta los números y la publicidad con la voz del navegador (sin costo).
// Prefiere una voz femenina en español de Perú; si el dispositivo no la tiene, usa la más parecida
// (español latino → cualquier español).
(function () {
    var CLAVE = 'billetazo:voz';
    var sintesis = window.speechSynthesis;
    var voz = null;

    var UNIDADES = ['cero', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
        'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve',
        'veinte', 'veintiuno', 'veintidós', 'veintitrés', 'veinticuatro', 'veinticinco', 'veintiséis',
        'veintisiete', 'veintiocho', 'veintinueve'];
    var DECENAS = { 3: 'treinta', 4: 'cuarenta', 5: 'cincuenta', 6: 'sesenta', 7: 'setenta', 8: 'ochenta', 9: 'noventa' };

    function enPalabras(n) {
        n = parseInt(n, 10);
        if (n < 30) return UNIDADES[n];
        if (n < 100) {
            var d = DECENAS[Math.floor(n / 10)];
            return n % 10 ? d + ' y ' + UNIDADES[n % 10] : d;
        }
        return String(n);
    }

    var FEMENINAS = /camila|paulina|sabina|dalia|helena|laura|m[oó]nica|luc[ií]a|elvira|paloma|ximena|valentina|renata|larissa|marisol|soledad|isabela|elena|esperanza|catalina|francisca|google espa[nñ]ol/i;
    var MASCULINAS = /alex|jorge|diego|juan|pablo|ra[uú]l|[aá]lvaro|enrique|gerardo|tom[aá]s|carlos|alonso|andr[eé]s|federico|lorenzo|male/i;

    function puntaje(v) {
        var idioma = (v.lang || '').toLowerCase().replace('_', '-');
        if (idioma.indexOf('es') !== 0) return -1;
        var p = 1;
        if (idioma === 'es-pe') p += 50;
        else if (/^es-(419|us|mx|co|cl|ar|ec|bo|ve)$/.test(idioma)) p += 30;
        if (FEMENINAS.test(v.name)) p += 20;
        if (MASCULINAS.test(v.name)) p -= 25;
        if (/natural|neural|online|premium|enhanced/i.test(v.name)) p += 10;
        if (v.localService) p += 2;
        return p;
    }

    function elegirVoz() {
        if (!sintesis) return;
        var voces = sintesis.getVoices().filter(function (v) { return puntaje(v) >= 0; });
        voces.sort(function (a, b) { return puntaje(b) - puntaje(a); });
        voz = voces[0] || null;
    }

    if (sintesis) {
        elegirVoz();
        if (typeof sintesis.addEventListener === 'function') sintesis.addEventListener('voiceschanged', elegirVoz);
        else sintesis.onvoiceschanged = elegirVoz;
    }

    function activo() {
        try { return localStorage.getItem(CLAVE) !== 'off'; } catch (e) { return true; }
    }

    function hablar(texto, opciones) {
        opciones = opciones || {};
        return new Promise(function (resolver) {
            if (!sintesis || !texto || (!opciones.forzar && !activo())) return resolver();
            if (opciones.interrumpir !== false) sintesis.cancel();
            var u = new SpeechSynthesisUtterance(texto);
            u.lang = voz ? voz.lang : 'es-PE';
            if (voz) u.voice = voz;
            u.rate = opciones.velocidad || 0.95;
            u.pitch = 1.05;
            u.onend = u.onerror = function () { resolver(); };
            sintesis.speak(u);
            // Algunos celulares nunca disparan onend: no dejamos la pantalla esperando
            setTimeout(resolver, Math.max(4000, texto.length * 120));
        });
    }

    window.Locutor = {
        disponible: !!sintesis,
        enPalabras: enPalabras,
        hablar: hablar,
        activo: activo,
        // "¡Diecinueve!"; con orden: "Bolilla número tres... ¡diecinueve!"
        cantarNumero: function (numero, posicion) {
            var palabras = enPalabras(numero);
            var texto = posicion ? 'Bolilla número ' + enPalabras(posicion) + '... ¡' + palabras + '!' : '¡' + palabras + '!';
            return hablar(texto);
        },
        callar: function () { if (sintesis) sintesis.cancel(); },
        // Los celulares solo permiten hablar después de que la persona toca la pantalla una vez
        desbloquear: function () {
            if (!sintesis) return;
            var u = new SpeechSynthesisUtterance(' ');
            u.volume = 0;
            sintesis.speak(u);
        },
        activar: function (si) {
            try { localStorage.setItem(CLAVE, si ? 'on' : 'off'); } catch (e) {}
            if (si) this.desbloquear(); else this.callar();
        },
        nombreVoz: function () { return voz ? voz.name + ' (' + voz.lang + ')' : 'voz por defecto'; },
    };
})();
