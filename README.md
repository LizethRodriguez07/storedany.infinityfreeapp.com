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
- Cada `.js` maneja: agregar al carrito, persistencia en `localStorage`, render del carrito lateral, cálculo de totales y el **checkout** (`datosCompra`) hacia `procesar_compra.php`.
- En el checkout se envía también **`id_cliente`** leído de `localStorage.cliente_dany`.
- Guardia de registro: si no hay cliente registrado, avisa y redirige a `personal-data.html`.
- **Carrito mejorado**: logo STORE DANY en header, miniaturas de producto, botón "Seguir Comprando", badge dorado con animación pulse + flash en icono + toast de confirmación.
- WhatsApp flotante "Escríbenos" deshabilitado en páginas de marca (solo visible en el resto del sitio).

### `shopping-cart.html`
- Vista completa del carrito, resumen del pedido y acceso al registro de datos personales.

### `personal-data.html`
- Formulario de registro del comprador (nombre, apellidos, cédula, celular, correo, departamento, municipio, dirección).

### `contactar.html`
- Encabezado claro: "Contáctanos" con badge "CANALES DE ATENCIÓN DIRECTA".
- Panel de asesora Lizeth con chips clicables (teléfono + WhatsApp + horario + punto físico).
- Formulario "Cuéntanos en qué te ayudamos": nombres, apellidos, correo, teléfono (opcional) y mensaje.
- Footer con horarios detallados (L-V 8-7, Sáb 8-6, Dom 9-12), ubicación y compra segura.

### `Style.css`
- Hoja de estilos global compartida (header, footer, tarjetas, modales, botón volver-arriba).

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
- [x] Catálogo de 5 marcas + vista rápida con tallas
- [x] **Diferenciación visual por marca**: Nike (negro), Adidas (azul), Puma (rojo), Reebok (rojo oscuro), New Balance (azul oscuro) — borde, precio, botón, zoom, badge y título hero teñidos con color de marca
- [x] Carrito persistente por marca (localStorage)
- [x] **Carrito mejorado**: logo STORE DANY en header, miniaturas de producto en cada item, botón "Seguir Comprando", badge dorado con animación pulse, flash en icono del carrito y toast "Agregado al carrito"
- [x] Registro de cliente vinculado al pedido (`id_cliente`)
- [x] Checkout transaccional (pedido + pago + detalle atómicos)
- [x] Recibo premium con código SD-XXXXX, fechas hábiles y cuenta enmascarada
- [x] Hora correcta en pedidos nuevos (PHP/Bogotá, inmune al reloj del servidor BD)
- [x] Panel admin con login premium (logo amplio, fondo zapatos, shake, spinner, bloqueo 3 intentos, Bloq Mayús, copyright)
- [x] KPIs + buscador/filtros + guías plegables en admin
- [x] Página de contacto mejorada (título claro, formulario simplificado: nombre + apellidos + mensaje, emoji codificado)
- [x] Botón volver arriba y detalles responsive básicos
- [x] Corrección de encoding en emojis y textos especiales
- [x] WhatsApp "Escríbenos" solo visible en páginas no-marca (inicio, contacto, registro)

### 🔜 Pendiente / Mejoras funcionales
- [ ] **Estados de la guía editables** desde admin (marcar enviado/entregado)
- [ ] **Seguimiento para el cliente**: página donde el comprador consulte su guía con el código SD
- [ ] Paginación/buscador avanzado cuando crezca el volumen de guías
- [ ] Login del lado servidor: hash de contraseña y HTTPS
- [ ] Inventario: descontar tallas vendidas y avisar stock bajo

### 🎨 Propuestas de mejora de diseño

**1. Páginas de marca (nike, adidas, puma, reebok, new-balance)**
- Agregar badge de "Más vendido" o "Nuevo" en productos destacados
- Filtro por talla visible en la grilla (no solo en la vista rápida)
- Animación de entrada staggered para las tarjetas al cargar la página

**2. Carrito de compras (panel lateral en páginas de marca)**
- ~~Miniaturas de los productos en la tabla del carrito~~ → ✅ Implementado
- ~~Botón "Seguir comprando" más visible para volver al catálogo~~ → ✅ Implementado
- Resumen sticky en el lateral que se mantenga visible al hacer scroll
- Stepper visual tipo "progress bar" que muestre: Carrito → Registro → Pago → Confirmación

**3. Registro de datos (personal-data.html)**
- Selector de departamento/municipio con cascada (seleccionar depto filtra los municipios)
- Indicador de fortaleza de datos completos (barra que se llena al llenar campos obligatorios)
- Tooltips o asteriscos claros en campos obligatorios vs opcionales

**4. Recibo de compra (procesar_compra.php)**
- Barra de progreso visual del pedido: Pagado → En preparación → Enviado → Entregado
- Código QR con el número de guía SD para escanear desde el celular
- Fecha de entrega en formato más legible ("Miércoles 2 de Septiembre" en vez de solo "02/09/2026")
- Opción de descargar el recibo como PDF (además de imprimir)

**5. Panel admin (admin.php)**
- Gráfico de ventas semanal/mensual (usando Chart.js o similar ligero)
- Modo oscuro toggle para el administrador
- Exportar guías a PDF o CSV para el transportador
- Notificación sonora o visual cuando llega un pedido nuevo
- Filtro por rango de fechas en las guías (además de texto y estado)

**6. Contactar (contactar.html)**
- Mapa de Google Maps incrustado con la ubicación exacta del punto de venta
- Horario dinámico que muestre "Abierto ahora" / "Cerrado" según hora actual
- Chat en vivo con Lizeth (usando socket simple o servicio gratuito como Tawk.to)
- Formulario con selector de tipo de consulta (compra, devolución, garantía, otro)

**7. Mejoras transversales (afectan todas las páginas)**
- Favicon consistente en todas las páginas (algunas aún no lo tienen)
- Transiciones suaves entre páginas (fade-in al cargar)
- Lazy loading en imágenes de productos (mejora la carga inicial)
- Dark mode global con toggle en el header
- Animaciones micro-interacciones: hover en botones, press states, ripple effects
- Google Analytics o similares para medir tráfico

---

*Última actualización: 25 de agosto 2026.*
