<?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota');

// 1. CONEXIÓN A LA BASE DE DATOS
if ($_SERVER['HTTP_HOST'] == 'localhost:8080' || $_SERVER['HTTP_HOST'] == '127.0.0.1:8080') {
    $host = 'base_datos'; 
    $db   = 'gst_ventasonline'; 
    $user = 'root';              
    $pass = 'clave_storedany_2026'; 
} else {
    $host = 'sql201.infinityfree.com';
    $db   = 'if0_41988386_gst_ventasonline';
    $user = 'if0_41988386';
    $pass = 'NJvVj32GYWri';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (\PDOException $e) {
    die("Error crítico de conexión a la base de datos: " . $e->getMessage());
}

// Alinear la hora de la base de datos con la hora de Colombia (UTC-5)
try {
    $pdo->exec("SET time_zone = '-05:00'");
} catch (Exception $tzE) { /* si el hosting no lo permite, se usa la hora del servidor */ }

// 2. LECTURA Y DECODIFICACIÓN DE DATOS RECIBIDOS POR FETCH JSON
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

if (!$data || empty($data['productos'])) {
    die("Error: No se recibió la información del carrito o los datos son inválidos.");
}

$idClienteReceived = !empty($data['id_cliente']) ? intval($data['id_cliente']) : null;
$total             = floatval($data['total'] ?? $data['total_compra'] ?? 0);
$metodoPago        = $data['metodo_pago'] ?? 'Efectivo';
$numeroCuenta      = trim($data['numero_cuenta_cliente'] ?? '');
$montoPagado       = floatval($data['monto_pagado'] ?? 0);
$devuelta          = floatval($data['devuelta_cambio'] ?? ($montoPagado > $total ? $montoPagado - $total : 0));
$productos         = $data['productos'];

// 3. ASIGNACION DEL CLIENTE Y CARGA DE SUS DATOS REALES
$idCliente = $idClienteReceived;

$stmtCli = $pdo->prepare("SELECT nombre, apellidos, cedula, celular, correo, departamento, municipio, direccion
                          FROM clientes WHERE id = :id");
$stmtCli->execute([':id' => $idCliente]);
$cliente = $stmtCli->fetch();

if (!$cliente) {
    // Respaldo: ultimo cliente registrado (flujo normal: el cliente se registra antes de comprar)
    $stmtLast = $pdo->query("SELECT id, nombre, apellidos, cedula, celular, correo, departamento, municipio, direccion
                             FROM clientes ORDER BY id DESC LIMIT 1");
    $cliente   = $stmtLast->fetch();
    $idCliente = $cliente ? intval($cliente['id']) : 1;
}

$nombreCompleto   = $cliente ? trim($cliente['nombre'] . ' ' . $cliente['apellidos']) : 'Cliente Registrado';
$telefonoCliente  = $cliente ? trim((string)$cliente['celular']) : '';
$cedulaCliente    = $cliente ? trim((string)$cliente['cedula']) : '';
$direccionCliente = $cliente ? trim(trim((string)$cliente['direccion']) . ' - ' . trim((string)$cliente['municipio']) . ' (' . trim((string)$cliente['departamento']) . ')', ' -') : '';

// 4. TRANSACCIÓN BASE DE DATOS
try {
    $pdo->beginTransaction();

    // A. Tabla pedidos
    $sqlPedido = "INSERT INTO pedidos (id_cliente, fecha_pedido, total) VALUES (:id_cliente, :fecha_pedido, :total)";
    $stmtP = $pdo->prepare($sqlPedido);
    $stmtP->execute([
        ':id_cliente'   => $idCliente,
        ':fecha_pedido' => date('Y-m-d H:i:s'),
        ':total'        => $total
    ]);
    $idPedido = $pdo->lastInsertId();

    // B. Tabla pagos
    // Estado dinámico según el método de pago: pago previo = Completado, contra entrega = Pendiente
    $metodoPagoNormalizado = strtolower(trim($metodoPago));
    $estadoPago = (strpos($metodoPagoNormalizado, 'contra') !== false) ? 'Pendiente' : 'Completado';
    $sqlPago = "INSERT INTO pagos (id_pedido, monto, estado) VALUES (:id_pedido, :monto, :estado)";
    $stmtPago = $pdo->prepare($sqlPago);
    $stmtPago->execute([
        ':id_pedido' => $idPedido,
        ':monto'     => $montoPagado,
        ':estado'    => $estadoPago
    ]);

            // C. Tabla detallpago
    $sqlDetalle = "INSERT INTO detallpago (id_pedido, id_producto, cantidad, precio_unitario, talla, color, marca) 
                   VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario, :talla, :color, :marca)";
    $stmtDetalle = $pdo->prepare($sqlDetalle);

    foreach ($productos as $item) {
        $precioLimpio = floatval(preg_replace('/[^0-9.]/', '', str_replace('.', '', $item['precio'])));
        
        $stmtProd = $pdo->prepare("SELECT id FROM producto WHERE nombre LIKE :nombre LIMIT 1");
        $stmtProd->execute([':nombre' => '%' . trim($item['titulo']) . '%']);
        $prodRow = $stmtProd->fetch();
        
        $idProducto = $prodRow ? $prodRow['id'] : 1;

        $stmtDetalle->execute([
            ':id_pedido'       => $idPedido,
            ':id_producto'     => $idProducto,
            ':cantidad'        => intval($item['cantidad']),
            ':precio_unitario' => $precioLimpio,
            ':talla'           => $item['talla'] ?? 'Única',
            ':color'           => $item['color'] ?? 'Original',
            ':marca'           => trim($item['titulo'] ?? '') !== '' ? trim($item['titulo']) : 'Producto Store Dany'
        ]);
    }

    $pdo->commit();

    // Fecha real del pedido segun la base de datos
    $stmtF = $pdo->prepare("SELECT fecha_pedido FROM pedidos WHERE id = :id");
    $stmtF->execute([':id' => $idPedido]);
    $filaPedido = $stmtF->fetch();
    $fechaPago = new DateTime($filaPedido ? $filaPedido['fecha_pedido'] : 'now');

    // Entrega estimada: 8 dias habiles (lunes a viernes) despues del pago
    $fechaEntrega = clone $fechaPago;
    $habiles = 0;
    while ($habiles < 8) {
        $fechaEntrega->modify('+1 day');
        if ((int)$fechaEntrega->format('N') <= 5) { $habiles++; }
    }

    $mesesES = [1=>'enero', 2=>'febrero', 3=>'marzo', 4=>'abril', 5=>'mayo', 6=>'junio',
                7=>'julio', 8=>'agosto', 9=>'septiembre', 10=>'octubre', 11=>'noviembre', 12=>'diciembre'];
    $fechaPagoTxt    = $fechaPago->format('d') . ' de ' . $mesesES[(int)$fechaPago->format('n')] . ' de ' . $fechaPago->format('Y') . ' - ' . $fechaPago->format('g:i a');
    $fechaEntregaTxt = $fechaEntrega->format('d') . ' de ' . $mesesES[(int)$fechaEntrega->format('n')] . ' de ' . $fechaEntrega->format('Y');

    // Codigo de orden legible para el cliente
    $codigoOrden = 'SD-' . str_pad((string)$idPedido, 5, '0', STR_PAD_LEFT);

    // Numero de cuenta enmascarado para metodos digitales
    $cuentaVisible = '';
    if ($metodoPago === 'Nequi' || $metodoPago === 'Daviplata') {
        if (strlen($numeroCuenta) >= 7) {
            $cuentaVisible = substr($numeroCuenta, 0, 3) . str_repeat('*', strlen($numeroCuenta) - 5) . substr($numeroCuenta, -2);
        } else {
            $cuentaVisible = $numeroCuenta;
        }
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Error al procesar la compra en la base de datos: " . $e->getMessage());
}

// 5. COMPROBANTE VISUAL
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Compra - STORE DANY</title>
    <link rel="icon" type="image/png" href="logotipo.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --bg-body: #f5efe6;
            --card-bg: #ffffff;
            --cafe: #857059;
            --cafe-oscuro: #3f3428;
            --dorado: #ffc107;
            --dorado-suave: #ecc998;
            --texto: #5a4b3b;
            --texto-suave: #8a7a6a;
            --borde: #e8ddd0;
            --verde: #2e9e5b;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-body);
            color: var(--texto);
            margin: 0;
            padding: 40px 15px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh;
        }

        .recibo {
            background: var(--card-bg);
            width: 100%;
            max-width: 480px;
            border-radius: 26px;
            border-top: 6px solid var(--dorado);
            box-shadow: 0 25px 50px -12px rgba(63, 52, 40, 0.28);
            overflow: hidden;
            animation: aparecer 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes aparecer {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ---------- ENCABEZADO ---------- */
        .recibo-cabecera {
            background: linear-gradient(150deg, var(--cafe-oscuro) 0%, #6b5744 60%, var(--cafe) 100%);
            color: #ffffff;
            padding: 32px 24px 38px;
            text-align: center;
            position: relative;
        }

        .recibo-cabecera::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0; right: 0;
            height: 26px;
            background: var(--card-bg);
            border-radius: 26px 26px 0 0;
        }

        .logo-recibo {
            width: 112px; height: 112px;
            border-radius: 50%;
            border: 4px solid var(--dorado);
            outline: 3px solid rgba(255, 193, 7, 0.35);
            object-fit: contain;
            background: #ffffff;
            padding: 4px;
            margin-bottom: 12px;
            box-shadow: 0 0 0 8px rgba(255, 193, 7, 0.12), 0 10px 30px rgba(0, 0, 0, 0.45);
        } 
            to   { transform: scale(1); }
        }

        .recibo-cabecera h1 {
            font-family: 'Baloo 2', sans-serif;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 1px;
            margin: 0 0 4px;
            color: var(--dorado-suave);
        }

        .recibo-cabecera .gracias-mensaje {
            font-family: 'Baloo 2', sans-serif;
            font-size: 17px;
            font-weight: 600;
            color: #ffe9a8;
            margin: 0 0 6px;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 6px rgba(0, 0, 0, 0.4);
        }

        .recibo-cabecera .gracias-mensaje strong {
            color: var(--dorado);
            font-weight: 400;
            letter-spacing: 1px;
        }

        .chip-orden {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(236, 201, 152, 0.45);
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .chip-orden strong {
            font-family: 'Baloo 2', monospace;
            color: var(--dorado);
            font-size: 15px;
            letter-spacing: 1.5px;
        }

        .chip-envio {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: 8px;
            background: rgba(46, 158, 91, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.30);
            color: #d9f2e3;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        /* ---------- CUERPO ---------- */
        .recibo-cuerpo { padding: 20px 24px 28px; }

        .rotulo-seccion {
            font-family: 'Baloo 2', sans-serif;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: var(--dorado);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 22px 0 12px;
        }

        .rotulo-seccion::after {
            content: '';
            flex: 1;
            height: 2px;
            border-radius: 2px;
            background: linear-gradient(90deg, rgba(255, 193, 7, 0.45), transparent);
        }

        /* Tarjeta del cliente */
        .tarjeta-cliente {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            background: linear-gradient(145deg, #23201b, #14120f);
            border-left: 5px solid var(--dorado);
            border-radius: 16px;
            padding: 18px;
            color: #ffffff;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.30);
        }

        .cliente-nombre {
            font-family: 'Baloo 2', sans-serif;
            font-size: 19px;
            font-weight: 800;
            color: var(--dorado-suave);
            margin: 0 0 10px;
            line-height: 1.2;
        }

        .cliente-linea {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13.5px;
            line-height: 1.45;
            padding: 6px 0;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.14);
        }

        .cliente-linea:last-child { border-bottom: none; }

        .cliente-linea .etiq {
            color: var(--dorado-suave);
            font-weight: 800;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            min-width: 86px;
            padding-top: 2px;
        }

        .cliente-linea .dato { flex: 1; word-break: break-word; color: #ffd76a !important; font-weight: 400; }

        .cliente-linea a { color: #ffd76a; text-decoration: none; font-weight: 700; }

        /* Fechas */
        .caja-fechas {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 4px;
        }

        .fecha-caja {
            background: #faf6f0;
            border: 1px solid var(--borde);
            border-radius: 14px;
            padding: 12px 14px;
        }

        .fecha-caja.entrega {
            background: #fdf6df;
            border-color: rgba(255, 193, 7, 0.45);
        }

        .fecha-caja .tit {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 800;
            color: var(--texto-suave);
            margin-bottom: 4px;
        }

        .fecha-caja.entrega .tit { color: #9c7a00; }

        .fecha-caja .val {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--cafe-oscuro);
            line-height: 1.35;
        }

        /* Desprendible de productos */
        .producto-fila {
            display: flex;
            gap: 13px;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dashed var(--borde);
        }

        .producto-fila:last-child { border-bottom: none; }

        .producto-img {
            width: 58px; height: 58px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid var(--borde);
            background: #faf6f0;
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }

        .prod-info { flex: 1; min-width: 0; }

        .prod-arriba {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 8px;
            margin-bottom: 6px;
        }

        .prod-nombre {
            font-size: 13px;
            font-weight: 800;
            color: var(--cafe-oscuro);
            text-transform: uppercase;
            line-height: 1.3;
        }

        .prod-cant {
            font-size: 11px;
            font-weight: 800;
            color: var(--cafe-oscuro);
            background: rgba(255, 193, 7, 0.22);
            border: 1px solid rgba(255, 193, 7, 0.45);
            padding: 1px 7px;
            border-radius: 6px;
            margin-right: 5px;
            white-space: nowrap;
        }

        .prod-precio {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--cafe);
            white-space: nowrap;
        }

        .prod-pastillas { display: flex; gap: 6px; flex-wrap: wrap; }

        .pastilla {
            background: #faf6f0;
            border: 1px solid #d9cfc2;
            padding: 3px 9px;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 600;
            color: var(--texto);
        }

        .pastilla strong { color: var(--cafe-oscuro); }

        /* Resumen de pago */
        .fila-pago {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13.5px;
            padding: 7px 0;
        }

        .fila-pago .lbl { color: var(--texto-suave); font-weight: 600; }

        .fila-pago .vlr { font-weight: 800; color: var(--cafe-oscuro); }

        .fila-total {
            margin-top: 8px;
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.16), rgba(236, 201, 152, 0.18));
            border: 1.5px solid rgba(255, 193, 7, 0.55);
            border-radius: 14px;
            padding: 14px 16px;
        }

        .fila-total .lbl {
            font-family: 'Baloo 2', sans-serif;
            color: var(--cafe-oscuro);
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .fila-total .vlr {
            font-family: 'Baloo 2', sans-serif;
            font-size: 21px;
            color: var(--cafe-oscuro);
        }

        .devuelta .lbl, .devuelta .vlr { color: var(--verde) !important; }

        /* Acciones */
        .acciones { display: flex; flex-direction: column; gap: 10px; margin-top: 26px; }


        .btn-neutro {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, var(--cafe), var(--cafe-oscuro));
            color: #ffffff; text-decoration: none;
            border: none; cursor: pointer;
            font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;
            border-radius: 13px;
            box-shadow: 0 5px 15px rgba(90, 75, 59, 0.35);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            font-family: inherit;
        }

        .btn-neutro:hover { transform: translateY(-2px); box-shadow: 0 9px 22px rgba(90, 75, 59, 0.45); }

        .pie-nota {
            text-align: center;
            font-size: 11.5px;
            color: var(--texto-suave);
            margin-top: 18px;
            line-height: 1.5;
        }

        /* Stepper de progreso */
        .stepper-compra {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            gap: 4px;
            max-width: 660px;
            margin: 0 auto;
            padding: 14px 16px 12px;
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid #eadfc8;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(90, 75, 59, 0.08);
        }
        .stepper-paso {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            text-align: center;
            cursor: default;
        }
        .stepper-circulo {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid #d8cfc2;
            background: #fffdf8;
            color: #a99f8f;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 600;
            line-height: 1;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
            box-shadow: 0 2px 6px rgba(90, 75, 59, 0.10);
        }
        .stepper-etiqueta {
            font-size: 11.5px;
            color: #a99f8f;
            margin-top: 7px;
            letter-spacing: 0.4px;
            font-weight: 400;
            white-space: nowrap;
        }
        .stepper-paso::before {
            content: '';
            position: absolute;
            top: 18px;
            left: calc(-50% + 18px);
            width: calc(100% - 36px);
            height: 3px;
            border-radius: 3px;
            background: #e5ddd0;
            z-index: 1;
        }
        .stepper-paso:first-child::before { display: none; }
        .stepper-paso.completado .stepper-circulo {
            background: linear-gradient(135deg, #43b673, #2e9e5b);
            border-color: #2e9e5b;
            color: #fff;
            box-shadow: 0 3px 10px rgba(46, 158, 91, 0.35);
        }
        .stepper-paso.completado::before { background: linear-gradient(90deg, #43b673, #2e9e5b); }
        .stepper-paso.completado .stepper-etiqueta { color: #2e9e5b; font-weight: 500; }
        .stepper-paso.activo .stepper-circulo {
            border-color: #e6a817;
            background: linear-gradient(135deg, #ffd86b, #ffc107);
            color: #5a4b3b;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.5);
            transform: scale(1.08);
        }
        .stepper-paso.activo::before { background: linear-gradient(90deg, #2e9e5b, #e6a817); }
        .stepper-paso.activo .stepper-etiqueta { color: #b98a17; font-weight: 600; }

        @media print {
            body { background: #ffffff; padding: 0; display: block; }
            .recibo { box-shadow: none; border: none; max-width: 100%; border-top-width: 4px; }
            .acciones { display: none; }
        }
    </style>
</head>
<body>

<div class="stepper-compra" style="max-width:640px; margin: 0 auto 14px;">
    <div class="stepper-paso completado"><div class="stepper-circulo">✓</div><span class="stepper-etiqueta">Registro</span></div>
    <div class="stepper-paso completado"><div class="stepper-circulo">✓</div><span class="stepper-etiqueta">Carrito</span></div>
    <div class="stepper-paso completado"><div class="stepper-circulo">✓</div><span class="stepper-etiqueta">Pago</span></div>
    <div class="stepper-paso activo"><div class="stepper-circulo">4</div><span class="stepper-etiqueta">Confirmación</span></div>
</div>

<div class="recibo">

    <!-- ENCABEZADO -->
    <div class="recibo-cabecera">
        <img src="logotipo.png" alt="STORE DANY" class="logo-recibo">
        <h1>&iexcl;PEDIDO CONFIRMADO!</h1>
        <p class="gracias-mensaje">¡Gracias por confiar en <strong>STORE DANY</strong>¡</p>
        <div class="chip-orden">
            <span>ORDEN</span>
            <strong><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo htmlspecialchars($codigoOrden); ?></strong>
        </div>
        <div class="chip-envio">ENV&Iacute;O GRATIS CONFIRMADO</div>
    </div>

    <div class="recibo-cuerpo">

        <!-- DATOS DEL CLIENTE -->
        <div class="rotulo-seccion">Datos del cliente</div>
        <div class="tarjeta-cliente">
            <p class="cliente-nombre"><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo htmlspecialchars($nombreCompleto); ?></p>
            <div class="cliente-linea">
                <span class="etiq">Documento</span>
                <span class="dato"><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo $cedulaCliente !== '' ? htmlspecialchars($cedulaCliente) : 'No registrado'; ?></span>
            </div>
            <div class="cliente-linea">
                <span class="etiq">Tel&eacute;fono</span>
                <span class="dato">
                    <?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); if ($telefonoCliente !== ''): ?>
                        <a href="https://wa.me/57<?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo preg_replace('/\D/', '', $telefonoCliente); ?>"><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo htmlspecialchars($telefonoCliente); ?></a>
                    <?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); else: ?>No registrado<?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); endif; ?>
                </span>
            </div>
            <div class="cliente-linea">
                <span class="etiq">Direcci&oacute;n</span>
                <span class="dato"><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo $direccionCliente !== '' ? htmlspecialchars($direccionCliente) : 'No registrada'; ?></span>
            </div>
        </div>

        <!-- FECHAS -->
        <div class="rotulo-seccion">Fechas del pedido</div>
        <div class="caja-fechas">
            <div class="fecha-caja">
                <div class="tit">Pago realizado</div>
                <div class="val"><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo htmlspecialchars($fechaPagoTxt); ?></div>
            </div>
            <div class="fecha-caja entrega">
                <div class="tit">Entrega estimada</div>
                <div class="val"><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo htmlspecialchars($fechaEntregaTxt); ?><br><small>(8 d&iacute;as h&aacute;biles)</small></div>
            </div>
        </div>

        <!-- DETALLE DEL PEDIDO -->
        <div class="rotulo-seccion">Detalle del pedido</div>
        <div>
            <?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); foreach ($productos as $item): ?>
                <div class="producto-fila">
                    <?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); if (!empty($item['imagen'])): ?>
                        <img src="<?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo htmlspecialchars($item['imagen']); ?>" alt="Producto" class="producto-img">
                    <?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); else: ?>
                        <div class="producto-img">&#128095;</div>
                    <?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); endif; ?>
                    <div class="prod-info">
                        <div class="prod-arriba">
                            <span class="prod-nombre"><span class="prod-cant"><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo intval($item['cantidad']); ?>x</span><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo htmlspecialchars($item['titulo']); ?></span>
                            <span class="prod-precio"><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo htmlspecialchars($item['precio']); ?></span>
                        </div>
                        <div class="prod-pastillas">
                            <span class="pastilla">Talla: <strong><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo htmlspecialchars($item['talla'] ?? 'Única'); ?></strong></span>
                            <span class="pastilla">Color: <strong><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo htmlspecialchars($item['color'] ?? 'Original'); ?></strong></span>
                        </div>
                    </div>
                </div>
            <?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); endforeach; ?>
        </div>

        <!-- RESUMEN DE PAGO -->
        <div class="rotulo-seccion">Resumen del pago</div>
        <div class="payment-summary">
            <div class="fila-pago">
                <span class="lbl">M&eacute;todo de pago</span>
                <span class="vlr"><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo htmlspecialchars($metodoPago); ?></span>
            </div>
            <?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); if ($cuentaVisible !== ''): ?>
            <div class="fila-pago">
                <span class="lbl">Cuenta desde donde paga</span>
                <span class="vlr"><?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo htmlspecialchars($cuentaVisible); ?></span>
            </div>
            <?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); endif; ?>
            <div class="fila-pago">
                <span class="lbl">Monto recibido</span>
                <span class="vlr">$<?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo number_format($montoPagado > 0 ? $montoPagado : $total, 0, ',', '.'); ?> COP</span>
            </div>
            <?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); if ($devuelta > 0): ?>
            <div class="fila-pago devuelta">
                <span class="lbl">Cambio / Devuelta</span>
                <span class="vlr">$<?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo number_format($devuelta, 0, ',', '.'); ?> COP</span>
            </div>
            <?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); endif; ?>
            <div class="fila-pago fila-total">
                <span class="lbl">TOTAL COMPRA</span>
                <span class="vlr">$<?php
// Hora oficial de Colombia para todo el flujo de compra
date_default_timezone_set('America/Bogota'); echo number_format($total, 0, ',', '.'); ?> COP</span>
            </div>
        </div>

        <!-- ACCIONES -->
        <div class="acciones">
            <button class="btn-neutro" onclick="window.print()">
                Imprimir comprobante
            </button>
            <a href="index.html" class="btn-neutro">
                Volver a la tienda
            </a>
        </div>

        <p class="pie-nota">
            STORE DANY - Calzado exclusivo para hombre<br>
            Calle 8 # 19 - 51, San Vicente de Chucur&iacute;, Santander
        </p>

    </div>
</div>

</body>
</html>
