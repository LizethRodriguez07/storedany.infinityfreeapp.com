# STORE DANY — Documentación del Proyecto

Tienda online de calzado para hombre. PHP + MySQL (XAMPP) con frontend en HTML/CSS/JS vanilla.
Identidad visual: café (#857059 / #3f3428) + dorado (#ffc107 / #ecc998), tipografías **Baloo 2** (títulos) e **Inter** (texto).

---

## Estructura general del flujo de compra

```
index.html (inicio)
   └── Marcas: nike.html · adidas.html · puma.html · reebok.html
              new-balance.html (+ vista rápida en cada una)
        └── Carrito: shopping-cart.html  (js de la marca arma el pedido)
              └── Registro: personal-data.html  (datos del cliente)
                    └── enviar.php  (guarda cliente en BD + localStorage)
                          └── shopping-cart.html → "Finalizar compra"
                                └── procesar_compra.php  (transacción + recibo)
                                      └── admin.php  (panel de guías de despacho)
```

---

## Páginas y archivos

### `index.html`
- Portada de la tienda: hero, categorías por marca, accesos a carrito y contacto.
- Botón global "volver arriba" y navegación fija.

### Páginas de marca (`nike`, `adidas`, `puma`, `reebok`, `new-balance` + sus `.js`)
- Catálogo con tarjetas de producto.
- **Vista rápida** (modal): pastillas de tallas disponibles + botón agregar al carrito; fondo oscuro con desenfoque.
- **Badges "Más vendido" / "Nuevo"** en productos destacados de cada marca (dorado/azul).
- Cada `.js` maneja: agregar al carrito, persistencia en `localStorage`, render del carrito lateral, cálculo de totales y el **checkout** (`datosCompra`) hacia `procesar_compra.php`.
- En el checkout se envía también **`id_cliente`** leído de `localStorage.cliente_dany`.
- Guardia de registro: si no hay cliente registrado, avisa y redirige a `personal-data.html`.
- **Carrito mejorado**: logo STORE DANY en header, miniaturas de producto (52×52, object-fit contain), layout horizontal (imagen al lado del texto), área de scroll 350px, borde dorado por item, botón "Seguir Comprando", badge dorado con animación pulse + flash en icono + toast de confirmación.
- WhatsApp flotante "Escríbenos" deshabilitado en páginas de marca (solo visible en el resto del sitio).

### `shopping-cart.html`
- Vista completa del carrito, resumen del pedido y acceso al registro de datos personales.

### `personal-data.html`
- Formulario de registro del comprador (nombre, apellidos, cédula, celular, correo, departamento, municipio, dirección).

### `contactar.html`
- Encabezado claro: "Contáctanos" con badge "CANALES DE ATENCIÓN DIRECTA".
- Panel de asesora Lizeth con chips clicables (teléfono + WhatsApp + horario + punto físico).
- **Indicador dinámico** "Abierto ahora" / "Cerrado" según hora Colombia (actualiza cada 60s).
- Formulario "Cuéntanos en qué te ayudamos": nombres, apellidos, **selector tipo de consulta** (compra, devolución, garantía, otro) y mensaje.
- Footer con horarios detallados (L-V 8-7, Sáb 8-6, Dom 9-12), ubicación y compra segura.

### `Style.css`
- Hoja de estilos global compartida (header, footer, tarjetas, modales, botón volver-arriba).
- **Badges de producto** (`.badge-producto`): posicionamiento absoluto, dorado para "Más vendido", azul para "Nuevo".
- **Indicador horario** (`.status-horario`): badge dinámico con animación pulse.

---

## Backend

### `enviar.php`
1. Recibe datos de dos formularios (identificados por `origen_formulario`):
   - **Registro de cliente** (`personal-data.html`): `INSERT INTO clientes` → obtiene `lastInsertId()` → guarda `localStorage.cliente_dany = { id, nombre, telefono }` → redirige al carrito.
   - **Contacto** (`contactar.html`): guarda nombre, apellidos, correo, teléfono y mensaje en tabla `chatonline`.

### `procesar_compra.php`
Recibe por POST (JSON) el detalle de la compra desde el `.js` de la marca:

| Función | Qué hace |
|---|---|
| Conexión BD | Local (`base_datos`) o remota (InfinityFree) según `HTTP_HOST` |
| Cliente real | Consulta `clientes` por `id_cliente`; si falta, usa el último registrado |
| Transacción | Inserta en `pedidos` → `pagos` → `detalle_pedido` con commit/rollback |
| **Hora del pedido** | Estampada por **PHP** con `date('Y-m-d H:i:s')` y zona `America/Bogota` (no depende del reloj del servidor BD) |
| Código de orden | Genera `SD-00001` (relleno a 5 dígitos) |
| Fechas del recibo | Pago realizado (hoy) + entrega estimada a **8 días hábiles**, meses en español |
| Cuenta Nequi/Daviplata | Se muestra enmascarada (primeros 3 + *** + últimos 2 dígitos) |
| Monto recibido | Usa el monto pagado real; si no viene, el total. Calcula la devuelta |
| Recibo | Diseño premium café/dorado con logo, datos del cliente, productos, totales e impresión |
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
- **Buscador**: filtra guías por cliente, cédula o número de guía + filtro por estado (completado / pendiente / cancelado) y contador de resultados.
- **Guías de despacho plegables**: cada tarjeta muestra badge dorado de guía, fecha (hora Colombia corregida), cliente (nombre, cédula, celular, dirección completa), productos con tallas/colores, estado de pago con monto y barra de progreso logístico.
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
- [x] Checkout con `id_cliente` vinculado al pedido

**Registro y contacto**
- [x] Registro de cliente vinculado al pedido (`id_cliente`)
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
- [x] KPIs + buscador/filtros + guías plegables

**Transversales**
- [x] Botón volver arriba y detalles responsive básicos
- [x] WhatsApp "Escríbenos" solo visible en páginas no-marca

### 🔜 Pendiente / Mejoras funcionales
*No hay mejoras funcionales pendientes por el momento.*

### 🎨 Mejoras de diseño pendientes

**1. Carrito de compras (panel lateral en páginas de marca)**
- Resumen sticky en el lateral que se mantenga visible al hacer scroll
- Stepper visual tipo "progress bar" que muestre: Carrito → Registro → Pago → Confirmación

**2. Registro de datos (personal-data.html)**
- Indicador de fortaleza de datos completos (barra que se llena al llenar campos obligatorios)
- Tooltips o asteriscos claros en campos obligatorios vs opcionales

**3. Panel admin (admin.php)**
- Gráfico de ventas semanal/mensual (usando Chart.js o similar ligero)
- Modo oscuro toggle para el administrador
- Exportar guías a PDF o CSV para el transportador
- Filtro por rango de fechas en las guías (además de texto y estado)

**4. Transversales (todas las páginas)**
- Favicon consistente en todas las páginas (algunas aún no lo tienen)
- Lazy loading en imágenes de productos (mejora la carga inicial)
- Dark mode global con toggle en el header
- Micro-interacciones: hover en botones, press states, ripple effects

---

*Última actualización: 26 de agosto 2026.*
