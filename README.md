# STORE DANY — Documentación del Proyecto

Tienda online de calzado para hombre. Desarrollada con un stack de código abierto: **PHP + MySQL (MariaDB)** en el backend, con **frontend en HTML, CSS y JavaScript vanilla** y **Bootstrap 4** como framework de estilos. El entorno de desarrollo corre con **Docker Compose sobre WSL** (`docker-compose.yml`): un contenedor **Apache con PHP 8.2** (servidor web, puerto 8080) y un contenedor **MariaDB 10.6** (base de datos, puerto 3306). La base de datos se llama **`gst_ventasonline`** (en el contenedor y también en la nube como `if0_41988386_gst_ventasonline` con InfinityFree). Enfoque del proyecto: **comercio electrónico (e-commerce)** tipo catálogo con registro de cliente por pedido, carrito de compras, checkout y panel logístico de despachos.

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
| **Propósito** | Presentar la marca STORE DANY de forma atractiva, dar confianza (sello de 4 badges: pago seguro, envío, originales, garantía) y encauzar al cliente hacia la compra o la consulta. |
| **Información que maneja** | Información de presentación (marcas, ofertas, confianza); no recopila datos personales en esta pantalla. |

### 📄 Páginas de marca: `nike.html`, `adidas.html`, `puma.html`, `reebok.html`, `new-balance.html`

| Aspecto | Descripción |
|---|---|
| **Función para el cliente** | **Explorar y elegir calzado**: ver el catálogo de cada marca, ampliar cada producto (vista rápida con tallas), agregar al carrito y gestionar su pedido. |
| **Propósito** | Vender el calzado de la marca: mostrar productos con badges (Más vendido / Nuevo), permitir seleccionar talla y cantidad, y llevar el control del carrito en pantalla. |
| **Información que maneja** | Datos del **producto** (nombre, marca, precio, talla, color) y del **cliente** vía registro obligatorio previo (nombre, cédula, teléfono) para poder su compra. |

### 📄 `shopping-cart.html` — Carrito y resumen del pedido

| Aspecto | Descripción |
|---|---|
| **Función para el cliente** | **Revisar y confirmar su pedido**: ver el catálogo completo, desplegar marcas, revisar el carrito con el resumen del total y acceder a su registro. |
| **Propósito** | Que el cliente verifique qué va a comprar y cuánto pagará antes de finalizar, usando el sello de confianza y la sección de Garantía y Cambios para tranquilidad. |
| **Información que maneja** | Resumen del **pedido** (productos, cantidades, totales) y datos de **registro del cliente** para continuar la compra. |

### 📄 `personal-data.html` — Registro de datos del cliente

| Aspecto | Descripción |
|---|---|
| **Función para el cliente** | **Registrarse como comprador** antes de pagar, llenando sus datos personales y de domicilio. |
| **Propósito** | Identificar de forma **única e individual** a quien compra (cada pedido exige un cliente nuevo registrado), para generar su despacho y su recibo a su nombre. |
| **Información que maneja** | Datos personales y de contacto: **nombre, apellidos, cédula, celular, correo, departamento, municipio y dirección**. |

### 📄 `contactar.html` — Contacto y atención al cliente

| Aspecto | Descripción |
|---|---|
| **Función para el cliente** | **Comunicarse con la tienda**: ver canales directos (teléfono, WhatsApp, horario, punto físico), saber si la tienda está abierta y enviar una consulta. |
| **Propósito** | Brindar atención y soporte (compras, devoluciones, garantías), indicando el horario real según la hora de Colombia. |
| **Información que maneja** | Datos de la **consulta**: **nombres, apellidos, correo, teléfono, tipo de consulta (compra/devolución/garantía/otro) y mensaje**. |

---

### 🎨 Estilos (CSS) — `Style.css`
- Hoja de estilos global compartida (header, footer, tarjetas, modales, botón volver-arriba).
- **Estética del negocio**: paleta café + dorado, tipografía Poppins, sello de confianza y sección garantía.
- **Estados visuales de producto**: badges "Más vendido" (dorado) / "Nuevo" (azul) e indicador horario animado.

### ⚙️ Lógica del navegador (JavaScript)
- **`js/main.js`**: comportamiento global (menú, volver-arriba) y el **muro de Términos y Condiciones** que el cliente debe aceptar antes de usar el sitio.
- **`nike.js` / `adidas.js` / `puma.js` / `reebok.js` / `newbalanc.js`**: catálogo, selección de tallas, gestión del carrito, cálculo de totales y envío del pedido al backend de cada marca.
- **`js/bootstrap-4.3.1.js` + `js/jquery-3.3.1.min.js` + `js/popper.min.js`**: librerías del framework visual Bootstrap 4.

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

## Estado del proyecto

### ✅ Implementado — Lista de chequeo 

> **Hilo conductor del proyecto:** cada acción del cliente (elegir, registrarse, pagar, confirmar) conlleva un **dato que se guarda en la base de datos `gst_ventasonline`**, y cada pantalla tiene un **diseño/estilo** que sostiene ese proceso.

**🏬 Catálogo y productos**
- ✅ Catálogo de 5 marcas con vista rápida y tallas.
- ✅ Diferenciación visual por marca (colores, bordes, botones, badges).
- ✅ Badges "Más vendido" / "Nuevo" en productos destacados.
- ✅ Cada producto está registrado en la tabla `producto` → **conlleva a la BD**.

**🛒 Carrito de compras**
- ✅ Carrito que se guarda en el navegador (`localStorage`) mientras el cliente arma su pedido.
- ✅ Carrito mejorado: logo, miniaturas, layout horizontal, scroll, borde dorado por item.
- ✅ Botón "Seguir Comprando", badge dorado pulse, flash en icono y toast de confirmación.
- ✅ El checkout envía el pedido con el cliente vinculado al **registro en BD**.
- ✅ Al finalizar se limpia el carrito para que el siguiente pedido exija registro nuevo.

**👤 Registro y contacto**
- ✅ Registro del cliente vinculado a la compra; sin registro válido el sistema **rechaza** el pedido.
- ✅ Formulario de contacto simplificado (nombre, apellidos, tipo de consulta, mensaje).
- ✅ Indicador dinámico "Abierto ahora" / "Cerrado" (hora Colombia).
- ✅ Los datos del registro y el contacto **se guardan en la BD** (tablas `clientes` y `chatonline`).

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
- ✅ Sello de confianza con 4 badges en index, catálogo y marcas.
- ✅ Sección Garantía y Cambios (daño de fábrica, 1 mes, cambio de producto).
- ✅ Stepper de progreso Registro → Carrito → Pago → Confirmación en todo el flujo.
- ✅ Muro de Términos y Condiciones con sellos de seguridad y aceptación obligatoria.
- ✅ Lazy loading en las imágenes de producto (mejora la carga inicial).
- ✅ Botón volver arriba, hover premium, botones elegantes y favicon consistente.

### 🎨 Nuevos pendientes / Mejoras de diseño

Pendientes enfocados en **diseño y presentación** de las pantallas existentes (sin crear más páginas).

**1. Resumen sticky en el carrito**
- Que el panel lateral del carrito (total y botón de compra) permanezca visible al hacer scroll, sin perderse al bajar por el catálogo.

**2. Dark mode global**
- Modo oscuro que se active con un interruptor en el header y guarde la preferencia del usuario.

**3. Mejoras visuales en el panel admin**
- Afinar el diseño del dashboard (gráfico de ventas y tarjetas KPIs) con la paleta café + dorado para una vista más profesional.

**4. Pulido visual general**
- Revisión fina de espaciados, sombras y animaciones para que la experiencia en todas las páginas sea más elegante y consistente.

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

*Última actualización: 02 de septiembre de 2026.*
