/* ============================================================
   STORE DANY - Mejoras globales del sitio (main.js)
   Animaciones de scroll, botón volver arriba, WhatsApp flotante
   y navbar con efecto al desplazarse.
   ============================================================ */

(function () {
    'use strict';

    /* ---------- 1. NAVBAR CON EFECTO AL HACER SCROLL ---------- */
    var navbar = document.querySelector('.main-navbar');
    function navbarScroll() {
        if (!navbar) return;
        if (window.scrollY > 60) {
            navbar.classList.add('navbar-shrunk');
        } else {
            navbar.classList.remove('navbar-shrunk');
        }
    }
    navbarScroll();
    window.addEventListener('scroll', navbarScroll, { passive: true });

    /* ---------- 2. ANIMACIONES DE APARICIÓN AL HACER SCROLL ---------- */
    function agregarAnimaciones() {
        var elementos = document.querySelectorAll('.animate-fade-in');
        if (!('IntersectionObserver' in window)) return;
        var observer = new IntersectionObserver(function (entradas) {
            entradas.forEach(function (entrada) {
                if (entrada.isIntersecting) {
                    entrada.target.classList.add('scroll-animado');
                    observer.unobserve(entrada.target);
                }
            });
        }, { threshold: 0.12 });
        elementos.forEach(function (el) {
            if (!el.classList.contains('scroll-animado')) {
                observer.observe(el);
            }
        });
    }

    // Soporte para el nuevo sistema de revelado "data-reveal"
    // y animación automática de las tarjetas de producto (.item)
    function agregarRevelado() {
        var selectores = '[data-reveal], .item, .tarjeta-dinamica';
        var elementos = document.querySelectorAll(selectores);
        if (!('IntersectionObserver' in window)) {
            elementos.forEach(function (el) { el.classList.add('reveal-visible'); });
            return;
        }
        var observer = new IntersectionObserver(function (entradas) {
            entradas.forEach(function (entrada) {
                if (entrada.isIntersecting) {
                    entrada.target.classList.add('reveal-visible');
                    observer.unobserve(entrada.target);
                }
            });
        }, { threshold: 0.1 });
        elementos.forEach(function (el) {
            if (!el.classList.contains('reveal-visible')) {
                observer.observe(el);
            }
        });
    }

    window.addEventListener('load', function () {
        agregarAnimaciones();
        agregarRevelado();
    });

    /* ---------- 3. BOTÓN VOLVER ARRIBA ---------- */
    function crearBotonVolverArriba() {
        var btn = document.createElement('button');
        btn.id = 'btn-volver-arriba';
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Volver arriba');
        btn.title = 'Volver arriba';
        btn.innerHTML = '↑';
        document.body.appendChild(btn);

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        function toggleBtn() {
            if (window.scrollY > 450) {
                btn.classList.add('visible');
            } else {
                btn.classList.remove('visible');
            }
        }
        toggleBtn();
        window.addEventListener('scroll', toggleBtn, { passive: true });
    }

    
    /* ---------- 5. MURO DE TÉRMINOS Y CONDICIONES ----------
       Bloquea el acceso al menú y a los datos personales hasta
       que el cliente marque la casilla y pulse "Aceptar".
       Se muestra una sola vez por sesión de navegación. */
    var CLAVE_ACEPTACION = 'store_dany_terminos_aceptados';

    function crearMuroTerminos() {
        // Si el cliente ya aceptó en esta sesión, no volvemos a bloquear.
        try {
            if (sessionStorage.getItem(CLAVE_ACEPTACION) === '1') return;
        } catch (e) { /* sin almacenamiento */ }

        // 1. Fondo oscuro que cubre toda la pantalla (bloquea menú y navegación)
        var muro = document.createElement('div');
        muro.id = 'termsWall';
        muro.className = 'terms-wall';
        muro.setAttribute('role', 'dialog');
        muro.setAttribute('aria-modal', 'true');
        muro.setAttribute('aria-labelledby', 'termsWallTitulo');

        // 2. Tarjeta central con los términos
        var tarjeta = document.createElement('div');
        tarjeta.className = 'terms-card';
        tarjeta.innerHTML =
            '<img src="logotipo.png" alt="Logotipo STORE DANY" class="terms-logo">' +
            '<h2 id="termsWallTitulo">STORE DANY</h2>' +
            '<p class="terms-subtitulo">Términos y Condiciones del Servicio</p>' +
            '<p class="terms-texto">Al continuar, confirmas que has leído y aceptas de forma voluntaria ' +
            'nuestras condiciones de servicio: tu pedido será verificado y gestionado con total seriedad; ' +
            'tus datos personales se mantienen protegidos y en estricta privacidad; y cada compra cuenta ' +
            'con respaldo, seguimiento y garantía real.</p>' +
            '<p class="terms-destacado">Solo aceptando podrás acceder a la información del negocio, ' +
            'registrar tus datos y navegar por el sitio con total tranquilidad y confianza.</p>' +
            '<div class="terms-sellos">' +
            '<div class="terms-sello"><span class="terms-sello-icono">🔒</span><span>Pago seguro</span></div>' +
            '<div class="terms-sello"><span class="terms-sello-icono">🚚</span><span>Envío protegido</span></div>' +
            '<div class="terms-sello"><span class="terms-sello-icono">🛡️</span><span>Datos privados</span></div>' +
            '</div>' +
            '<label class="terms-check">' +
            '<input type="checkbox" id="checkTerminosWall">' +
            '<span>He leído y acepto los términos y condiciones descritos anteriormente.</span>' +
            '</label>' +
            '<button id="btnAceptarWall" type="button" class="btn-acepto" disabled>ACEPTO Y ACCEDO CON TOTAL CONFIANZA</button>';

        muro.appendChild(tarjeta);
        document.body.appendChild(muro);
        document.body.classList.add('site-lockeado');

        // 3. Habilitar el botón únicamente cuando se marque la casilla
        var check = document.getElementById('checkTerminosWall');
        var btn = document.getElementById('btnAceptarWall');

        check.addEventListener('change', function () {
            var aceptado = check.checked;
            btn.disabled = !aceptado;
            btn.classList.toggle('disabled', !aceptado);
        });

        // 4. Al aceptar: desbloquear el sitio y ocultar el muro
        btn.addEventListener('click', function () {
            if (!check.checked) return;
            try {
                sessionStorage.setItem(CLAVE_ACEPTACION, '1');
            } catch (e) { /* sin almacenamiento */ }
            document.body.classList.remove('site-lockeado');
            muro.classList.add('terms-wall-oculto');
            setTimeout(function () {
                if (muro.parentNode) muro.parentNode.removeChild(muro);
            }, 500);
        });
    }

    /* ---------- 4. BOTÓN FLOTANTE DE WHATSAPP ---------- */
    function crearWhatsAppFlotante() {
        var paginasMarca = ['nike.html','adidas.html','puma.html','reebok.html','new-balance.html'];
        var actual = window.location.pathname.split('/').pop().toLowerCase();
        if (paginasMarca.indexOf(actual) !== -1) return;
        var enlace = document.createElement('a');
        enlace.id = 'btn-whatsapp-flotante';
        enlace.href = 'https://wa.me/573123555400?text=' + encodeURIComponent('Hola Store Dany 👟, quiero información sobre sus calzados');
        enlace.target = '_blank';
        enlace.rel = 'noopener';
        enlace.innerHTML = '<span>💬</span><span>Escríbenos</span>';
        document.body.appendChild(enlace);
    }

    /* ---------- 5. BARRA ANUNCIANTE: ENVÍOS GRATIS ---------- */
    function crearBarraEnvioGratis() {
        var barra = document.createElement('div');
        barra.id = 'barra-envio-gratis';

        var pista = document.createElement('div');
        pista.className = 'barra-anuncio-pista';

        var texto = '🚚 ENVÍOS GRATIS A TODA COLOMBIA &nbsp;✦&nbsp; ¡GRACIAS POR CONFIAR EN NOSOTROS! &nbsp;✦&nbsp; ';
        pista.innerHTML = '<span class="barra-anuncio-texto">' + texto.repeat(6) + '</span>';

        barra.appendChild(pista);
        document.body.insertBefore(barra, document.body.firstChild);
        document.body.classList.add('tiene-barra-anuncio');
    }

    /* ---------- 6. INICIALIZACIÓN ---------- */

    /* ---------- SELECTOR VISUAL DE TALLAS ---------- */
    document.addEventListener('click', function (evento) {
        var pill = evento.target.closest('.talla-pill');
        if (!pill) return;
        var grupo = pill.closest('.card-producto');
        if (!grupo) return;
        grupo.querySelectorAll('.talla-pill').forEach(function (p) { p.classList.remove('activa'); });
        pill.classList.add('activa');
        var selector = grupo.querySelector('.talla-seleccion');
        if (selector) selector.value = pill.getAttribute('data-talla');
    });

    document.addEventListener('DOMContentLoaded', function () {
        crearBarraEnvioGratis();
        crearMuroTerminos();
        crearBotonVolverArriba();
        crearWhatsAppFlotante();
    });
})();