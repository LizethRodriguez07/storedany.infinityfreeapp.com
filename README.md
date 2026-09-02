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

## Frontend (HTML, CSS y JS del navegador)

A continuación, el papel de cada pantalla desde la visión **funcional**: qué hace el cliente en ella, con qué propósito se construyó y qué información maneja. (No se listan detalles internos de código.)

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

### ✅ Implementado

**Catálogo y productos**
- [x] Catálogo de 5 marcas + vista rápida con tallas
- [x] Diferenciación visual por marca (colores, bordes, botones, badges, hero teñidos)
- [x] Badges "Más vendido" / "Nuevo" en productos destacados de cada marca

**Carrito de compras**
- [x] Carrito persistente por marca (localStorage)
- [x] Carrito mejorado: logo, miniaturas, layout horizontal, scroll 350px, borde dorado por item
- [x] Botón "Seguir Comprando", badge dorado pulse, flash en icono, toast de confirmación
- [x] Checkout con `id_cliente` vinculado al pedido (cada pedido exige un cliente recién registrado)
- [x] Al completar la compra se **limpia el carrito y `cliente_dany`** para que el siguiente pedido requiera registro nuevo

**Registro y contacto**
- [x] Registro de cliente vinculado al pedido (`id_cliente`); sin registro válido el servidor **rechaza** el pedido (no reutiliza al último cliente)
- [x] Formulario contacto simplificado (nombre, apellidos, tipo de consulta, mensaje)
- [x] Indicador dinámico "Abierto ahora" / "Cerrado" (hora Colombia, cada 60s)
- [x] Selector tipo de consulta (compra, devolución, garantía, otro)

**Backend y recibo**
- [x] Checkout transaccional (pedido + pago + detalle atómicos)
- [x] Recibo premium con código SD-XXXXX, fechas hábiles y cuenta enmascarada
- [x] Hora correcta en pedidos (PHP/Bogotá, inmune al reloj del servidor BD)
- [x] Corrección de encoding en emojis y textos especiales

**Panel admin**
- [x] Login premium (logo amplio, fondo, shake, spinner, bloqueo 3 intentos, Bloq Mayús, copyright)
- [x] KPIs + buscador por cliente/cédula/guía + guías plegables
- [x] Estado de pago dinámico por guía (verde "Completado" para Nequi/Daviplata, naranja "Pendiente" para contra entrega); se eliminó el filtro desplegable "Todos los estados"

**Transversales**
- [x] Botón volver arriba y detalles responsive básicos
- [x] WhatsApp "Escríbenos" solo visible en páginas no-marca
- [x] Tipografía Poppins en todo el sitio (sin negrillas, lectura cómoda)
- [x] Sello de confianza con 4 badges (pago seguro, envío, originales, garantía) en index, catálogo y marcas
- [x] Sección Garantía y Cambios (daño de fábrica, 1 mes, cambio de producto)
- [x] Datos legales ficticios en el footer (NIT)
- [x] Stepper de progreso de compra (Registro → Carrito → Pago → Confirmación) en todo el flujo
- [x] Recibo/procesar_compra.php en columna centrada (stepper arriba, comprobante debajo) con CSS del stepper integrado
- [x] Asteriscos de campos obligatorios con tooltip + leyenda en los formularios
- [x] Favicon consistente (logotipo) en todas las páginas visibles
- [x] Hover premium: tarjetas se elevan + zoom de imagen
- [x] Línea decorativa dorada en títulos de sección
- [x] Botones elegantes con sombra dorada y elevación al hover
- [x] Muro de Términos y Condiciones con sellos de seguridad animados, texto destacado y checkbox de aceptación obligatorio
- [x] Lazy loading (`loading="lazy"`) en las imágenes de producto del catálogo de las 5 marcas (mejora la carga inicial)

### 🔜 Pendiente / Mejoras funcionales
*No hay mejoras funcionales pendientes por el momento.*

### 🎨 Mejoras de diseño pendientes

**1. Carrito de compras (panel lateral en páginas de marca)**
- Resumen sticky en el lateral que se mantenga visible al hacer scroll

**2. Registro de datos (personal-data.html)**
- Indicador de fortaleza de datos completos (barra que se llena al llenar campos obligatorios)

**3. Panel admin (admin.php)**
- **Gráfico de ventas semanal/mensual (Chart.js)**. Plan propuesto:
  - Verificar que la tabla de ventas/pedidos en `gst_ventasonline` guarde `fecha` y `total` por pedido.
  - Crear un endpoint PHP que devuelva `SUM(total)` agrupado por día (semanal) o por mes (mensual) en formato JSON.
  - Cargar Chart.js por CDN y un `<canvas>` en el dashboard.
  - Conectar con `fetch()` para dibujar el gráfico (barra o línea) con los colores café (#857059) y dorado (#ffc107).
  - Botones/toggle para alternar entre vista semanal y mensual.

**4. Transversales (todas las páginas)**
- Dark mode global con toggle en el header

---

## 📅 Bitácora / Cronograma de trabajo

Registro de las sesiones de desarrollo y las fechas reales en que se trabajó el proyecto:

| Fecha | Actividad / Mejora | Detalle | Estado |
|---|---|---|---|
| 25 ago 2026 | Primera entrega del proyecto | Se subió el proyecto final al repositorio (catálogo, carrito, registro, compra, panel admin). | ✅ Completado |
| 31 ago 2026 | Mejoras de diseño y flujo | Fondo crema arena `#E2D5BE`, stepper de progreso (Registro → Carrito → Pago → Confirmación), panel admin (sin filtro de estado, estado dinámico), panel de marcas, sección garantía, recibo en columna centrada, `new-balance.js` renombrado. | ✅ Completado |
| 02 sep 2026 | Muro de términos y flujo de cliente | Muro de Términos y Condiciones rediseñado (sellos de seguridad animados, texto destacado, botón "ACEPTO Y ACCEDO CON TOTAL CONFIANZA") y **cada pedido exige un cliente nuevo registrado** (no se reutiliza el último). | ✅ Completado |
| 02 sep 2026 | Actualización del README | Documentación actualizada (muro de términos, flujo de cliente, bitácora de trabajo). | ✅ Completado |
| 02 sep 2026 | Lazy loading de imágenes | Se agregó `loading="lazy"` a las 45 imágenes de producto del catálogo (9 por cada una de las 5 marcas) para mejorar la carga inicial. | ✅ Completado |
| 02 sep 2026 | Stepper: pasos 2 y 3 automáticos | El paso 2 (Carrito) se marca completado al añadir ≥1 producto, y el paso 3 (Pago) al seleccionar método de pago y contar con la cuenta (Nequi/Daviplata) o solo el método (contra entrega). | ✅ Completado |

*Última actualización: 02 de septiembre de 2026.*
