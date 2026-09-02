# STORE DANY — Documentación del Proyecto

Tienda online de calzado para hombre. PHP + MySQL (XAMPP) con frontend en HTML/CSS/JS vanilla.
Identidad visual: café (#857059 / #3f3428) + dorado (#ffc107 / #ecc998), tipografías **Baloo 2** (títulos) e **Inter** (texto). Fondo general del sitio en **crema arena** `#E2D5BE` (ajustado a mitad entre claro y beige, con degradado de luz suave).

---

## Estructura general del flujo de compra

```
index.html (inicio)
   └── Marcas: nike.html · adidas.html · puma.html · reebok.html
              new-balance.html (+ vista rápida en cada una)
        └── Registro: personal-data.html  (datos del cliente)  ← paso 1
              └── enviar.php  (guarda cliente en BD + localStorage)
                    └── Carrito: shopping-cart.html  (selección de pedidos) ← paso 2
                          └── "Finalizar compra"
                                └── procesar_compra.php  (Pago → recibo) ← pasos 3-4
                                      └── admin.php  (panel de guías de despacho)
```
**(Orden del flujo: 1 Registro → 2 Carrito → 3 Pago → 4 Confirmación)**

---

## Páginas y archivos

### `index.html`
- Portada de la tienda: hero, categorías por marca, accesos a carrito y contacto.
- Botón global "volver arriba" y navegación fija.
- **Sello de confianza** con 4 badges (pago seguro, envío confiable, originales, garantía).

### Páginas de marca (`nike`, `adidas`, `puma`, `reebok`, `new-balance`)
- Catálogo con tarjetas de producto.
- **Vista rápida** (modal): pastillas de tallas disponibles + botón agregar al carrito; fondo oscuro con desenfoque.
- **Badges "Más vendido" / "Nuevo"** en productos destacados de cada marca (dorado/azul).
- **Sello de confianza** con 4 badges justo encima del catálogo.
- **Stepper de progreso**: paso activo "Registro" (el cliente se registra antes de seleccionar pedidos).
- Nota: `new-balance.html` carga el script `newbalanc.js` (nombre sin separador).
- Cada `.js` maneja: agregar al carrito, persistencia en `localStorage`, render del carrito lateral, cálculo de totales y el **checkout** (`datosCompra`) hacia `procesar_compra.php`.
- En el checkout se envía también **`id_cliente`** leído de `localStorage.cliente_dany`.
- Guardia de registro: si no hay cliente registrado, avisa y redirige a `personal-data.html`.
- **Carrito mejorado**: logo STORE DANY en header, miniaturas de producto (52×52, object-fit contain), layout horizontal (imagen al lado del texto), área de scroll 350px, borde dorado por item, botón "Seguir Comprando", badge dorado con animación pulse + flash en icono + toast de confirmación.
- WhatsApp flotante "Escríbenos" deshabilitado en páginas de marca (solo visible en el resto del sitio).

### `shopping-cart.html`
- Vista completa del catálogo, carrito, resumen del pedido y acceso al registro de datos personales.
- **Panel de marcas disponibles**: botón "Ver marcas" que despliega las 5 marcas (NIKE, ADIDAS, PUMA, NEW BALANCE, REEBOK) en grupo, **justo debajo del botón** (antes del sello de confianza).
- **Sello de confianza** (pago seguro, envío confiable, originales, garantía).
- **Stepper de progreso**: paso activo "Registro" (el cliente se registra antes de seleccionar pedidos).
- **Sección Garantía y Cambios**: garantía por daño de fábrica dentro del primer mes, cambio (no devolución de dinero), proceso sencillo, organizada en **cuadrícula centrada de 4 tarjetas uniformes**.

### `personal-data.html`
- Formulario de registro del comprador (nombre, apellidos, cédula, celular, correo, departamento, municipio, dirección).
- **Stepper de progreso**: paso 1 "Registro" activo; al completar todos los datos del cliente, el paso 1 pasa a completo (✓) y continúa con Carrito → Pago → Confirmación.
- **Asteriscos de campos obligatorios** con tooltip en todos los campos + leyenda aclaratoria.

### `contactar.html`
- Encabezado claro: "Contáctanos" con badge "CANALES DE ATENCIÓN DIRECTA".
- Panel de asesora Lizeth con chips clicables (teléfono + WhatsApp + horario + punto físico).
- **Indicador dinámico** "Abierto ahora" / "Cerrado" según hora Colombia (actualiza cada 60s).
- Formulario "Cuéntanos en qué te ayudamos": nombres, apellidos, **selector tipo de consulta** (compra, devolución, garantía, otro) y mensaje, con **asteriscos de campos obligatorios** + leyenda.
- Footer con horarios detallados (L-V 8-7, Sáb 8-6, Dom 9-12), ubicación y compra segura.

### `Style.css`
- Hoja de estilos global compartida (header, footer, tarjetas, modales, botón volver-arriba).
- **Badges de producto** (`.badge-producto`): posicionamiento absoluto, dorado para "Más vendido", azul para "Nuevo".
- **Indicador horario** (`.status-horario`): badge dinámico con animación pulse.

### Mejoras de presentación (capas de diseño)
- **Fondo general** en crema arena `#E2D5BE` con degradado de luz suave (sin imagen, solo color).
- **Tipografía Poppins** en todo el sitio, sin negrillas en contenido informativo (lectura cómoda).
- **Sello de confianza** (`.barra-confianza` / `.sello-confianza`): badges de pago seguro, envío confiable, productos originales y garantía.
- **Sección garantía** (`.seccion-garantia` / `.garantia-item`): tarjeta con garantía por daño de fábrica (1 mes) y cambio de producto, en **cuadrícula centrada de 4 tarjetas de ancho/alto uniforme** (responsive: 4 → 2 → 1 columnas).
- **Datos legales** (`.footer-legal`): NIT ficticio en el footer del negocio.
- **Stepper de progreso** (`.stepper-compra`): barra visual Registro → Carrito → Pago → Confirmación en una tarjeta integrada; pasos completados en verde degradado, paso actual en dorado con realce, línea conectora redondeada.
- **Asteriscos de campos obligatorios** (`.req-asterisco`): marca roja con tooltip ("Campo obligatorio") en todos los campos de los formularios + leyenda aclaratoria.
- **Favicon consistente** con el logotipo (`logotipo.png`) en todas las páginas visibles.
- **Alt de logos corregidos** por marca (Puma, Reebok, New Balance ya no dicen "Logo Nike").
- **Hover premium**: tarjetas de producto que se elevan + zoom suave en la imagen.
- **Muro de Términos y Condiciones** (`js/main.js` + CSS en `Style.css`): bloquea el acceso hasta aceptar; tarjeta premium con logo, texto continuo que inspira confianza (comas / dos puntos / punto y coma / punto final), frase final **destacada y centrada** (`.terms-destacado`), **sellos de seguridad** animados (`.terms-sellos`): 🔒 Pago seguro, 🚚 Envío protegido, 🛡️ Datos privados — en tarjetas centradas, checkbox personalizado y botón "ACEPTO Y ACCEDO CON TOTAL CONFIANZA".
- **Línea decorativa dorada** bajo los títulos de sección.
- **Botones elegantes**: redondeados, sombra dorada y elevación al hover.

---

## Backend

### `enviar.php`
1. Recibe datos de dos formularios (identificados por `origen_formulario`):
   - **Registro de cliente** (`personal-data.html`): `INSERT INTO clientes` → obtiene `lastInsertId()` → guarda `localStorage.cliente_dany = { id, nombre, telefono }` → redirige al carrito.
   - **Contacto** (`contactar.html`): guarda nombre, apellidos, correo, teléfono y mensaje en tabla `chatonline`.
2. **Registro obligatorio por pedido**: el cliente debe registrarse antes de comprar; al completar la compra, el carrito y `cliente_dany` se **borran** del navegador, de modo que el siguiente pedido exige un registro nuevo (no se reutiliza el cliente anterior).

### `procesar_compra.php`
Recibe por POST (JSON) el detalle de la compra desde el `.js` de la marca:

| Función | Qué hace |
|---|---|
| Conexión BD | Local (`base_datos`) o remota (InfinityFree) según `HTTP_HOST` |
| Cliente real | Consulta `clientes` por `id_cliente`; **cada pedido exige un cliente recién registrado** — si no hay `id_cliente` válido, **rechaza el pedido** con mensaje y enlace al registro (ya NO reutiliza el último cliente de la BD) |
| Transacción | Inserta en `pedidos` → `pagos` → `detalle_pedido` con commit/rollback; **estado del pago dinámico** (Completado para Nequi/Daviplata, Pendiente para contra entrega) |
| **Hora del pedido** | Estampada por **PHP** con `date('Y-m-d H:i:s')` y zona `America/Bogota` (no depende del reloj del servidor BD) |
| Código de orden | Genera `SD-00001` (relleno a 5 dígitos) |
| Fechas del recibo | Pago realizado (hoy) + entrega estimada a **8 días hábiles**, meses en español |
| Cuenta Nequi/Daviplata | Se muestra enmascarada (primeros 3 + *** + últimos 2 dígitos) |
| Monto recibido | Usa el monto pagado real; si no viene, el total. Calcula la devuelta |
| Recibo | Diseño premium café/dorado con logo, datos del cliente, productos, totales e impresión |
| **Stepper** | Muestra el flujo completo (Registro → Carrito → Pago → Confirmación) con los pasos 1-3 completados y "Confirmación" activo; cuerpo en **columna centrada** (pasos arriba, comprobante debajo) con CSS del stepper integrado |
| Acciones finales | Imprimir comprobante + volver a la tienda (sin WhatsApp, por decisión del negocio) |

### `admin.php` — Panel de Logística y Despachos
Acceso restringido por sesión (`storedany_admin`). Contiene dos vistas:

**Login del administrador**
- Logo STORE DANY amplio en cabecera café con brillo dorado; fondo con foto de zapatos y velo café.
- Favicon con el logotipo en la pestaña.
- Mostrar/ocultar contraseña (ojo).
- Aviso de Bloq Mayús activado.
- Bloqueo de 30 s tras 3 intentos fallidos (cuenta regresiva en pantalla).
- Animación shake si las credenciales son incorrectas; spinner al ingresar.
- Pie con © STORE DANY y año automático.

**Dashboard**
- **KPIs de ventas**: ventas de hoy, ventas del mes y pedidos totales (tarjetas con borde dorado).
- **Buscador**: filtra guías por cliente, cédula o número de guía + contador de resultados.
- **Guías de despacho plegables**: cada tarjeta muestra badge dorado de guía, fecha (hora Colombia corregida), cliente (nombre, cédula, celular, dirección completa), productos con tallas/colores, **estado de pago dinámico** (punto + badge: verde "Completado" para pagos previos Nequi/Daviplata, naranja "Pendiente" para contra entrega) con barra de progreso logístico.
- Botón **Vaciar Todo** para borrar las guías de prueba.

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
