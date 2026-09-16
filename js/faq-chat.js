/* ============================================================
   STORE DANY · Burbuja de ayuda con preguntas frecuentes
   Creada por JS y compartida por todas las páginas del sitio.
   Para editar las preguntas, cambia el arreglo FAQS (abajo).
   ============================================================ */
(function () {
    'use strict';

    var FAQS = [
        {
            q: '🚚 ¿Cuánto tarda en llegar mi pedido?',
            a: 'En aproximación a 8 días calendario llega tu pedido. Al momento de realizar la compra, la factura procesa la compra realizada y te mostrará la fecha en que te llegará. Por eso es recomendable que descargues la factura, para que tengas la información más fácil a la mano.'
        },
        {
            q: '🧾 ¿Cómo obtengo mi factura?',
            a: 'Al momento de finalizar la compra, dando clic en "Procesar compra segura", el sistema te muestra la factura completa con tus datos, los detalles de los productos seleccionados, el monto y el método de pago. Al final encuentras el botón de imprimir para descargarla en PDF.'
        },
        {
            q: '💳 ¿Cómo confirmo mi pago por Nequi o Daviplata?',
            a: 'El sistema te indica la cuenta a la que debes hacer la confirmación del pago, para que el proceso lo veas más seguro.'
        },
        {
            q: '🔄 ¿Cómo hago una garantía o devolución?',
            a: 'Para el procedimiento en caso de presentarse cualquiera de estos dos asuntos, escribe por el chat online, por WhatsApp o en el campo de dudas e inquietudes. Desplega el asunto de garantía o devolución, define a qué se debe y explica por qué lo estás realizando.'
        },
        {
            q: '🔁 ¿Puedo cambiar de talla si no me quedó?',
            a: 'Sí, claro. En el chat online hay un campo para ingresar tus datos; eliges en el asunto la opción "Devolución" y notificas cuál es el motivo.'
        },
        {
            q: '💬 ¿Puedo hablar directo con la asesora?',
            a: 'Sí, claro. Al momento de realizar la asesoría por medio de WhatsApp, ella se comunicará contigo justo cuando notifiques tu mensaje.'
        }
    ];

    var btn = document.createElement('button');
    btn.id = 'faq-chat-btn';
    btn.type = 'button';
    btn.setAttribute('aria-label', 'Abrir ayuda y preguntas frecuentes');
    btn.innerHTML = '<span class="faq-chat-pulso"></span>💬';

    var panel = document.createElement('div');
    panel.id = 'faq-chat-panel';

    panel.innerHTML =
        '<div class="faq-chat-cabecera">' +
            '<span class="faq-chat-logo">👟</span>' +
            '<div>' +
                '<p class="faq-chat-titulo">Ayuda rápida</p>' +
                '<span class="faq-chat-subtitulo">Preguntas frecuentes STORE DANY</span>' +
            '</div>' +
            '<button type="button" id="faq-chat-cerrar" aria-label="Cerrar">✕</button>' +
        '</div>' +
        '<div class="faq-chat-cuerpo"></div>' +
        '<div class="faq-chat-pie">' +
            '<p class="faq-chat-pie-texto">¿No encontraste tu duda? Escríbenos directo 👇</p>' +
            '<div class="faq-chat-botones">' +
                '<a class="faq-chat-boton faq-chat-boton-wa" href="https://wa.me/573123555400" target="_blank" rel="noopener">💬 WhatsApp</a>' +
                '<a class="faq-chat-boton faq-chat-boton-form" href="contactar.html">📝 Escríbenos</a>' +
            '</div>' +
        '</div>';

    var cuerpo = panel.querySelector('.faq-chat-cuerpo');
    FAQS.forEach(function (f) {
        var item = document.createElement('div');
        item.className = 'faq-chat-item';
        item.innerHTML =
            '<button type="button" class="faq-chat-pregunta">' +
                '<span>' + f.q + '</span>' +
                '<span class="faq-chat-flecha">▶</span>' +
            '</button>' +
            '<div class="faq-chat-respuesta">' + f.a + '</div>';
        item.querySelector('.faq-chat-pregunta').addEventListener('click', function () {
            var estabaAbierto = item.classList.contains('abierto');
            var abiertos = cuerpo.querySelectorAll('.faq-chat-item.abierto');
            for (var i = 0; i < abiertos.length; i++) {
                abiertos[i].classList.remove('abierto');
            }
            if (!estabaAbierto) item.classList.add('abierto');
        });
        cuerpo.appendChild(item);
    });

    document.body.appendChild(btn);
    document.body.appendChild(panel);

    btn.addEventListener('click', function () {
        panel.classList.toggle('abierto');
    });

    panel.querySelector('#faq-chat-cerrar').addEventListener('click', function () {
        panel.classList.remove('abierto');
    });
})();