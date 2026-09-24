# Especificaciones del proyecto — STORE DANY

> Documento complementario del [README](./README.md) de la tienda STORE DANY. Aquí se detallan los **requisitos funcionales**, los **requisitos no funcionales** y las **especificaciones técnicas** del sistema. Para la descripción general del proyecto, su arquitectura, el objetivo general y los objetivos específicos, el flujo de compra y la bitácora, consulte el [README](./README.md).

## Tabla de contenido

- [Requisitos funcionales (RF)](#requisitos-funcionales-rf)
- [Requisitos no funcionales (RNF)](#requisitos-no-funcionales-rnf)
- [Especificaciones técnicas del entorno](#especificaciones-técnicas-del-entorno)
- [Relación con el README](#relación-con-el-readme)

## Requisitos funcionales (RF)

Conjunto de reglas de comportamiento que debe cumplir el sistema (lo que la tienda hace).

| ID | Requisito funcional |
|---|---|
| RF-01 | El catálogo agrupa el calzado por **5 marcas** (Nike, Adidas, Puma, Reebok, New Balance) y ofrece **vista rápida** de producto y **selector de tallas** con equivalencias US/UK/cm. |
| RF-02 | El sistema mantiene un **carrito flotante** persistente en `localStorage` con contador, panel lateral, resumen de totales y eliminación de ítems. |
| RF-03 | El **registro del cliente** valida la **cédula**: bloquea el registro si el número ya existe con un nombre distinto y **actualiza los datos** del mismo cliente en compras posteriores. |
| RF-04 | El cliente elige entre **3 métodos de pago** (Nequi, Daviplata o contra entrega), se le indica la cuenta destino de la tienda y se confirma el monto a pagar. |
| RF-05 | El **proceso de compra** guarda pedido, pago y detalle en la BD, asigna la guía `SD-XXXXX`, usa la hora de Colombia y genera el **recibo imprimible** con la opción de imprimir. |
| RF-06 | El sistema exige **aceptar los Términos y Condiciones** (Ley 1581 de 2012) antes de operar, con aceptación por scroll en el muro. |
| RF-07 | El **chat online** recibe las consultas de los clientes, las clasifica por **tipo de consulta** y las guarda en la BD con el mensaje y el dato de contacto. |
| RF-08 | La **burbuja de preguntas frecuentes (FAQ)** ofrece 7 respuestas en acordeón, visible en portada, catálogo y página de contacto. |
| RF-09 | El **panel admin** permite el ingreso con credenciales, muestra KPIs del día/mes, busca guías por cliente o cédula y actualiza el **estado de pago** según el método. |
| RF-10 | El admin gestiona el **estado de envío** (🕒 Pendiente → 📦 Empacado → 🚚 Enviado) y queda registrada la fecha del cambio. |

## Requisitos no funcionales (RNF)

Cualidades y restricciones del sistema (la forma en que lo hace).

| ID | Requisito no funcional |
|---|---|
| RNF-01 | **Seguridad y datos**: consultas preparadas con PDO (sin inyección SQL), credenciales reales fuera del repositorio, login admin con bloqueo por intentos fallidos y datos personales protegidos bajo la Ley 1581 de 2012. |
| RNF-02 | **Diseño responsive**: la interfaz se adapta a móvil, tablet y escritorio con la paleta café–dorado y las tipografías de la marca (Baloo 2 / Oswald / Poppins). |
| RNF-03 | **Mantenibilidad**: desarrollo en HTML, CSS y JavaScript vanilla, una sola hoja de estilos global (`Style.css`) y scripts independientes por página de marca. |
| RNF-04 | **Portabilidad del despliegue**: el mismo código funciona en el entorno local (Docker Compose: Apache + PHP 8.2 + MariaDB 10.6) y en la nube (InfinityFree + MySQL), sin cambios de lógica. |
| RNF-05 | **Rendimiento**: *lazy loading* de imágenes, dependencias (Bootstrap, jQuery) alojadas localmente para no depender de CDN y guía recibo con hora y formato UTF-8. |
| RNF-06 | **Usabilidad con notificaciones elegantes**: los avisos del proceso se muestran como **toasts** (falta/error/éxito) en lugar de los `alert()` del navegador, y el rechazo de cédula usa el mismo aviso visual con redirección. |

## Especificaciones técnicas del entorno

| Aspecto | Especificación |
|---|---|
| Lenguajes | PHP 8.2 (backend) · HTML5 · CSS3 · JavaScript vanilla (frontend) |
| Base de datos | MariaDB 10.6 (local) / MySQL (nube) — BD `gst_ventasonline` |
| Framework visual | Bootstrap 4.3.1 + jQuery 3.3.1 + Popper |
| Servidor local | Apache en contenedor Docker (WSL), puerto 8080; BD en puerto 3306 |
| Producción | InfinityFree con PHP + MySQL: sitio publicado con la misma lógica del entorno local |

## Relación con el README

- **README.md**: descripción general del proyecto, arquitectura de 3 capas, stack tecnológico, características principales, objetivo general y objetivos específicos, flujo de compra y bitácora/cronograma.
- **Especificaciones.md** (este documento): detalla los requisitos funcionales, no funcionales y el entorno técnico que implementan lo descrito en el README.

---

**© 2026 STORE DANY — Todos los derechos reservados.**