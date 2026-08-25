// ==========================================
// 1. SELECCIÓN DE ELEMENTOS DEL DOM
// ==========================================
const btnCart = document.querySelector('#cart-icon');
const containerCartProducts = document.querySelector('#cart-container');
const rowProduct = document.querySelector('#row-product');
const valorTotal = document.querySelector('.total-pagar');
const valorSubtotal = document.querySelector('.subtotal-valor'); 
const contadorProductos = document.querySelector('#contador-productos');
const listaProductos = document.querySelector('#lista-productos');
const btnCerrarCarrito = document.querySelector('#cerrar-carrito');

// Elementos del checkout / pago (Declarados correctamente)
const selectMetodoPago = document.getElementById('metodo-pago');
const contenedorMonto = document.getElementById('contenedor-monto');
const montoConfirmado = document.getElementById('monto-confirmado');
const contenedorCuenta = document.getElementById('contenedor-cuenta');
const labelCuenta = document.getElementById('label-cuenta');
const inputNumeroCuenta = document.getElementById('numero-cuenta');
const bannerEstadoPago = document.getElementById('banner-estado-pago');
const tituloDestino = document.getElementById('titulo-destino');
const numeroDestino = document.getElementById('numero-destino');

// Variable global para mantener el total numérico calculado
let totalDineroGlobal = 0;

// 🔒 LEER PERSISTENCIA DESDE LOCALSTORAGE
let productosCarrito = JSON.parse(localStorage.getItem('carrito_tienda')) || [];


// ==========================================
// 2. MOSTRAR / OCULTAR CARRITO
// ==========================================
if (btnCart && containerCartProducts) {
    btnCart.addEventListener('click', () => {
        containerCartProducts.classList.toggle('hidden-cart');
    });
}

if (btnCerrarCarrito && containerCartProducts) {
    btnCerrarCarrito.addEventListener('click', () => {
        containerCartProducts.classList.add('hidden-cart');
    });
}


// ==========================================
// 3. CONTROL Y DESPLIEGUE DE MÉTODOS DE PAGO
// ==========================================
if (selectMetodoPago) {
    selectMetodoPago.addEventListener('change', (e) => {
        const metodo = e.target.value;
        const formatoMoneda = `$${totalDineroGlobal.toLocaleString('es-CO')}`;

        // Resetear la visibilidad de los bloques
        if (contenedorMonto) contenedorMonto.style.display = 'none';
        if (contenedorCuenta) contenedorCuenta.style.display = 'none';
        if (bannerEstadoPago) bannerEstadoPago.style.display = 'none';

        if (metodo === 'Nequi' || metodo === 'Daviplata') {
            // Mostrar resumen del monto a transferir
            if (contenedorMonto && montoConfirmado) {
                montoConfirmado.innerText = formatoMoneda;
                contenedorMonto.style.display = 'block';
            }

            // Mostrar campo para ingresar su número
            if (contenedorCuenta) {
                contenedorCuenta.style.display = 'block';
                if (labelCuenta) {
                    labelCuenta.innerText = `📱 INGRESA TU NÚMERO DE CELULAR DE (${metodo.toUpperCase()}):`;
                }
            }

            // Configurar datos destino de la tienda
            if (tituloDestino && numeroDestino) {
                if (metodo === 'Nequi') {
                    tituloDestino.innerText = '📱 CUENTA NEQUI STORE DANY:';
                    numeroDestino.innerText = '300 123 4567';
                } else {
                    tituloDestino.innerText = '💬 CUENTA DAVIPLATA STORE DANY:';
                    numeroDestino.innerText = '310 987 6543';
                }
            }

            // Mostrar u ocultar banner si ya hay un número ingresado
            if (inputNumeroCuenta && inputNumeroCuenta.value.trim().length >= 7) {
                if (bannerEstadoPago) bannerEstadoPago.style.display = 'block';
            }

        } else if (metodo === 'Contraentrega') {
            // Mostrar confirmación de monto a pagar al repartidor
            if (contenedorMonto && montoConfirmado) {
                montoConfirmado.innerText = formatoMoneda;
                contenedorMonto.style.display = 'block';
            }
        }
    });
}

// Escuchar la escritura del número de celular en vivo
if (inputNumeroCuenta) {
    inputNumeroCuenta.addEventListener('input', (e) => {
        const metodoActual = selectMetodoPago ? selectMetodoPago.value : '';
        if ((metodoActual === 'Nequi' || metodoActual === 'Daviplata') && e.target.value.trim().length >= 7) {
            if (bannerEstadoPago) bannerEstadoPago.style.display = 'block';
        } else {
            if (bannerEstadoPago) bannerEstadoPago.style.display = 'none';
        }
    });
}


// ==========================================
// 4. AGREGAR PRODUCTOS DESDE EL CATÁLOGO
// ==========================================
if (listaProductos) {
    listaProductos.addEventListener('click', e => {
        if (e.target.classList.contains('btn-add-cart')) {
            const product = e.target.closest('.card-producto');
            if (!product) return;

            const selectorTalla = product.querySelector('.talla-seleccion');
            if (!selectorTalla || !selectorTalla.value) {
                alert('Por favor selecciona tu talla antes de agregar al carrito 👟');
                const pills = product.querySelectorAll('.talla-pill');
                if (pills.length) {
                    pills.forEach(p => { p.style.transition = 'transform 0.15s'; });
                    let n = 0;
                    const temblor = setInterval(() => {
                        pills.forEach(p => { p.style.transform = (n % 2 === 0) ? 'translateX(-3px)' : 'translateX(3px)'; });
                        if (++n > 5) { clearInterval(temblor); pills.forEach(p => { p.style.transform = ''; }); }
                    }, 90);
                }
                return;
            }

            const rutaLimpia = product.querySelector('.img-tenis').getAttribute('src').replace(/\\/g, '/');

            const infoProduct = {
                cantidad: 1,
                titulo: product.querySelector('h3').innerText,
                precio: product.querySelector('.price-tag').innerText,
                talla: product.querySelector('.talla-seleccion').value,
                color: product.querySelector('.color-info').innerText.replace(/color:/i, '').trim(),
                imagen: encodeURI(rutaLimpia),
                marca: (() => { const t = product.querySelector('h3').innerText.toUpperCase(); if (t.includes('PUMA')) return 'Puma'; if (t.includes('NIKE') || t.includes('AIR')) return 'Nike'; if (t.includes('ADIDAS') || t.includes('ULTRABOOST') || t.includes('SUPERSTAR')) return 'Adidas'; if (t.includes('NEW BALANCE') || t.includes('574') || t.includes('530')) return 'New Balance'; if (t.includes('REEBOK')) return 'Reebok'; return 'Store Dany'; })()
            };
            
            const existe = productosCarrito.some(p => 
                p.titulo === infoProduct.titulo && 
                p.talla === infoProduct.talla && 
                p.color === infoProduct.color
            );
            
            if (existe) {
                productosCarrito = productosCarrito.map(p => {
                    if (p.titulo === infoProduct.titulo && p.talla === infoProduct.talla && p.color === infoProduct.color) {
                        p.cantidad++;
                    }
                    return p;
                });
            } else {
                productosCarrito = [...productosCarrito, infoProduct];
            }

            actualizarCarritoHTML();
        }
    });
}


// ==========================================
// 5. ELIMINAR PRODUCTO INDIVIDUAL (BOTÓN X)
// ==========================================
if (rowProduct) {
    rowProduct.addEventListener('click', e => {
        if (e.target.classList.contains('icon-close') || e.target.closest('.icon-close')) {
            const btnBorrar = e.target.closest('.icon-close');
            const titulo = btnBorrar.getAttribute('data-titulo');
            const talla = btnBorrar.getAttribute('data-talla');

            productosCarrito = productosCarrito.filter(p => !(p.titulo === titulo && p.talla === talla));
            actualizarCarritoHTML();
        }
    });
}


// ==========================================
// 6. RENDERIZAR CARRITO Y RECALCULAR TOTALES
// ==========================================
const actualizarCarritoHTML = () => {
    if (!rowProduct) return;

    rowProduct.innerHTML = '';
    let totalDinero = 0;
    let totalItems = 0;
    
    productosCarrito.forEach(product => {
        const containerProduct = document.createElement('div');
        containerProduct.classList.add('cart-product-item'); 

        containerProduct.innerHTML = `
            <div class="cart-item-info">
                <p class="cart-item-title">${product.cantidad}x ${product.titulo}</p>
                <small class="cart-item-details">Talla: ${product.talla} | Color: ${product.color}</small>
                <span class="cart-item-price">${product.precio}</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="icon-close" data-titulo="${product.titulo}" data-talla="${product.talla}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        `;
        rowProduct.append(containerProduct);

        const precioLimpio = parseInt(product.precio.replace('$', '').replace(/\./g, '').trim());
        totalDinero += product.cantidad * precioLimpio;
        totalItems += product.cantidad;
    });

    totalDineroGlobal = totalDinero;
    const formatoMoneda = `$${totalDinero.toLocaleString('es-CO')}`;

    if (valorTotal) valorTotal.innerText = formatoMoneda;
    if (valorSubtotal) valorSubtotal.innerText = formatoMoneda;
    if (contadorProductos) contadorProductos.innerText = totalItems;
    if (montoConfirmado) montoConfirmado.innerText = formatoMoneda;

    // Persistir en LocalStorage
    localStorage.setItem('carrito_tienda', JSON.stringify(productosCarrito));
};


// ==========================================
// 7. VACIAR CARRITO
// ==========================================
const btnVaciar = document.querySelector('#vaciar-carrito');
if (btnVaciar) {
    btnVaciar.addEventListener('click', () => {
        productosCarrito = [];
        localStorage.setItem('carrito_tienda', JSON.stringify(productosCarrito));
        
        if (contenedorMonto) contenedorMonto.style.display = 'none';
        if (contenedorCuenta) contenedorCuenta.style.display = 'none';
        if (bannerEstadoPago) bannerEstadoPago.style.display = 'none';
        if (selectMetodoPago) selectMetodoPago.value = "";
        if (inputNumeroCuenta) inputNumeroCuenta.value = "";
        
        actualizarCarritoHTML();
    });
}


// ==========================================
// 8. PROCESAR Y ENVIAR COMPRA A PHP
// ==========================================
const btnFinalizar = document.getElementById('finalizar-compra');
if (btnFinalizar) {
    btnFinalizar.addEventListener('click', () => {
        // 0. Verificar que el cliente registro sus datos previamente
        let clienteRegistrado = null;
        try { clienteRegistrado = JSON.parse(localStorage.getItem("cliente_dany")); } catch (e) {}
        if (!clienteRegistrado || !clienteRegistrado.id) {
            alert("Para realizar tu compra primero debes registrar tus datos personales. Te llevamos al registro.");
            window.location.href = "personal-data.html";
            return;
        }
        // 1. Validar carrito vacío
        if (productosCarrito.length === 0) {
            alert("Tu carrito está vacío. Agrega un producto para comprar.");
            return;
        }

        // 2. Validar selección de método de pago
        const metodoPagoSeleccionado = selectMetodoPago ? selectMetodoPago.value : "";
        if (!metodoPagoSeleccionado) {
            alert("Por favor, selecciona un método de pago antes de continuar.");
            if (selectMetodoPago) selectMetodoPago.focus();
            return;
        }

        // 3. Validar número de cuenta para pagos digitales
        let numeroCuentaCliente = "";
        if (metodoPagoSeleccionado === 'Nequi' || metodoPagoSeleccionado === 'Daviplata') {
            numeroCuentaCliente = inputNumeroCuenta ? inputNumeroCuenta.value.trim() : "";
            if (numeroCuentaCliente.length < 7) {
                alert(`Por favor ingresa tu número de celular registrado en ${metodoPagoSeleccionado}.`);
                if (inputNumeroCuenta) inputNumeroCuenta.focus();
                return;
            }
        }

        // 4. Preparar payload de datos
        const datosCompra = {
            productos: productosCarrito,
            metodo_pago: metodoPagoSeleccionado,
            total_compra: totalDineroGlobal,
            numero_cuenta_cliente: numeroCuentaCliente,
            id_cliente: (function () { try { var c = JSON.parse(localStorage.getItem("cliente_dany")); return c ? c.id : null; } catch (e) { return null; } })()
        };

        fetch('procesar_compra.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datosCompra) 
        })
        .then(res => res.text())
        .then(data => {
            // Limpiar memoria al completar la compra
            productosCarrito = [];
            localStorage.setItem('carrito_tienda', JSON.stringify(productosCarrito));
            
            // Renderizar la respuesta del servidor (recibo)
            document.body.innerHTML = data;
        })
        .catch(error => {
            console.error('Error al procesar la compra:', error);
            alert('Hubo un error al conectar con el servidor.');
        });
    });
}


// ==========================================
// 9. INICIALIZACIÓN Y MODAL DE IMÁGENES
// ==========================================
actualizarCarritoHTML();

function abrirVisor(contenedor) {
    const visor = document.getElementById("visorImagen");
    if (!visor) return;
    const card = contenedor.closest(".card-producto");
    if (!card) return;

    document.getElementById("visorImgElemento").src = card.querySelector(".img-tenis").src;
    document.getElementById("visorTextoElemento").innerText = card.querySelector("h3").innerText;
    document.getElementById("visorPrecioElemento").innerText = card.querySelector(".price-tag").innerText;
    const colorEl = card.querySelector(".color-info");
    document.getElementById("visorColorElemento").innerText = colorEl ? colorEl.innerText : "";
    const marcaEl = document.getElementById("visorMarcaElemento");
    if (marcaEl) marcaEl.innerText = card.dataset.marca || "";

    const contTallas = document.getElementById("visorTallas");
    contTallas.innerHTML = "";
    const selector = card.querySelector(".talla-seleccion");
    const valorActual = selector ? selector.value : "";
    card.querySelectorAll(".talla-pill").forEach(pill => {
        const clon = pill.cloneNode(true);
        clon.classList.remove("activa");
        if (clon.dataset.talla === valorActual) clon.classList.add("activa");
        clon.addEventListener('click', () => {
            contTallas.querySelectorAll(".talla-pill").forEach(x => x.classList.remove("activa"));
            clon.classList.add("activa");
        });
        contTallas.appendChild(clon);
    });

    document.getElementById("visorAgregarBtn").onclick = function () {
        const elegida = contTallas.querySelector(".talla-pill.activa");
        if (!elegida) { alert("Por favor selecciona tu talla antes de agregar al carrito"); return; }
        if (selector) selector.value = elegida.dataset.talla;
        card.querySelectorAll(".talla-pill").forEach(p => p.classList.toggle("activa", p.dataset.talla === elegida.dataset.talla));
        cerrarVisor();
        const btnReal = card.querySelector(".btn-add-cart");
        if (btnReal) btnReal.click();
    };

    visor.style.display = "flex";
}

function cerrarVisor() {
    const visor = document.getElementById("visorImagen");
    if (visor) {
        visor.style.display = "none";
    }
}

window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        cerrarVisor();
    }
});