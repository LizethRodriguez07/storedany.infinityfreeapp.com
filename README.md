# STORE DANY — Documentación del Proyecto

Tienda online de calzado para hombre. Desarrollada con un stack de código abierto: **PHP + MySQL (MariaDB)** en el backend, con **frontend en HTML, CSS y JavaScript vanilla** y **Bootstrap 4** como framework de estilos. El entorno de desarrollo corre con **Docker Compose sobre WSL** (`docker-compose.yml`): un contenedor **Apache con PHP 8.2** (servidor web, puerto 8080) y un contenedor **MariaDB 10.6** (base de datos, puerto 3306). La base de datos se llama **`gst_ventasonline`** (en el contenedor y también en la nube como `if0_41988386_gst_ventasonline` con InfinityFree). Enfoque del proyecto: **comercio electrónico (e-commerce)** tipo catálogo con registro de cliente por pedido, carrito de compras, checkout y panel logístico de despachos. Asimismo, el proyecto se apoya en una **infraestructura virtualizada y de redes**: el sistema base corre sobre **Linux**, la virtualización de equipos se realiza con **VMware Workstation Pro**, el modelado de las topologías de red se hace con **GNS3** y la conectividad entre máquinas virtuales se logra mediante la tarjeta virtual **VMware Network Adapter (VMnet1)**.

## Tabla de contenido

- [Arquitectura y diseño](#arquitectura-y-diseño)
- [Stack tecnológico](#stack-tecnológico)
- [Características principales](#características-principales)
- [Licencia](#licencia)
- [Estructura general del flujo de compra](#estructura-general-del-flujo-de-compra)
- [Frontend (HTML, CSS y JS del navegador)](#frontend-html-css-y-js-del-navegador)
  - [index.html — Portada de la tienda](#-indexhtml--portada-de-la-tienda)
  - [personal-data.html — Registro de datos del cliente](#-personaldatahtml--registro-de-datos-del-cliente)
  - [Páginas de marca](#-páginas-de-marca-nikehtmladidashtmlpumahtmlreebokhtmlnew-balancehtml)
  - [shopping-cart.html — Carrito y resumen del pedido](#-shoppingcarthtml--carrito-y-resumen-del-pedido)
  - [contactar.html — Contacto y atención al cliente](#-contactarhtml--contacto-y-atención-al-cliente)
  - [Lógica del navegador (JavaScript)](#-lógica-del-navegador-javascript)
- [Backend (PHP)](#backend-php)
  - [enviar.php — Recepción de registro y contacto](#-enviarphp--recepción-de-registro-y-contacto)
  - [procesar_compra.php — Proceso de compra y recibo](#-procesarcompraphp--proceso-de-compra-y-recibo)
  - [admin.php — Panel de Logística y Despachos](#-adminphp--panel-de-logística-y-despachos)
- [Bitácora / Cronograma de trabajo](#-bitácora--cronograma-de-trabajo)

---

## Arquitectura y diseño

El proyecto usa una **arquitectura de 3 capas**, simple de desplegar tanto en local (Docker) como en la nube (InfinityFree):

```
┌─ CAPA DE PRESENTACIÓN (FRONTEND · navegador)
│  HTML5 · CSS3 · JavaScript vanilla · Bootstrap 4
│  Carrito y cliente temporal en localStorage
│
│  · index.html            →  portada de la tienda
│  · personal-data.html    →  Paso 1 · registro del cliente
│  · nike / adidas / puma / reebok / new-balance.html
│                          →  Paso 2 · catálogo y carrito (vista rápida)
│  · shopping-cart.html    →  Paso 2 · carrito y resumen del pedido
│  · contactar.html        →  atención al cliente (chat online)
│
├─ CAPA DE APLICACIÓN (BACKEND · servidor Apache)
│  PHP 8.2 con PDO y consultas preparadas
│  · enviar.php            →  guarda registro del cliente y consultas
│  · procesar_compra.php   →  Paso 3 y 4 · guarda el pedido y genera el recibo
│  · admin.php             →  panel de guías / despachos del administrador
│
└─ CAPA DE DATOS (MariaDB / MySQL — base gst_ventasonline)
   clientes · producto · pedidos · pagos · detallpago · chatonline
```

- **Diseño funcional:** el flujo completo sigue **4 pasos** (Registro → Carrito → Pago → Confirmación). Cada acción del cliente conlleva un dato que se guarda en la base de datos, y cada pantalla tiene un **diseño/estilo propio** que sostiene ese proceso.
- **Diseño visual:** identidad **STORE DANY** con paleta **café–dorado**, fondo crema arena, tipografías **Baloo 2 / Oswald / Poppins** y hoja de estilos única global (`Style.css`).

**🗄️ Base de Datos — `gst_ventasonline`**

Estructura de las tablas principales del sistema y la información que almacenan:

| Tabla | Información que guarda |
|---|---|
| `clientes` | Datos personales y de contacto de cada comprador registrado. |
| `pedidos` | Cabecera de cada compra (fecha, total, **estado de envío**: Pendiente/Empacado/Enviado + fecha del cambio, código de guía, cliente). |
| `pagos` | Datos del pago (método, cuenta, estado: completado/pendiente, monto recibido). |
| `detallpago` | Detalle de los **productos** de cada pedido (artículo, talla, color, cantidad, subtotal). |
| `producto` | Catálogo de calzado (nombre, marca, precio, imagen). |
| `chatonline` | Mensajes enviados por los clientes desde el formulario de contacto. |

## Stack tecnológico

| Capa | Tecnología | Detalle |
|---|---|---|
| **Frontend** | HTML5 · CSS3 · JavaScript vanilla | Sin frameworks JS: el comportamiento global vive en `js/main.js` y hay un script por marca (`nike.js`, `adidas.js`, `puma.js`, `reebok.js`, `newbalanc.js`). |
| **Framework CSS** | Bootstrap 4.3.1 (+ jQuery 3.3.1 y Popper) | Sistema de rejilla, navbar, dropdowns y componentes responsive. |
| **Backend** | PHP 8.2 | Scripts procedimentales `enviar.php`, `procesar_compra.php` y `admin.php`, con conexión por **PDO** y **consultas preparadas** (sin inyección SQL). |
| **Base de datos** | MariaDB 10.6 / MySQL | Base `gst_ventasonline` (local) e `if0_41988386_gst_ventasonline` (nube InfinityFree). |
| **Entorno local** | Docker Compose (WSL) | Contenedor Apache **PHP 8.2** (puerto 8080) + contenedor **MariaDB 10.6** (puerto 3306). |
| **Infraestructura y redes** | Linux · VMware Workstation Pro · GNS3 | Sistema base **Linux**, virtualización de equipos con **VMware Workstation Pro**, modelado de topologías de red con **GNS3** y conexión entre máquinas virtuales a través de la red virtual **VMware Network Adapter VMnet1**. |
| **Producción** | InfinityFree | Hosting PHP + MySQL donde se desplegó el proyecto tal cual: las páginas dependen solo de HTML/CSS/JS y los scripts PHP se conectan **directamente a la BD de la nube** (`sql201.infinityfree.com` / `if0_41988386_gst_ventasonline`), por lo que el sitio funciona en la web con la misma lógica del entorno local. |

## Características principales

- 🏬 **Catálogo de 5 marcas** (Nike, Adidas, Puma, Reebok, New Balance) con vista rápida de producto, selector de tallas y diferenciación visual por marca.
- 🛒 **Carrito flotante / cajón lateral** guardado en `localStorage`: panel de marca, contador, resumen de totales, métodos de pago como tarjetas y limpieza al finalizar la compra.
- 👤 **Registro del cliente**: antes de pagar se guardan sus datos en BD, con **validación de cédula** (no se repite con un nombre distinto) y actualización del mismo cliente si vuelve a comprar.
- 💳 **3 métodos de pago** (Nequi, Daviplata y contra entrega) con campo de cuenta validado y cuenta destino oficial de la tienda.
- 🧾 **Recibo de compra**: guía `SD-XXXXX`, hora de Colombia, cuenta enmascarada, fechas en español y opción de imprimir (con UTF-8).
- 👨‍💼 **Panel admin**: login con bloqueo por intentos fallidos, KPIs del día/mes, buscador de guías y estado de pago dinámico por método.
- 🔒 **Muro de Términos y Condiciones** con aceptación por scroll (Ley 1581 de 2012).
- 📣 **Notificaciones elegantes (toasts)** para los avisos de la compra y para el rechazo de cédula en el registro.
- 💬 **Chat online**: canales directos (WhatsApp, teléfono), indicador "Abierto ahora / Cerrado" según la hora de Colombia y consultas guardadas en BD.
- 📱 **Responsive** con detalles de presentación: carrusel de marcas, sello de confianza, botón volver arriba y *lazy loading* de imágenes.

## Licencia

**© 2026 STORE DANY — Todos los derechos reservados.**

Este proyecto es **propietario (código cerrado)** y pertenece al negocio **STORE DANY** (San Vicente de Chucurí, Santander, Colombia). No cuenta con ninguna licencia open-source: **no está permitido copiar, modificar, distribuir ni usar comercialmente** el código, los recursos (logos, imágenes, videos) ni el contenido sin la autorización escrita de su propietaria, Katherine Rodríguez. Esta condición coincide con el aviso del pie de página del sitio web: *"© 2026 STORE DANY. Todos los derechos reservados."*

---

## Estructura general del flujo de compra

El proceso de compra sigue un orden de **4 pasos**: **1. Registro → 2. Carrito → 3. Pago → 4. Confirmación**.

| Paso | Nombre | Página / archivo | Qué ocurre |
|---|---|---|---|
| 1 | **Registro** | `personal-data.html` → `enviar.php` | El cliente ingresa sus datos; se guarda en BD y en `localStorage`.
| 2 | **Carrito** | páginas de marca → `shopping-cart.html` | Se seleccionan los productos y se confirma el pedido.
| 3 | **Pago** | páginas de marca / `shopping-cart.html` | Se elige método de pago (Nequi, Daviplata o contra entrega).
| 4 | **Confirmación** | `procesar_compra.php` | Se registra el pedido, se calcula el recibo y se muestra el comprobante.
| — | **Despacho** | `admin.php` | El administrador consulta las guías/pedidos y actualiza su **estado de envío** (Pendiente → Empacado → Enviado).

---
## Frontend (HTML, CSS y JS del navegador)

A continuación, el papel de cada pantalla desde la visión **funcional**: qué hace el cliente en ella, con qué propósito se construyó y qué información maneja.

### 📄 `index.html` — Portada de la tienda

| Aspecto | Descripción |
|---|---|
| **Función para el cliente** | Es la **puerta de entrada**. Le permite conocer la tienda, ver las categorías por marca, abrir el carrito, ir a los datos de registro y al contacto. |
| **Propósito** | Presentar la marca STORE DANY de forma atractiva y encauzar al cliente hacia la compra o la consulta. |
| **Información que maneja** | Información de presentación (marcas, ofertas, confianza). |

### 📄 `personal-data.html` — Registro de datos del cliente

| Aspecto | Descripción |
|---|---|
| **Función para el cliente** | **Registrarse como comprador** antes de pagar, llenando sus datos personales y de domicilio. |
| **Propósito** | Identificar de forma **única e individual** a quien compra mediante su **cédula**, para generar su despacho y su recibo a su nombre. Si la cédula ya existe y el nombre completo coincide, se actualizan sus datos y se reutiliza el registro; si la cédula existe con otro nombre, se **bloquea** el registro porque el número no es correcto. |
| **Información que maneja** | Datos personales y de contacto: **nombre, apellidos, cédula, celular, correo, departamento, municipio y dirección**. |

### 📄 Páginas de marca: `nike.html`, `adidas.html`, `puma.html`, `reebok.html`, `new-balance.html`

| Aspecto | Descripción |
|---|---|
| **Función para el cliente** | **Explorar y elegir calzado**: ver el catálogo de cada marca, ampliar cada producto (vista rápida con tallas), agregar al carrito y gestionar su pedido. |
| **Propósito** | Vender el calzado de la marca: permitir seleccionar talla y cantidad, y llevar el control del carrito en pantalla. Cada título de marca incluye una **franja con las 5 marcas** para navegar rápido entre ellas. |
| **Información que maneja** | Datos del **producto** (nombre, marca, precio, talla, color) y del **cliente** vía registro obligatorio previo (nombre, cédula, teléfono) para poder su compra. |

### 📄 `shopping-cart.html` — Carrito y resumen del pedido

| Aspecto | Descripción |
|---|---|
| **Función para el cliente** | **Revisar y confirmar su pedido**: ver el catálogo completo, desplegar marcas, revisar el carrito con el resumen del total y acceder a su registro. |
| **Propósito** | Que el cliente verifique qué va a comprar y cuánto pagará antes de finalizar, usando la sección de Garantía y Cambios para tranquilidad. |
| **Información que maneja** | Resumen del **pedido** (productos, cantidades, totales) y datos de **registro del cliente** para continuar la compra. |

### 📄 `contactar.html` — Contacto y atención al cliente

| Aspecto | Descripción |
|---|---|
| **Función para el cliente** | **Comunicarse con la tienda**: ver canales directos (teléfono, WhatsApp, horario, punto físico), saber si la tienda está abierta y enviar una consulta. |
| **Propósito** | Brindar atención y soporte (compras, devoluciones, garantías), indicando el horario de la tienda. |
| **Información que maneja** | Datos de la **consulta**: **nombres, apellidos, correo, teléfono, tipo de consulta (compra/devolución/garantía/otro) y mensaje**. |

---

### ⚙️ Lógica del navegador (JavaScript)
JavaScript **vanilla, sin frameworks**, cargado en las páginas según su papel:
- **`js/main.js`** (comportamiento global de todo el sitio): navbar con efecto al hacer scroll, barra de **Envíos gratis**, animaciones de aparición, botón **volver-arriba**, selector visual de tallas, muro de **Términos y Condiciones** (bloquea el sitio hasta marcar la casilla y leer el texto), la **burbuja de preguntas frecuentes (FAQ)** y las **notificaciones elegantes (RNF-06)** que reemplazan los `alert()`.
- **`nike.js` / `adidas.js` / `puma.js` / `reebok.js` / `newbalanc.js`** (una por marca): catálogo, selección de tallas, gestión del carrito, cálculo de totales y envío del pedido al backend.
- **`jquery`, `popper` y `bootstrap`**: librerías del framework visual cargadas junto al CSS.

---

## Backend (PHP)

### 🖥️ `enviar.php` — Recepción de registro y contacto

| Aspecto | Descripción |
|---|---|
| **Función** | Recibe y **guarda los datos** que el cliente envía desde el formulario de registro o de contacto, validando la **cédula**. |
| **Propósito** | Registrar al cliente para que pueda comprar y almacenar las consultas de contacto. La cédula no puede repetirse con un nombre distinto: si ya existe y el **nombre completo coincide**, se **actualizan** los datos (dirección/celular); si existe con **otro nombre**, se rechaza con un aviso elegante. |
| **Información que maneja** | Del **registro**: nombre, apellidos, cédula, celular, correo y dirección. Del **contacto**: nombres, apellidos, correo, teléfono y mensaje. |

### 🖥️ `procesar_compra.php` — Proceso de compra y recibo

| Función | Qué hace |
|---|---|
| Conexión BD | Local (Docker) o remota (InfinityFree) según el servidor |
| Cliente real | Solo acepta un **cliente registrado en la tienda**; si no existe, **rechaza el pedido** e invita a registrarse |
| Registro del pedido | Guarda el **pedido → pago → detalle** y marca el pago (Completado en Nequi/Daviplata, Pendiente en contra entrega) |
| **Hora del pedido** | Usa la hora de **Colombia** (PHP/Bogotá), no depende del servidor |
| Código de orden | Genera el número de guía `SD-00001` |
| Fechas del recibo | Pago al día + entrega estimada a **8 días hábiles**, en español |
| Cuenta de pago | La muestra **enmascarada** (primeros 3 + *** + últimos 2 dígitos) para seguridad |
| Monto y devuelta | Usa el monto pagado real y calcula la devuelta |
| Recibo | Muestra el comprobante con logo, datos del cliente, productos y totales, con opción de imprimir |
| **Codificación** | Envía el recibo con **`Content-Type: text/html; charset=utf-8`** forzado para que las tildes, signos (¡) y emojis se muestren sin errores |
| **Detalle del pedido** | Las etiquetas Talla / Color se muestran en **pastillas doradas legibles** (tipografía y tamaño mejorados) |

### 🖥️ `admin.php` — Panel de Logística y Despachos

| Aspecto | Descripción |
|---|---|
| **Función** | Es la **vista del negocio**: permite al administrador ingresar con credenciales, consultar los pedidos/despachos y actualizar su **estado de envío**. |
| **Propósito** | Llevar el **control logístico**: consultar las guías de despacho, el **estado de envío** (🕒 Pendiente → 📦 Empacado → 🚚 Enviado) y el **estado de pago** de cada pedido, para gestionar las entregas. |
| **Información que maneja** | Ventas del día/mes, pedidos totales, y por cada guía: **cliente** (nombre, cédula, celular, dirección), **productos** (tallas/colores), **estado de pago**, **estado de envío + fecha del cambio** y valor declarado. |

---

## 📅 Bitácora / Cronograma de trabajo

Registro de las sesiones de desarrollo y las fechas reales en que se trabajó el proyecto. El diseño y escritura del código se realizó en **Visual Studio** durante **mayo de 2026**; las entregas y refinamiento al repositorio fueron a partir de **agosto de 2026**.

| Fecha | Actividad / Mejora | Detalle | Estado |
|---|---|---|---|
| 20–21 may 2026 | **Diseño y desarrollo del código** | Se diseñó y escribió el código base del proyecto: todas las páginas (HTML), los scripts de cada marca (JS), la hoja de estilos (CSS) y los archivos del backend (PHP) con su base de datos. | ✅ Completado |
| 31 ago 2026 | Mejoras de diseño y flujo | Fondo crema arena `#E2D5BE`, stepper de progreso (Registro → Carrito → Pago → Confirmación), panel admin con estado dinámico, panel de marcas, sección garantía, recibo en columna centrada y `new-balance.js` renombrado. | ✅ Completado |
| 02 sep 2026 | Muro de términos y flujo de cliente | Muro de Términos y Condiciones rediseñado y ajustes al flujo de registro del cliente. | ✅ Completado |
| 07 sep 2026 | Carrito flotante y botón fuera del menú | El carrito deja el menú y se convierte en **botón flotante** (esquina inferior derecha) que abre el panel hacia arriba, con badge contador. | ✅ Completado |
| 07 sep 2026 | Menú modernizado | Logo más grande con brillo dorado, título en fuente serif, enlaces legibles, página activa en pastilla dorada, CHAT ONLINE en cápsula y hamburguesa visible en móvil. | ✅ Completado |
| 07 sep 2026 | Títulos, logos y carrusel de marcas | Título de cada marca con **efecto dorado metalizado**, descripción motivacional centrada, logos con **marco y destello**, **franja de las 5 marcas** navegable y **carrusel de portada** con medallones dorados. | ✅ Completado |
| 08 sep 2026 | Pulido visual: precios, colores, badges y modal | Precio con **borde dorado**, info de color con línea dorada, título en Oswald, badges "Más vendido/Nuevo" **solo en el modal** "Ver detalle" y tono visual **por marca**. | ✅ Completado |
| 08 sep 2026 | Pulido visual: index, catálogo y chat | Recuadro de registro del index (3 pasos, mano 👉, botón verde con detalle dorado), hero del catálogo con "Sobre STORE DANY" y bloque de confianza, asesora online integrada en **Chat Online** y títulos de navegador unificados. | ✅ Completado |
| 08 sep 2026 | Panel admin: KPIs, buscador y colores de marca | Tarjetas KPI con **icono en cápsula dorada** y entrada escalonada; **buscador de guías** con botón de limpiar (✖) y **contador "X de Y"**; fondos y hovers en la paleta crema/dorada de la marca. | ✅ Completado |
| 08 sep 2026 | Método de pago en el panel | Se agrega la columna `metodo` a la tabla `pagos`; `procesar_compra.php` guarda el método elegido y el admin lo muestra con su icono (📱/💬/🚚) en cada guía. | ✅ Completado |
| 09 sep 2026 | RNF-06: notificaciones elegantes al cliente | Sistema de **toasts** en `js/main.js` (café/dorado, falta/error/éxito, auto-cierre) que reemplazan los `alert()` de las páginas de marca y del carrito. | ✅ Completado |
| 09 sep 2026 | Muro de T&C rediseñado | Diseño final: logo centrado, sellos con **iconos SVG animados**, **checkbox a medida** con check SVG, **aceptación obligatoria vía scroll** y mención de la **Ley 1581 de 2012**. | ✅ Completado |
| 09 sep 2026 | Comprobante de compra y fix UTF-8 | `procesar_compra.php`: **charset UTF-8 forzado** (evita el error `Â¡`), **pastillas Talla/Color** legibles y corrección del cierre `¡Gracias…!`. | ✅ Completado |
| 10 sep 2026 | Pulido del cajón del carrito | Precio por ítem con **degradado café-dorado**, **total en cápsula dorada** destacado, cabecera con logo grande y "STORE DANY" en **dorado metalizado**; el panel **se desliza desde la derecha sobre fondo desenfocado** con la **marca en el lado izquierdo**, contador y cierre por ✕, clic fuera o Esc. | ✅ Completado |
| 14 sep 2026 | Rediseño UX: catálogo, garantía y portada | Selector de tallas con **equivalencias US/UK/cm** y nota "100% fabricadas en Vietnam"; panel "Ver marcas disponibles" en **una sola línea de medallones con logos reales**; garantía como **flujo numerado 01–04 con flechas animadas**; bienvenida del index con **sello dorado, divisor con brillo, STORE DANY en dorado brillante y tagline**; réplica del botón verde estilo registro en ENVIAR/REGISTRAR DATOS y píldoras de **dominios de correo rápido** (@gmail, @hotmail, @outlook, @yahoo). | ✅ Completado |
| 15 sep 2026 | **Panel admin: estados de envío** | Flujo logístico **🕒 Pendiente → 📦 Empacado → 🚚 Enviado** con **stepper de estados** por guía (botones que actualizan `pedidos.estado_envio` + `fecha_estado`), el estado actual resaltado con su color, pasos superados con ✓ y `estado_envio.sql` listo para aplicar en la BD local y en InfinityFree. | ✅ Completado |
| 15 sep 2026 | **Burbuja de ayuda con preguntas frecuentes** | Widget flotante **💬 en la esquina inferior derecha** (encima del carrito, sin estorbarse) visible en **3 páginas**: `index.html`, catálogo (`shopping-cart.html`) y chat online (`contactar.html`). La función `crearFaqChat()` de `js/main.js` crea la burbuja con **7 preguntas frecuentes** tipo acordeón (tiempos de entrega + factura, confirmación de pago, garantía/devoluciones, cambio de talla, contacto con la asesora y recompra actualizando datos), cabecera en tonos de la marca y pie con botones a **WhatsApp** y a la página **CHAT ONLINE**. Se **retiró el botón flotante verde de WhatsApp** que había a la derecha para no duplicar canales; el contacto por WhatsApp sigue disponible en la burbuja y en la página de contacto. Las respuestas se editan en el arreglo `FAQS` de `js/main.js`. | ✅ Completado |
| 17 sep 2026 | **Validación de cédula en el registro** | `enviar.php` valida la cédula: si ya existe y el **nombre completo coincide** (comparación tolerante a mayúsculas, tildes y espacios), **actualiza** los datos del mismo cliente (dirección/celular) sin duplicar; si existe con **otro nombre**, **bloquea** el registro con un aviso elegante (el mismo toast de las tallas) y devuelve al formulario. | ✅ Completado |
| 17 sep 2026 | **Fix chat online y precios** | El formulario de contacto enviaba el campo `mensaje` pero el backend leía `textarea`, por lo que solo se guardaba el tipo de consulta: ahora se guarda el mensaje completo con los datos de contacto. Se corrigió también el signo `$` faltante en el 8.º producto de `nike`, `puma`, `new-balance` y `reebok`. | ✅ Completado |

*Última actualización: 17 de septiembre de 2026.*
