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

    // Acordeón interactivo de las tarjetas de compromisos (portada)
    function activarAcordeones() {
        var tarjetas = document.querySelectorAll('.tarjeta-acordeon');
        tarjetas.forEach(function (tarjeta) {
            var cabeza = tarjeta.querySelector('.acordeon-cabeza');
            if (!cabeza) return;
            cabeza.addEventListener('click', function (e) {
                if (e.target.closest('a')) return;
                var abierta = tarjeta.classList.toggle('abierta');
                cabeza.setAttribute('aria-expanded', abierta ? 'true' : 'false');
            });
            cabeza.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    cabeza.click();
                }
            });
        });
    }

    window.addEventListener('load', function () {
        agregarAnimaciones();
        agregarRevelado();
        activarAcordeones();
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
            'tus datos personales se mantienen protegidos, en estricta privacidad y conforme a la Ley 1581 de 2012 ' +
            '(Protección de Datos Personales en Colombia); y cada compra cuenta ' +
            'con respaldo, seguimiento y garantía real.</p>' +
            '<p class="terms-destacado">Solo aceptando podrás acceder a la información del negocio, ' +
            'registrar tus datos y navegar por el sitio con total tranquilidad y confianza.</p>' +
            '<div class="terms-sellos">' +
            '<div class="terms-sello"><span class="terms-sello-icono"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg></span><span>Pago seguro</span></div>' +
            '<div class="terms-sello"><span class="terms-sello-icono"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-3.75"/></svg></span><span>Envío protegido</span></div>' +
            '<div class="terms-sello"><span class="terms-sello-icono"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg></span><span>Datos privados</span></div>' +
            '</div>' +
            '<label class="terms-check">' +
            '<input type="checkbox" id="checkTerminosWall">' +
            '<span class="terms-check-caja" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg></span>' +
            '<span>He leído y acepto los términos y condiciones descritos anteriormente.</span>' +
            '</label>' +
            '<div class="terms-scroll-ayuda">↓ Lee y desplázate hasta el final del texto</div>' +
            '<button id="btnAceptarWall" type="button" class="btn-acepto" disabled>ACEPTO Y ACCEDO CON TOTAL CONFIANZA</button>' +
            '<p class="terms-pie">STORE DANY · Santander, Colombia 🇨🇴 · Envíos a todo el país</p>';

        muro.appendChild(tarjeta);
        document.body.appendChild(muro);
        document.body.classList.add('site-lockeado');

        // 3. Habilitar el botón únicamente cuando: casilla marcada Y texto leído hasta el final
        var check = document.getElementById('checkTerminosWall');
        var btn = document.getElementById('btnAceptarWall');
        var ayuda = tarjeta.querySelector('.terms-scroll-ayuda');
        var leidoCompleto = false;

        function evaluarBoton() {
            var listo = check.checked && leidoCompleto;
            btn.disabled = !listo;
            btn.classList.toggle('disabled', !listo);
            if (ayuda) {
                ayuda.style.display = listo ? 'none' : '';
                ayuda.classList.toggle('ok', leidoCompleto);
            }
            ayuda.textContent = leidoCompleto
                ? '✓ Ya leíste las condiciones. Marca la casilla para continuar.'
                : '↓ Lee y desplázate hasta el final del texto';
        }

        function detectarScroll() {
            var limite = tarjeta.scrollTop + tarjeta.clientHeight >= tarjeta.scrollHeight - 8;
            if (limite && !leidoCompleto) {
                leidoCompleto = true;
                evaluarBoton();
            }
        }

        // Si el texto cabe sin scroll (pantallas grandes), se considera leído
        setTimeout(function () { detectarScroll(); }, 60);

        tarjeta.addEventListener('scroll', detectarScroll);
        check.addEventListener('change', evaluarBoton);

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

        var texto = 'Desde el corazón de Santander, te damos la bienvenida a STORE DANY 🚚 Envíos gratis a toda Colombia &nbsp;✦&nbsp; Gracias por confiar en nosotros &nbsp;✦&nbsp; ';
        pista.innerHTML = '<span class="barra-anuncio-texto">' + texto.repeat(6) + '</span>';

        barra.appendChild(pista);
        document.body.insertBefore(barra, document.body.firstChild);
        document.body.classList.add('tiene-barra-anuncio');
    }

    /* ---------- 6. NOTIFICACIONES ELEGANTES PARA EL CLIENTE ----------
       Reemplazan los alert() del navegador durante el proceso de compra.
       Tipos: 'falta' (requisito del cliente), 'error' (servidor) y
       'exito' (confirmación). Se exponen como storeDanyNotificar(). */
    function crearContenedorNotificaciones() {
        var cont = document.getElementById('store-dany-toasts');
        if (cont) return cont;
        cont = document.createElement('div');
        cont.id = 'store-dany-toasts';
        cont.className = 'store-dany-toasts';
        document.body.appendChild(cont);
        return cont;
    }

    function storeDanyNotificar(mensaje, tipo) {
        var cont = crearContenedorNotificaciones();
        var aviso = document.createElement('div');
        aviso.className = 'store-dany-notificacion ' + (tipo || 'falta');

        var icono = '⚠️';
        var titulo = 'Atención';
        if (tipo === 'error') { icono = '⛔'; titulo = 'Error en el proceso'; }
        else if (tipo === 'exito') { icono = '✅'; titulo = 'Todo listo'; }
        else if (tipo === 'falta') { icono = '👟'; titulo = 'Te falta un paso'; }

        aviso.innerHTML =
            '<div class="store-dany-notif-icono">' + icono + '</div>' +
            '<div class="store-dany-notif-contenido">' +
            '<span class="store-dany-notif-titulo">' + titulo + '</span>' +
            '<span class="store-dany-notif-mensaje">' + mensaje + '</span>' +
            '</div>' +
            '<button type="button" class="store-dany-notif-cerrar" aria-label="Cerrar">✕</button>';

        cont.appendChild(aviso);
        requestAnimationFrame(function () {
            requestAnimationFrame(function () { aviso.classList.add('visible'); });
        });

        function cerrar() {
            aviso.classList.remove('visible');
            setTimeout(function () {
                if (aviso.parentNode) aviso.parentNode.removeChild(aviso);
            }, 320);
        }

        aviso.querySelector('.store-dany-notif-cerrar').addEventListener('click', cerrar);
        aviso.contador = setTimeout(cerrar, 6000);
        aviso.addEventListener('mouseenter', function () { clearTimeout(aviso.contador); });
        aviso.addEventListener('mouseleave', function () { aviso.contador = setTimeout(cerrar, 2500); });
    }

    window.storeDanyNotificar = storeDanyNotificar;

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