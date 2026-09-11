# STORE DANY — Documentación del Proyecto

Tienda online de calzado para hombre. Desarrollada con un stack de código abierto: **PHP + MySQL (MariaDB)** en el backend, con **frontend en HTML, CSS y JavaScript vanilla** y **Bootstrap 4** como framework de estilos. El entorno de desarrollo corre con **Docker Compose sobre WSL** (`docker-compose.yml`): un contenedor **Apache con PHP 8.2** (servidor web, puerto 8080) y un contenedor **MariaDB 10.6** (base de datos, puerto 3306). La base de datos se llama **`gst_ventasonline`** (en el contenedor y también en la nube como `if0_41988386_gst_ventasonline` con InfinityFree). Enfoque del proyecto: **comercio electrónico (e-commerce)** tipo catálogo con registro de cliente por pedido, carrito de compras, checkout y panel logístico de despachos.

---

## Arquitectura y diseño

El proyecto usa una **arquitectura de 3 capas**, simple de desplegar tanto en local (Docker) como en la nube (InfinityFree):

```
┌─ CAPA DE PRESENTACIÓN (navegador)
│  HTML5 · CSS3 · JavaScript vanilla · Bootstrap 4
│  Carrito y cliente temporal en localStorage
│  index.html · personal-data.html · nike.html · adidas.html · puma.html
│  reebok.html · new-balance.html · shopping-cart.html · contactar.html
├─ CAPA DE APLICACIÓN (servidor)
│  PHP 8.2 sobre Apache, con PDO y consultas preparadas
│  enviar.php · procesar_compra.php · admin.php
└─ CAPA DE DATOS
   MariaDB / MySQL — base gst_ventasonline
   clientes · producto · pedidos · pagos · detallpago · chatonline
```

- **Diseño funcional:** el flujo completo sigue **4 pasos** (Registro → Carrito → Pago → Confirmación). Cada acción del cliente conlleva un dato que se guarda en la base de datos, y cada pantalla tiene un **diseño/estilo propio** que sostiene ese proceso.
- **Diseño de datos:** la base separa el catálogo (`producto`) del cliente (`clientes`), la cabecera de compra (`pedidos`), el pago (`pagos`) con su método y estado, el detalle de ítems (`detallpago`) y las consultas de ejemplo o contacto (`chatonline`).
- **Diseño visual:** identidad **STORE DANY** con paleta **café–dorado**, fondo crema arena, tipografías **Baloo 2 / Oswald / Poppins** y hoja de estilos única global (`Style.css`).

## Stack tecnológico

| Capa | Tecnología | Detalle |
|---|---|---|
| **Frontend** | HTML5 · CSS3 · JavaScript vanilla | Sin frameworks JS: el comportamiento global vive en `js/main.js` y hay un script por marca (`nike.js`, `adidas.js`, `puma.js`, `reebok.js`, `newbalanc.js`). |
| **Framework CSS** | Bootstrap 4.3.1 (+ jQuery 3.3.1 y Popper) | Sistema de rejilla, navbar, dropdowns y componentes responsive. |
| **Backend** | PHP 8.2 | Scripts procedimentales `enviar.php`, `procesar_compra.php` y `admin.php`, con conexión por **PDO** y **consultas preparadas** (sin inyección SQL). |
| **Base de datos** | MariaDB 10.6 / MySQL | Base `gst_ventasonline` (local) e `if0_41988386_gst_ventasonline` (nube InfinityFree). |
| **Entorno local** | Docker Compose (WSL) | Contenedor Apache **PHP 8.2** (puerto 8080) + contenedor **MariaDB 10.6** (puerto 3306). |
| **Producción** | InfinityFree | Hosting PHP + MySQL donde se desplegó el proyecto tal cual: las páginas dependen solo de HTML/CSS/JS y los scripts PHP se conectan **directamente a la BD de la nube** (`sql201.infinityfree.com` / `if0_41988386_gst_ventasonline`), por lo que el sitio funciona en la web con la misma lógica del entorno local. |

## Características principales

- 🏬 **Catálogo de 5 marcas** (Nike, Adidas, Puma, Reebok, New Balance) con vista rápida de producto, selector de tallas y diferenciación visual por marca.
- 🛒 **Carrito flotante / cajón lateral** guardado en `localStorage`: panel de marca, contador, resumen de totales, métodos de pago como tarjetas y limpieza al finalizar la compra.
- 👤 **Registro por pedido**: cada compra exige un **cliente nuevo registrado** (guardado en BD), con validaciones y notificaciones elegantes.
- 💳 **3 métodos de pago** (Nequi, Daviplata y contra entrega) con campo de cuenta validado y cuenta destino oficial de la tienda.
- 🧾 **Recibo de compra**: guía `SD-XXXXX`, hora de Colombia, cuenta enmascarada, fechas en español y opción de imprimir (con UTF-8).
- 👨‍💼 **Panel admin**: login con bloqueo por intentos fallidos, KPIs del día/mes, buscador de guías y estado de pago dinámico por método.
- 🔒 **Muro de Términos y Condiciones** con aceptación por scroll (Ley 1581 de 2012).
- 📣 **Notificaciones elegantes (toasts)** que reemplazan los `alert()` durante todo el proceso de compra.
- 💬 **Chat online**: canales directos (WhatsApp, teléfono), indicador "Abierto ahora / Cerrado" según la hora de Colombia y consultas guardadas en BD.
- 📱 **Responsive** con detalles de presentación: carrusel de marcas, sello de confianza, botón volver arriba, WhatsApp flotante y *lazy loading* de imágenes.

## Licencia

**© 2026 STORE DANY — Todos los derechos reservados.**

Este proyecto es **propietario (código cerrado)** y pertenece al negocio **STORE DANY** (San Vicente de Chucurí, Santander, Colombia). No cuenta con ninguna licencia open-source: **no está permitido copiar, modificar, distribuir ni usar comercialmente** el código, los recursos (logos, imágenes, videos) ni el contenido sin la autorización escrita de su propietaria, Katherine Rodríguez. Esta condición coincide con el aviso del pie de página del sitio web: *"© 2026 STORE DANY. Todos los derechos reservados."*

---

## Estructura general del flujo de compra

El proceso de compra sigue un orden de **4 pasos**: **1. Registro → 2. Carrito → 3. Pago → 4. Confirmación**.

```
index.html (inicio)
   │
   ▼
Paso 1 · Registro del cliente
   en: personal-data.html  →  enviar.php  (guarda cliente en BD + localStorage)
   │
   ▼
Paso 2 · Selección de pedidos (Carrito)
   en: nike.html · adidas.html · puma.html · reebok.html · new-balance.html  (vista rápida)
       └── ▶ continuar en shopping-cart.html  (carrito + resumen)
   │
   ▼
Paso 3 · Pago
   │
   ▼
Paso 4 · Confirmación (recibo)
   en: procesar_compra.php  (guarda pedido + genera recibo)
   │
   ▼
admin.php  (panel de guías / despachos del ADMIN)
```

| Paso | Nombre | Página / archivo | Qué ocurre |
|---|---|---|---|
| 1 | **Registro** | `personal-data.html` → `enviar.php` | El cliente ingresa sus datos; se guarda en BD y en `localStorage`.
| 2 | **Carrito** | páginas de marca → `shopping-cart.html` | Se seleccionan los productos y se confirma el pedido.
| 3 | **Pago** | páginas de marca / `shopping-cart.html` | Se elige método de pago (Nequi, Daviplata o contra entrega).
| 4 | **Confirmación** | `procesar_compra.php` | Se registra el pedido, se calcula el recibo y se muestra el comprobante.
| — | **Despacho** | `admin.php` | El administrador consulta las guías/pedidos.

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
| **Propósito** | Identificar de forma **única e individual** a quien compra (cada pedido exige un cliente nuevo registrado), para generar su despacho y su recibo a su nombre. |
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

### 🎨 Estilos (CSS) — `Style.css`
Hoja de estilos **única y global** (más de 5.000 líneas) que se carga en todo el sitio. Su concepto: definir la **identidad visual de la marca** (paleta café-dorado, tipografías Baloo 2 / Oswald / Poppins) y dar **estilo a cada pantalla y componente** — navbar con efecto al scroll, barra de envíos gratis, carrito flotante, formularios, tarjetas de producto, toasts, muro de Términos y Condiciones y panel admin — manteniendo un solo lugar donde vive el diseño y evitando estilos duplicados por página.


### ⚙️ Lógica del navegador (JavaScript)
JavaScript **vanilla, sin frameworks**, cargado en las páginas según su papel:
- **`js/main.js`** (comportamiento global de todo el sitio): navbar con efecto al hacer scroll, barra de **Envíos gratis**, animaciones de aparición, botón **volver-arriba**, **WhatsApp flotante**, selector visual de tallas, muro de **Términos y Condiciones** (bloquea el sitio hasta marcar la casilla y leer el texto) y las **notificaciones elegantes (RNF-06)** que reemplazan los `alert()`.
- **`nike.js` / `adidas.js` / `puma.js` / `reebok.js` / `newbalanc.js`** (una por marca): catálogo, selección de tallas, gestión del carrito, cálculo de totales y envío del pedido al backend.
- **`jquery`, `popper` y `bootstrap`**: librerías del framework visual cargadas junto al CSS.

---

## Backend (PHP y Base de Datos)

### 🖥️ `enviar.php` — Recepción de registro y contacto

| Aspecto | Descripción |
|---|---|
| **Función** | Recibe y **guarda los datos** que el cliente envía desde el formulario de registro o de contacto. |
| **Propósito** | Registrar al cliente para que pueda comprar (cada pedido exige un cliente nuevo) y almacenar las consultas de contacto. |
| **Información que maneja** | Del **registro**: nombre, apellidos, cédula, celular, correo y dirección. Del **contacto**: nombres, apellidos, correo, teléfono y mensaje. |

### 🖥️ `procesar_compra.php` — Proceso de compra y recibo

| Función | Qué hace |
|---|---|
| Conexión BD | Local (Docker) o remota (InfinityFree) según el servidor |
| Cliente real | Solo acepta un **cliente recién registrado**; si no existe, **rechaza el pedido** e invita a registrarse |
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
| **Función** | Es la **vista del negocio**: permite al administrador ingresar con credenciales e inspeccionar los pedidos/despachos. |
| **Propósito** | Llevar el **control logístico**: consultar las guías de despacho, el estado de pago de cada pedido y las ventas, para gestionar las entregas. |
| **Información que maneja** | Ventas del día/mes, pedidos totales, y por cada guía: **cliente** (nombre, cédula, celular, dirección), **productos** (tallas/colores), **estado de pago** y fecha de despacho. |

### 🗄️ Base de Datos — `gst_ventasonline`

Tablas principales del sistema y la información que almacenan:

| Tabla | Información que guarda |
|---|---|
| `clientes` | Datos personales y de contacto de cada comprador registrado. |
| `pedidos` | Cabecera de cada compra (fecha, total, código de guía, cliente). |
| `pagos` | Datos del pago (método, cuenta, estado: completado/pendiente, monto recibido). |
| `detallpago` | Detalle de los **productos** de cada pedido (artículo, talla, color, cantidad, subtotal). |
| `producto` | Catálogo de calzado (nombre, marca, precio, imagen). |
| `chatonline` | Mensajes enviados por los clientes desde el formulario de contacto. |

---

## Requerimientos del sistema

### 📋 Requerimientos funcionales (RF) — redactados con método INVEST

| ID | Historia | Rol | Funcionalidad | Razón / Resultado | Criterios de aceptación (como – quiero – para) |
|---|---|---|---|---|---|
| RF-01 | Explorar catálogo | Cliente | Explorar el catálogo por cada una de las 5 marcas con vista rápida de cada producto | Elegir el calzado que más le guste | Como cliente, quiero ver el catálogo por marca y el detalle de cada producto, para elegir el calzado adecuado. |
| RF-02 | Seleccionar talla | Cliente | Elegir talla y cantidad antes de agregar al carrito; si no elige talla, el sistema lo impide y lo notifica | Asegurarse de que el producto le quede bien | Como cliente, quiero elegir talla y cantidad antes de agregar al carrito, para asegurarme de que el producto me quedará bien. |
| RF-03 | Revisar carrito | Cliente | Ver el carrito en el navegador (`localStorage`) con botón flotante, contador, panel lateral y total acumulado | Revisar el pedido antes de pagar | Como cliente, quiero ver mi carrito con el total acumulado, para revisar mi pedido antes de pagar. |
| RF-04 | Registrar datos personales | Cliente | Registrar sus datos personales (nombre, cédula, teléfono, dirección) vinculados a un pedido nuevo en la BD | Hacer el pedido y recibir la guía a su nombre | Como cliente, quiero registrar mis datos personales por compra, para hacer el pedido y recibir mi guía a mi nombre. |
| RF-05 | Elegir método de pago | Cliente | Elegir entre Nequi, Daviplata o contra entrega | Pagar de la forma más cómoda | Como cliente, quiero elegir mi método de pago, para pagar de la forma que me quede más cómoda. |
| RF-06 | Ingresar número de cuenta | Cliente | Ingresar y validar (mínimo 7 dígitos) el número de cuenta en pagos digitales | Validar el pago digital | Como cliente, quiero ingresar mi número de cuenta al pagar con Nequi/Daviplata, para que mi pago sea válido. |
| RF-07 | Guardar la compra | Sistema | Guardar cada compra como pedido → pago → detalle en las tablas `pedidos`, `pagos` y `detallpago`, incluyendo el método de pago | Registrar la transacción completa en la BD | Como sistema, quiero guardar cada compra como pedido → pago → detalle, para registrar la transacción completa en la BD. |
| RF-08 | Generar número de guía | Sistema | Generar una guía única (`SD-XXXXX`) con la fecha y hora de Colombia | Identificar cada pedido | Como sistema, quiero generar una guía única con la hora de Colombia, para identificar cada pedido. |
| RF-09 | Recibir recibo | Cliente | Generar un comprobante con datos del cliente, productos, totales y cuenta enmascarada, con opción de imprimir | Tener constancia del pedido | Como cliente, quiero recibir mi recibo de compra, para tener constancia de mi pedido y su número de guía. |
| RF-10 | Aceptar términos y condiciones | Cliente | Aceptar el muro de Términos y Condiciones antes de usar el sitio | Uso regulado del sitio | Como cliente, quiero aceptar los términos y condiciones, para usar el sitio de forma autorizada. |
| RF-11 | Ingresar al panel admin | Administrador | Ingresar con credenciales y bloquear el acceso por intentos fallidos | Acceso seguro al panel | Como administrador, quiero ingresar al panel con mis credenciales, para acceder de forma segura a los pedidos de la tienda. |
| RF-12 | Ver guías de despacho | Administrador | Ver las guías con estado del pago, método de pago (📱/💬/🚚), datos del cliente y los productos | Gestionar los despachos | Como administrador, quiero ver las guías con su estado y método de pago, para gestionar los despachos. |
| RF-13 | Ver KPIs y buscar guías | Administrador | Ver KPIs de ventas (hoy, mes, pedidos) y buscar guías por cliente/cédula/guía con botón de limpiar y contador | Conocer el negocio y localizar pedidos | Como administrador, quiero ver los KPIs y buscar una guía, para conocer el negocio y localizar un pedido. |
| RF-14 | Enviar consultas | Cliente | Enviar consultas desde el formulario de contacto y guardarlas en la tabla `chatonline` | Resolver dudas | Como cliente, quiero enviar mis consultas, para resolver mis dudas de compra, devolución o garantía. |
| RF-15 | Recibir avisos claros | Cliente | Recibir notificaciones elegantes si falta un dato o hay un error en el proceso de compra (reemplazan los `alert()`) | Completar la compra sin quedarse atascado | Como cliente, quiero que me avisen de forma clara si me falta un dato o hay un error, para completar mi compra sin atascarme. |
| RF-16 | Saber el horario de la tienda | Cliente | Indicar dinámicamente si la tienda está "Abierta ahora / Cerrada" según la hora de Colombia | Saber si la tienda está disponible | Como cliente, quiero saber si la tienda está abierta, para saber cuándo puedo ser atendido. |

### ⚙️ Requerimientos no funcionales (RNF) — redactados con método INVEST

| ID | Historia | Rol | Funcionalidad | Razón / Resultado | Criterios de aceptación (como – quiero – para) |
|---|---|---|---|---|---|
| RNF-01 | Seguridad en la gestión del pedido | Sistema | Manejar la información de la compra de forma segura (consultas preparadas PDO, sin inyección SQL) y enmascarar el pago | Proteger la información del cliente | Como sistema, quiero manejar la compra con consultas seguras y el pago enmascarado, para proteger los datos del cliente. ✅ |
| RNF-02 | Términos y condiciones | Cliente | Exigir la aceptación de los T&C antes de hacer uso del sistema | Uso regulado del sitio | Como sistema, quiero exigir la aceptación de los T&C antes del uso, para garantizar un uso regulado del sitio. ✅ |
| RNF-03 | Capacidad del sistema | Sistema | Soportar hasta 1000 usuarios simultáneos disponibles 24/7 y avisar cuando se alcance el cupo límite | Garantizar disponibilidad constante | Como sistema, quiero soportar 1000 usuarios 24/7 y avisar el cupo límite, para garantizar disponibilidad. ⚠️ Parcial (falta el aviso de cupo) |
| RNF-04 | Rendimiento | Sistema | Responder cada acción en menos de 10 segundos | Compra ágil | Como sistema, quiero responder en menos de 10 segundos, para que la compra sea ágil. ⚠️ Parcial (sin medición formal) |
| RNF-05 | Copias de seguridad | Sistema | Generar copias de respaldo con una lista de fecha y cliente | Recuperar la información | Como sistema, quiero generar copias de seguridad con fecha y cliente, para recuperar la información. ⚠️ Parcial (InfinityFree hace backups automáticos; falta la lista) |
| RNF-06 | Notificación de errores | Cliente | Recibir una notificación elegante y visible al faltar un dato o ocurrir un error en el proceso de compra | Completar la compra sin atascarse | Como cliente, quiero recibir notificaciones elegantes ante faltas o errores, para completar mi proceso de compra. ✅ (toasts en `js/main.js`) |

---

## Estado del proyecto

### ✅ Implementado — Lista de chequeo 

> **Hilo conductor del proyecto:** cada acción del cliente (elegir, registrarse, pagar, confirmar) conlleva un **dato que se guarda en la base de datos `gst_ventasonline`**, y cada pantalla tiene un **diseño/estilo** que sostiene ese proceso.

**🏬 Catálogo y productos**
- ✅ Catálogo de 5 marcas con vista rápida y tallas.
- ✅ Diferenciación visual por marca (colores, bordes, botones, badges).
- ✅ Badges "Más vendido" / "Nuevo" visibles **dentro del modal "Ver detalle"** (ocultos en las tarjetas del catálogo).
- ✅ Precio con **borde dorado elegante** y **línea dorada** bajo la info de color; título de producto en tipografía Oswald.
- ✅ Modal "Ver detalle" con **tono visual propio por marca**.
- ✅ Cada producto está registrado en la tabla `producto` → **conlleva a la BD**.

**🛒 Carrito de compras**
- ✅ Carrito que se guarda en el navegador (`localStorage`) mientras el cliente arma su pedido.
- ✅ Botón **flotante** del carrito fuera del menú (esquina inferior derecha): el panel abre hacia arriba, con badge contador dorado.
- ✅ Métodos de pago como **tarjetas visuales** (Nequi, Daviplata y Contraentrega) con selección y check; el campo pide **"INGRESA EL NÚMERO DE CUENTA"**.
- ✅ Carrito mejorado: logo, miniaturas, layout horizontal, scroll, borde dorado por item.
- ✅ Carrito modal: al abrirse **se desliza desde el lado derecho** sobre un **fondo oscuro desenfocado**, con **marca (logo + título/subtítulo) en el lado izquierdo** y contador de ítems.
- ✅ Botón "Seguir Comprando", badge dorado pulse, flash en icono y toast de confirmación.
- ✅ El checkout envía el pedido con el cliente vinculado al **registro en BD**.
- ✅ Al finalizar se limpia el carrito para que el siguiente pedido exija registro nuevo.

**👤 Registro y contacto**
- ✅ Registro del cliente vinculado a la compra; sin registro válido el sistema **rechaza** el pedido.
- ✅ Formulario de contacto simplificado (nombre, apellidos, tipo de consulta, mensaje).
- ✅ Indicador dinámico "Abierto ahora" / "Cerrado" (hora Colombia).
- ✅ Los datos del registro y el contacto **se guardan en la BD** (tablas `clientes` y `chatonline`).
- ✅ Recuadro de registro en el index ("¡Registra tus datos! 👤") con 3 pasos, mano 👉 animada y botón verde con detalle dorado hacia el ingreso de datos.
- ✅ Asesora online integrada en el panel de **Chat Online** (compromisos y caja de confianza) en lugar de estar en el catálogo.

**💳 Backend y recibo** (todo lo que el cliente decide aquí **se registra en la BD**)
- ✅ Checkout transaccional: el pedido se guarda como **pedido → pago → detalle** (tablas `pedidos`, `pagos`, `detallpago`).
- ✅ Registrar el pedido dejando el estado de pago (Completado / Pendiente) según el método elegido.
- ✅ Generar el número de guía `SD-00001` y la fecha con hora de Colombia.
- ✅ Mostrar el **recibo** con los productos, totales y la cuenta de pago enmascarada.

**👨‍💼 Panel admin** (leer la información que conllevó a la BD)
- ✅ Login de administrador con acceso restringido y bloqueo por intentos fallidos.
- ✅ KPIs de ventas (hoy, mes, pedidos) + buscador por cliente/cédula/guía.
- ✅ Guías de despacho con estado de pago dinámico, consultando **directamente la BD**.

**🎨 Transversales** (el diseño/estilo que acompaña todo el proceso)
- ✅ Estética STORE DANY (paleta café + dorado, tipografía Poppins, fondo crema arena) en todo el sitio.
- ✅ Menú modernizado: logo grande con brillo, título en serif elegante, enlaces legibles, página activa en pastilla dorada y hamburguesa bien visible en móvil.
- ✅ Títulos de marca con **efecto dorado metalizado** + descripción motivacional centrada bajo cada título.
- ✅ Logos de marca con **marco elegante, animación de destello** y **franja de las 5 marcas** de navegación rápida en cada página.
- ✅ **Carrusel de marcas** en la portada con medallones dorados, logos a color y fondo degradado premium.
- ✅ Sello de confianza con 4 badges únicamente en el **index** (retirado del catálogo y de las páginas de marca).
- ✅ Sección Garantía y Cambios (daño de fábrica, 1 mes, cambio de producto).
- ✅ Stepper de progreso Registro → Carrito → Pago → Confirmación en todo el flujo.
- ✅ Muro de Términos y Condiciones con **sellos SVG animados**, **checkbox a medida**, **aceptación por scroll** (Ley 1581 de 2012) y logo centrado.
- ✅ **Notificaciones elegantes al cliente (RNF-06)**: avisos café/dorado que reemplazan los `alert()` del navegador durante el proceso de compra (talla, datos personales, carrito vacío, método de pago, cuenta y errores de conexión).
- ✅ Lazy loading en las imágenes de producto (mejora la carga inicial).
- ✅ Botón volver arriba, hover premium, botones elegantes y favicon consistente.

### 🎨 Nuevos pendientes / Mejoras de diseño

Pendientes enfocados en **diseño y presentación** de las pantallas existentes (sin crear más páginas).

**1. Resumen sticky en el carrito**
- Que el panel lateral del carrito (total y botón de compra) permanezca visible al hacer scroll, sin perderse al bajar por el catálogo.

**2. Dark mode global**
- Modo oscuro que se active con un interruptor en el header y guarde la preferencia del usuario.

**3. Mejoras visuales en el panel admin**
- ✅ Tarjetas KPI (Ventas de hoy, Ventas del mes y Pedidos totales) con **icono en cápsula dorada**, degradado superior dorado y **animación de entrada escalonada**.
- ✅ Buscador de guías con **botón de limpiar** (✖) y **contador de guías** en el título de la sección ("X de Y" al filtrar).
- ✅ Método de pago visible en cada guía (📱 Nequi / 💬 Daviplata / 🚚 Contraentrega), guardado en la columna `metodo` de la tabla `pagos`.

**4. Pulido visual general**
- ✅ Avanzado: precio con borde dorado, info de color con línea dorada, título Oswald, badge solo en modal, tono de marca en el modal, recuadro de registro en el index, asesora en Chat Online, **depuración del CSS** y **panel admin alineado a la paleta de la marca** (fondo crema, avatar dorado, hovers dorados, botones con borde dorado).
- ⏳ Queda pendiente el repaso fino de espaciados, sombras y animaciones en el resto de las páginas (portada, catálogo y marcas).

---

## 📅 Bitácora / Cronograma de trabajo

Registro de las sesiones de desarrollo y las fechas reales en que se trabajó el proyecto. El diseño y escritura del código se realizó en **Visual Studio** durante **mayo de 2026**; las entregas y refinamiento al repositorio fueron a partir de **agosto de 2026**.

| Fecha | Actividad / Mejora | Detalle | Estado |
|---|---|---|---|
| 20–21 may 2026 | **Diseño y desarrollo del código** | Se diseñó y escribió el código base del proyecto en Visual Studio: todas las páginas (HTML), los scripts de cada marca (JS), la hoja de estilos (CSS) y los archivos del backend (PHP) con su base de datos. | ✅ Completado |
| 25 ago 2026 | Primera entrega del proyecto | Se subió el proyecto final al repositorio (catálogo, carrito, registro, compra, panel admin). | ✅ Completado |
| 31 ago 2026 | Mejoras de diseño y flujo | Fondo crema arena `#E2D5BE`, stepper de progreso (Registro → Carrito → Pago → Confirmación), panel admin (sin filtro de estado, estado dinámico), panel de marcas, sección garantía, recibo en columna centrada, `new-balance.js` renombrado. | ✅ Completado |
| 02 sep 2026 | Muro de términos y flujo de cliente | Muro de Términos y Condiciones rediseñado (sellos de seguridad animados, texto destacado, botón "ACEPTO Y ACCEDO CON TOTAL CONFIANZA") y **cada pedido exige un cliente nuevo registrado** (no se reutiliza el último). | ✅ Completado |
| 02 sep 2026 | Actualización del README | Documentación actualizada (muro de términos, flujo de cliente, bitácora de trabajo). | ✅ Completado |
| 02 sep 2026 | Lazy loading de imágenes | Se agregó `loading="lazy"` a las 45 imágenes de producto del catálogo (9 por cada una de las 5 marcas) para mejorar la carga inicial. | ✅ Completado |
| 02 sep 2026 | Stepper: pasos 2 y 3 automáticos | El paso 2 (Carrito) se marca completado al añadir ≥1 producto, y el paso 3 (Pago) al seleccionar método de pago y contar con la cuenta (Nequi/Daviplata) o solo el método (contra entrega). | ✅ Completado |
| 07 sep 2026 | Carrito flotante y botón fuera del menú | El carrito deja el menú y se convierte en **botón flotante** (esquina inferior derecha) que abre el panel hacia arriba; se reconstruyó el bloque del carrito en las 5 páginas de marca y quedó el badge contador sobre el botón. | ✅ Completado |
| 07 sep 2026 | Campo de cuenta renombrado | Se cambia la petición de "celular" por **"INGRESA EL NÚMERO DE CUENTA"** en el campo de pago digital (HTML y JS de las 5 marcas). | ✅ Completado |
| 07 sep 2026 | Menú modernizado | Logo más grande con brillo dorado, título en fuente serif elegante, enlaces más grandes y legibles, página activa en pastilla dorada, CHAT ONLINE en cápsula y hamburguesa bien visible en móvil (con dropdown más claro). | ✅ Completado |
| 07 sep 2026 | Títulos, logos y carrusel de marcas | Título de cada marca con **efecto dorado metalizado**; descripción motivacional centrada bajo cada título; logos con **marco elegante y destello** al pasar el mouse; **franja de las 5 marcas** navegable en cada página; **carrusel de portada** rediseñado con medallones dorados, logos a color y fondo degradado premium (sin enlaces). | ✅ Completado |
| 07 sep 2026 | Sello de confianza retirado | Los 4 badges "Pago seguro / Envío confiable / Productos originales / Garantía" se eliminan de las **5 páginas de marca** y del **catálogo**; se conservan únicamente en el **index**. | ✅ Completado |
| 07 sep 2026 | Actualización del README | Documentación puesta al día con el carrito flotante, el menú, los títulos/logos y el carrusel de marcas, además del sello de confianza. | ✅ Completado |
| 08 sep 2026 | Pulido visual: precios, colores, badges y modal | Precio con **borde dorado elegante** en tarjetas y modal; **info de color** rediseñada (Oswald + línea dorada); **título de producto** en Oswald 500; badges "Más vendido / Nuevo" movidos **solo al modal** "Ver detalle"; tono visual **por marca** dentro del modal. | ✅ Completado |
| 08 sep 2026 | Pulido visual: index, catálogo y chat | Recuadro de registro del index rediseñado (título con 👤, 3 pasos, mano 👉, botón verde con detalle dorado); hero del catálogo con "Sobre STORE DANY" + bloque de confianza unificado (mini-beneficios + cifras); asesora online integrada en **Chat Online**; títulos de navegador unificados en todo el sitio. | ✅ Completado |
| 08 sep 2026 | Depuración técnica | **Eliminadas las reglas CSS en desuso** (tarjeta de asesora, sección "en cifras", precios con descuento, estilos de avatar del registro) y corregido el bug de badges en el modal de `nike.js`. | ✅ Completado |
| 08 sep 2026 | Panel admin: pulido de KPIs | Tarjetas KPI (Ventas de hoy, del mes y pedidos) rediseñadas con **icono en cápsula dorada** y **animación de entrada escalonada**; grid de 3 columnas recuperado. | ✅ Completado |
| 08 sep 2026 | Panel admin: buscador y contador | Buscador de guías con **botón de limpiar** (✖ visible al escribir) y **contador de guías** en el título de la sección ("X de Y" al filtrar, total sin filtro). | ✅ Completado |
| 08 sep 2026 | Panel admin: colores de marca | Fondo al crema del sitio `#F7F3EA`, avatar del cliente en **degradado dorado**, tarjetas KPI con borde superior dorado, hovers de filas en dorado suave y botones con **borde dorado** al pasar el mouse. | ✅ Completado |
| 08 sep 2026 | Método de pago en el panel | Se agrega la columna `metodo` a la tabla `pagos` (ALTER en InfinityFree); `procesar_compra.php` guarda el método elegido (Nequi/Daviplata/Contraentrega) y el admin lo muestra con su icono (📱/💬/🚚) en cada guía. | ✅ Completado |
| 09 sep 2026 | RNF-06: notificaciones elegantes al cliente | Se crea el sistema de **toasts de notificación** en `js/main.js` (estética café/dorado, tipos falta/error/éxito, auto-cierre) y se reemplazan los 35 `alert()` de las 5 marcas y los 2 de `enviar.php` por avisos elegantes en el proceso de compra. | ✅ Completado |
| 09 sep 2026 | Sección de requerimientos en el README | Se documentan los **requerimientos funcionales (RF-01 a RF-16)** y **no funcionales (RNF-01 a RNF-06)** implementados, más las **historias de usuario (HU-01 a HU-12)** con el método **INVEST**. | ✅ Completado |
| 09 sep 2026 | Requerimientos en formato INVEST | La sección de requerimientos se unifica en **2 tablas** (RF y RNF) redactadas con el método **INVEST** y las columnas ID, Historia, Rol, Funcionalidad, Razón/Resultado y Criterios de aceptación (como – quiero – para); se elimina la tabla independiente de historias de usuario. | ✅ Completado |
| 09 sep 2026 | Muro de T&C rediseñado | **Diseño final del muro de Términos y Condiciones**: logo centrado, sellos con **iconos SVG animados** (pago seguro, envío protegido, datos privados), **checkbox a medida** con check SVG, **aceptación obligatoria vía scroll** (el botón solo se habilita tras leer el texto) y mención de la **Ley 1581 de 2012**. | ✅ Completado |
| 09 sep 2026 | Comprobante de compra y fix UTF-8 | `procesar_compra.php`: **charset UTF-8 forzado** (evita el error `Â¡` en el hosting), **pastillas Talla/Color** con tipografía y tamaño mejorados, y corrección del cierre `¡Gracias…!`. | ✅ Completado |
| 09 sep 2026 | Documentación del frontend | README actualizado: se describen el **muro de términos**, el **formulario de datos personales rediseñado**, las **notificaciones elegantes (toasts)** y el **comprobante con UTF-8** y detalle legible. | ✅ Completado |
| 10 sep 2026 | Pulido del carrito de compras | Precio por ítem con **degradado café-dorado** (no compite con el total) y **total en cápsula dorada** (fondo dorado en degradado, más grande y destacado). | ✅ Completado |
| 10 sep 2026 | Logo y marca en el carrito | Cabecera del carrito rediseñada: **logo más grande** (28px → 54px, marco dorado y sombra) con "STORE DANY" en **dorado metalizado** (Baloo 2, 21px) sobre franja degradada crema. | ✅ Completado |
| 10 sep 2026 | Carrito modal con fondo desenfocado | Al abrir el carrito se **desliza desde el lado derecho** sobre un **fondo oscuro transparente y desenfocado** (blur) que queda detrás del panel; cabecera con **logo + título "GESTIÓN DE VENTAS ONLINE" y subtítulo "STORE DANY"** a la izquierda, **contador de ítems**, y cierre por botón ✕, clic fuera o tecla **Esc**. | ✅ Completado |
| 10 sep 2026 | Marca en el lado izquierdo del carrito | Al abrir el cajón, la **marca (logo + título "Gestión de Ventas Online" + subtítulo "STORE DANY") se muestra en el lado izquierdo** sobre el fondo desenfocado; el cajón derecho queda solo con "TU CARRITO DE COMPRAS", contador y ✕. En móvil el panel de marca se oculta. | ✅ Completado |

*Última actualización: 10 de septiembre de 2026.*
