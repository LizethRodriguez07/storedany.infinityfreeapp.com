<?php
// ⏰ 1. CONFIGURACIÓN HORARIA OFICIAL DE COLOMBIA
date_default_timezone_set('America/Bogota');

// 🔒 2. CONTROL DE ACCESO (Credenciales autorizadas para el negocio)
$USUARIO_ADMIN = "storedany_admin";
$CLAVE_ADMIN   = "Dany2026*";

session_start();

// Lógica para cerrar sesión
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Verificar inicio de sesión
if (isset($_POST['login'])) {
    $user_input = $_POST['username'] ?? '';
    $pass_input = $_POST['password'] ?? '';
    if ($user_input === $USUARIO_ADMIN && $pass_input === $CLAVE_ADMIN) {
        $_SESSION['admin_logeado'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error_login = "Usuario o contraseña incorrectos.";
    }
}

// 🌐 3. ENTORNO DINÁMICO DE CONEXIÓN A LA BASE DE DATOS
if ($_SERVER['HTTP_HOST'] == 'localhost:8080' || $_SERVER['HTTP_HOST'] == '127.0.0.1:8080') {
    $host = 'base_datos'; 
    $db   = 'gst_ventasonline'; 
    $user = 'root';              
    $pass = 'clave_storedany_2026'; 
} else {
    // Al subir a tu hosting, edita estas 4 líneas con tus datos reales de cPanel
    $host = 'localhost'; 
    $host = 'sql201.infinityfree.com';
    $db   = 'if0_41988386_gst_ventasonline';
    $user = 'if0_41988386';
    $pass = 'NJvVj32GYWri';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (\PDOException $e) {
    die("Error crítico de conexión: " . $e->getMessage());
}

// 🪠 4. LÓGICA PARA VACIAR EL HISTORIAL COMERCIAL (Directa y limpia)
if (isset($_POST['vaciar_historial'])) {
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec("TRUNCATE TABLE detallpago;");
        $pdo->exec("TRUNCATE TABLE pagos;");
        $pdo->exec("TRUNCATE TABLE pedidos;");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
        
        header("Location: admin.php?status=vaciado");
        exit();
    } catch (\Exception $e) {
        echo "Error al vaciar: " . $e->getMessage();
    }
}

// SI NO ESTÁ LOGEADO MUESTRA LA TARJETA DE LOGIN ELEGANTES
if (!isset($_SESSION['admin_logeado'])) {
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STORE DANY · Acceso Administrador</title>
    <link rel="icon" type="image/png" href="logotipo.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;800&family=Inter:wght@400;500;600;700&display=swap');

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh; margin: 0; padding: 24px;
            display: flex; align-items: center; justify-content: center;
            position: relative;
        }
        body::before {
            content: ''; position: fixed; inset: 0; z-index: -2;
            background: url('zapatos.jpg') center/cover no-repeat;
        }
        body::after {
            content: ''; position: fixed; inset: 0; z-index: -1;
            background: linear-gradient(135deg, rgba(63,52,40,.93), rgba(90,75,59,.87));
        }

        .tarjeta-login {
            width: 100%; max-width: 430px; background: #fffdf9;
            border-radius: 24px; overflow: hidden; border: 1px solid #e8ddd0;
            box-shadow: 0 30px 70px rgba(20,15,10,.55);
            animation: entrar .5s ease both;
        }
        @keyframes entrar { from { opacity: 0; transform: translateY(22px); } to { opacity: 1; transform: none; } }
        .tarjeta-login.tarjeta-sacude { animation: sacudir .45s ease both !important; }
        @keyframes sacudir {
            0%, 100% { transform: none; } 20% { transform: translateX(-9px); }
            40% { transform: translateX(8px); } 60% { transform: translateX(-6px); } 80% { transform: translateX(4px); }
        }

        .login-cabecera {
            background: linear-gradient(135deg, #3f3428 0%, #5a4b3b 60%, #6f5c47 100%);
            text-align: center; padding: 38px 28px 26px; position: relative;
        }
        .login-cabecera::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, #ffc107, #ecc998, #ffc107);
        }
        .login-logo {
            width: min(230px, 74%); height: auto; display: block; margin: 0 auto 12px;
            filter: drop-shadow(0 6px 16px rgba(255,193,7,.38));
        }
        .login-titulo {
            font-family: 'Baloo 2', sans-serif; font-size: 21px; font-weight: 800;
            color: #ffe9a8; margin: 0; letter-spacing: .5px;
        }
        .login-subtitulo { font-size: 12.5px; color: #d9c9ae; margin: 6px 0 0; letter-spacing: .4px; }

        .login-cuerpo { padding: 30px 32px 26px; }
        .form-group { margin-bottom: 18px; text-align: left; }
        .form-group label {
            display: block; font-size: 11px; color: #857059; font-weight: 700;
            text-transform: uppercase; letter-spacing: .8px; margin-bottom: 7px;
        }
        .campo { position: relative; }
        .form-group input {
            width: 100%; padding: 13px 44px 13px 14px; border: 1px solid #e8ddd0;
            border-radius: 11px; font-size: 15px; color: #3a2e24; background: #faf6f0;
            transition: all .2s ease;
        }
        .form-group input:focus {
            outline: none; border-color: #b99a56; background: #fff;
            box-shadow: 0 0 0 3px rgba(255,193,7,.16);
        }
        .btn-ojo {
            position: absolute; top: 50%; right: 8px; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; font-size: 17px;
            line-height: 1; color: #a38c75; padding: 6px; border-radius: 8px; transition: color .2s;
        }
        .btn-ojo:hover { color: #5a4b3b; }

        .aviso-caps {
            display: none; align-items: center; gap: 6px; margin-top: 7px;
            background: #fff3e0; color: #e65100; border: 1px solid #ffe0b2;
            border-radius: 8px; padding: 6px 10px; font-size: 12px; font-weight: 600;
        }
        .aviso-bloqueo {
            display: none; align-items: center; justify-content: center; gap: 8px;
            background: #fce4ec; color: #c62828; border: 1px solid #f5c6cb;
            border-radius: 10px; padding: 10px; font-size: 13px; font-weight: 700; margin-bottom: 14px;
        }
        .error {
            color: #c62828; background-color: #fce4ec; padding: 10px 12px;
            border-radius: 10px; font-size: 13px; font-weight: 600; margin-bottom: 14px;
            border: 1px solid #f5c6cb; text-align: center;
        }

        .btn-login {
            position: relative; width: 100%; padding: 14px; border: none; border-radius: 11px;
            background: linear-gradient(135deg, #857059, #5a4b3b); color: #fff;
            font-weight: 700; font-size: 13px; letter-spacing: .9px; text-transform: uppercase;
            cursor: pointer; margin-top: 6px; font-family: inherit;
            box-shadow: 0 6px 18px rgba(90,75,59,.3); transition: all .25s ease;
        }
        .btn-login:hover:not(:disabled) {
            transform: translateY(-2px); background: linear-gradient(135deg, #9a826a, #6f5c47);
            box-shadow: 0 10px 26px rgba(90,75,59,.4);
        }
        .btn-login:disabled { opacity: .55; cursor: not-allowed; }
        .btn-login.cargando { pointer-events: none; }
        .btn-login.cargando::after {
            content: ''; width: 15px; height: 15px; border: 2px solid rgba(255,255,255,.4);
            border-top-color: #ffc107; border-radius: 50%; display: inline-block;
            margin-left: 9px; vertical-align: -2px; animation: girar .7s linear infinite;
        }
        @keyframes girar { to { transform: rotate(360deg); } }

        .link-back {
            display: inline-block; margin-top: 18px; color: #857059;
            text-decoration: none; font-size: 13.5px; font-weight: 600; transition: color .2s;
        }
        .link-back:hover { color: #3f3428; }
        .pie-login {
            margin-top: 16px; padding-top: 14px; border-top: 1px dashed #eee2cf;
            font-size: 11.5px; color: #b3a48f; text-align: center;
        }
    </style>
</head>
<body>
<div class="tarjeta-login" id="tarjeta">
    <div class="login-cabecera">
        <img src="logotipo.png" alt="STORE DANY" class="login-logo">
        <h1 class="login-titulo">Panel de Control</h1>
        <p class="login-subtitulo">Acceso exclusivo del equipo STORE DANY</p>
    </div>
    <div class="login-cuerpo">
        <?php if (isset($error_login)): ?>
            <div class="error">&#9888; <?php echo $error_login; ?></div>
        <?php endif; ?>

        <div class="aviso-bloqueo" id="aviso-bloqueo"></div>

        <form method="POST">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <div class="campo">
                    <input type="password" name="password" id="campo-pass" required autocomplete="current-password">
                    <button type="button" class="btn-ojo" id="btn-ojo" title="Mostrar contraseña">&#128065;</button>
                </div>
                <div class="aviso-caps" id="aviso-caps">&#9684; Bloq Mayús está activado</div>
            </div>
            <button type="submit" name="login" class="btn-login">Ingresar al Sistema</button>
        </form>

        <a href="index.html" class="link-back">&larr; Volver a la Tienda</a>
        <div class="pie-login">© <?php echo date('Y'); ?> STORE DANY · Todos los derechos reservados</div>
    </div>
</div>

<script>
(function () {
    var pass = document.getElementById('campo-pass'),
        ojo = document.getElementById('btn-ojo'),
        caps = document.getElementById('aviso-caps'),
        form = document.querySelector('form'),
        btn = document.querySelector('.btn-login'),
        tarjeta = document.getElementById('tarjeta');

    ojo.addEventListener('click', function () {
        var ver = pass.type === 'password';
        pass.type = ver ? 'text' : 'password';
        ojo.innerHTML = ver ? '&#128064;' : '&#128065;';
        ojo.title = ver ? 'Ocultar contraseña' : 'Mostrar contraseña';
        pass.focus();
    });

    function capsChk(e) {
        if (e.getModifierState) caps.style.display = e.getModifierState('CapsLock') ? 'flex' : 'none';
    }
    ['keydown', 'keyup'].forEach(function (ev) { pass.addEventListener(ev, capsChk); });
    pass.addEventListener('blur', function () { caps.style.display = 'none'; });

    var KF = 'sd_fallos_login', KT = 'sd_bloqueo_hasta';
    var fallos = parseInt(sessionStorage.getItem(KF) || '0', 10);
    var hasta = parseInt(sessionStorage.getItem(KT) || '0', 10);

    <?php if (isset($error_login)): ?>
    tarjeta.classList.add('tarjeta-sacude');
    fallos++;
    if (fallos >= 3) { hasta = Date.now() + 30000; sessionStorage.setItem(KT, String(hasta)); }
    sessionStorage.setItem(KF, String(fallos));
    <?php else: ?>
    sessionStorage.removeItem(KF); sessionStorage.removeItem(KT);
    <?php endif; ?>

    var aviso = document.getElementById('aviso-bloqueo');
    function refrescaBloqueo() {
        var resta = Math.ceil((hasta - Date.now()) / 1000);
        var campos = form.querySelectorAll('input, button');
        if (resta > 0) {
            aviso.style.display = 'flex';
            aviso.innerHTML = '&#9888; Demasiados intentos fallidos. Reintenta en ' + resta + ' s';
            campos.forEach(function (c) { c.disabled = true; });
            setTimeout(refrescaBloqueo, 400);
        } else {
            aviso.style.display = 'none';
            campos.forEach(function (c) { c.disabled = false; });
        }
    }
    refrescaBloqueo();

    form.addEventListener('submit', function () {
        if (!btn.disabled) btn.classList.add('cargando');
    });
})();
</script>
</body></html>
<?php
exit();
}   

// 🔍 5. CONSULTA LOGÍSTICA AVANZADA (Relaciona pedidos con datos del comprador)
$sql_pedidos = "SELECT p.id AS orden_id, p.fecha_pedido, p.total, c.nombre, c.apellidos, c.cedula, c.celular, c.departamento, c.municipio, c.direccion, pag.estado, pag.monto 
                FROM pedidos p
                INNER JOIN clientes c ON p.id_cliente = c.id
                INNER JOIN pagos pag ON pag.id_pedido = p.id
                ORDER BY p.id DESC";
$pedidos = $pdo->query($sql_pedidos)->fetchAll(PDO::FETCH_ASSOC);
// KPIs de ventas para el panel
$hoy = date('Y-m-d');
$mesActual = date('Y-m');
$ventasHoy = 0; $ventasMes = 0; $ventasTotal = 0;
foreach ($pedidos as $kpiRow) {
    $fKpi = new DateTime($kpiRow['fecha_pedido']);
    $tKpi = floatval($kpiRow['total']);
    $ventasTotal += $tKpi;
    if ($fKpi->format('Y-m-d') === $hoy) { $ventasHoy += $tKpi; }
    if ($fKpi->format('Y-m') === $mesActual) { $ventasMes += $tKpi; }
}
$ticketPromedio = count($pedidos) > 0 ? $ventasTotal / count($pedidos) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Logística y Despachos - Store Dany</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        :root {
            --bg-main: #f5efe6;
            --bg-card: #ffffff;
            --bg-warm: #faf6f0;
            --text-main: #3a2e24;
            --text-body: #5a4b3b;
            --text-muted: #8a7a6a;
            --accent: #857059;
            --accent-dark: #5a4b3b;
            --accent-light: #a38c75;
            --dorado: #ffc107;
            --dorado-suave: #ecc998;
            --dorado-bg: #fef9ec;
            --border: #e8ddd0;
            --border-light: #f0e8dc;
            --success: #2e7d32;
            --success-bg: #e8f5e9;
            --warning: #e65100;
            --warning-bg: #fff3e0;
            --danger: #c62828;
            --danger-bg: #fce4ec;
            --shadow-sm: 0 2px 8px rgba(90, 75, 59, 0.06);
            --shadow-md: 0 8px 30px rgba(90, 75, 59, 0.08);
            --shadow-lg: 0 20px 50px rgba(90, 75, 59, 0.12);
            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 24px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-main);
            background-image: 
                radial-gradient(ellipse at 20% 50%, rgba(133, 112, 89, 0.04) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(255, 193, 7, 0.03) 0%, transparent 50%);
            padding: 40px 20px;
            color: var(--text-main);
            margin: 0;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
        }

        /* ===== HEADER ===== */
        .header-panel {
            background: var(--bg-card);
            padding: 28px 40px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 35px;
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .header-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), var(--dorado), var(--accent));
        }

        .header-panel:hover {
            box-shadow: var(--shadow-lg);
            border-color: var(--dorado-suave);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .header-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--bg-warm), var(--border-light));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .header-panel:hover .header-icon {
            transform: translateY(-3px) rotate(5deg);
            box-shadow: 0 8px 20px rgba(133, 112, 89, 0.12);
        }

        .header-title {
            margin: 0;
            font-size: 22px;
            color: var(--text-main);
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .header-subtitle {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 4px 0 0 0;
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: var(--dorado);
            border-radius: 50%;
            animation: pulse 2s infinite;
            box-shadow: 0 0 8px rgba(255, 193, 7, 0.4);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.7; }
        }

        .guia-count {
            color: var(--accent);
            background: var(--bg-warm);
            padding: 3px 10px;
            border-radius: 6px;
            border: 1px solid var(--border);
            font-size: 12px;
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            padding: 12px 22px;
            border-radius: var(--radius-sm);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
        }

        .btn-danger {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid #f5c6cb;
        }

        .btn-danger:hover {
            background: var(--danger);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(198, 40, 40, 0.25);
        }

        .btn-accent {
            background: var(--accent-dark);
            color: #fff;
            box-shadow: 0 4px 14px rgba(90, 75, 59, 0.2);
        }

        .btn-accent:hover {
            background: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(133, 112, 89, 0.3);
        }

        /* ===== ALERTS ===== */
        .alert {
            padding: 16px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 25px;
            font-weight: 600;
            text-align: center;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success);
            border: 1px solid #c8e6c9;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 80px 40px;
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 2px dashed var(--border);
            color: var(--text-muted);
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 16px;
            margin: 0;
            font-weight: 500;
        }

        /* ===== ORDER CARD ===== */
        .orden-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            margin-bottom: 30px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .orden-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--dorado-suave);
        }

        .orden-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--bg-warm);
            padding: 20px 30px;
            border-bottom: 1px solid var(--border);
            position: relative;
        }

        .orden-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 30px;
            right: 30px;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--dorado-suave), transparent);
        }

        .badge-guia {
            font-size: 11px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            padding: 8px 18px;
            border-radius: 10px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            box-shadow: 0 4px 14px rgba(133, 112, 89, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .orden-card:hover .badge-guia {
            animation: shine 1.5s linear infinite;
            background-size: 200% auto;
            background-image: linear-gradient(90deg, var(--accent) 0%, var(--accent-light) 25%, var(--accent) 50%, var(--accent-light) 75%, var(--accent) 100%);
        }

        @keyframes shine {
            to { background-position: 200% center; }
        }

        .orden-date {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-card);
            padding: 8px 16px;
            border-radius: 10px;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .orden-card:hover .orden-date {
            border-color: var(--dorado-suave);
            color: var(--text-main);
        }

        .orden-date strong {
            color: var(--accent-dark);
            font-weight: 600;
        }

        /* ===== GRID ===== */
        .grid-logistica {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 0;
        }

        @media (max-width: 768px) {
            .grid-logistica { grid-template-columns: 1fr; }
            .col-cliente { border-right: none !important; border-bottom: 1px solid var(--border); }
            .header-panel { flex-direction: column; gap: 16px; text-align: center; }
            .header-actions { justify-content: center; }
        }

        /* ===== LEFT COLUMN ===== */
        .col-cliente {
            padding: 28px 30px;
            border-right: 1px solid var(--border-light);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }

        .section-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 4px 12px rgba(133, 112, 89, 0.2);
        }

        .section-label {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent);
            font-weight: 700;
        }

        .section-desc {
            margin: 2px 0 0 0;
            font-size: 12px;
            color: var(--text-muted);
        }

        .data-row {
            margin: 4px 0;
            padding: 12px 14px;
            border-radius: var(--radius-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px dashed var(--border-light);
            transition: all 0.2s ease;
            cursor: default;
        }

        .data-row:last-child { border-bottom: none; }

        .data-row:hover {
            background: var(--bg-warm);
            transform: translateX(4px);
        }

        .data-row.whatsapp:hover {
            background: #e8f5e9;
        }

        .data-label {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 13px;
        }

        .data-value {
            color: var(--text-main);
            font-weight: 700;
            font-size: 13px;
            text-align: right;
            max-width: 220px;
            line-height: 1.3;
        }

        .data-value.accent {
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .chat-badge {
            font-size: 10px;
            background: var(--success-bg);
            color: var(--success);
            padding: 2px 7px;
            border-radius: 4px;
            font-weight: 700;
        }

        .whatsapp-link {
            color: var(--success);
            font-weight: 700;
            text-decoration: underline;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ===== RIGHT COLUMN ===== */
        .col-productos {
            padding: 28px 30px;
        }

        .products-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            padding-bottom: 14px;
            border-bottom: 2px solid var(--border-light);
        }

        .products-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .products-icon {
            width: 34px;
            height: 34px;
            background: var(--bg-warm);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            font-size: 16px;
        }

        .products-title {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent);
            font-weight: 800;
        }

        .control-badge {
            font-size: 10px;
            background: var(--bg-warm);
            color: var(--text-muted);
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid var(--border);
        }

        .product-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 18px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            transition: all 0.3s ease;
        }

        .product-card:last-child { margin-bottom: 0; }

        .product-card:hover {
            border-color: var(--accent);
            box-shadow: 0 6px 20px rgba(133, 112, 89, 0.08);
        }

        .product-left {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            flex: 1;
        }

        .check-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 70px;
            text-align: center;
            margin-top: 2px;
        }

        .check-col input[type="checkbox"] {
            width: 22px;
            height: 22px;
            accent-color: var(--accent);
            cursor: pointer;
            border-radius: 6px;
            transition: transform 0.15s ease;
        }

        .check-col input[type="checkbox"]:hover {
            transform: scale(1.15);
        }

        .check-label {
            font-size: 8px;
            color: var(--accent);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-top: 6px;
            display: block;
            max-width: 60px;
            user-select: none;
        }

        .product-name {
            font-weight: 800;
            color: var(--accent-dark);
            display: block;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.3px;
            margin-bottom: 8px;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
        }

        .product-specs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            user-select: none;
        }

        .spec-tag {
            font-size: 11px;
            color: var(--text-body);
            background: var(--bg-warm);
            padding: 5px 10px;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .spec-tag strong {
            color: var(--accent);
            font-weight: 700;
        }

        .product-price {
            text-align: right;
            min-width: 100px;
        }

        .product-price-label {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .product-price-value {
            font-weight: 800;
            color: var(--accent-dark);
            font-size: 16px;
        }

        /* ===== PROGRESS BAR ===== */
        .progress-section {
            margin-top: 28px;
        }

        .progress-bar-bg {
            background: var(--border);
            height: 8px;
            border-radius: 50px;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(90, 75, 59, 0.08);
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 50px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .progress-bar-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: barShine 2s infinite;
        }

        @keyframes barShine {
            from { transform: translateX(-150%); }
            to { transform: translateX(300%); }
        }

        /* ===== FOOTER ===== */
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 30px;
            border-top: 1px solid var(--border-light);
            background: var(--bg-warm);
        }

        .status-badge {
            padding: 7px 18px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            animation: badgePulse 3s infinite ease-in-out;
        }

        @keyframes badgePulse {
            0%, 100% { box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
            50% { box-shadow: 0 4px 16px rgba(133, 112, 89, 0.12); }
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        .total-section {
            text-align: right;
        }

        .total-label {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.8px;
            margin-bottom: 2px;
        }

        .total-value {
            font-size: 26px;
            font-weight: 900;
            color: var(--accent-dark);
            letter-spacing: -0.5px;
            transition: all 0.2s ease;
        }

        .card-footer:hover .total-value {
            color: var(--accent);
            transform: scale(1.02);
        }

        .total-currency {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            margin-left: 3px;
        }

        /* ===== BACK BUTTON ===== */
        .btn-volver {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 30px;
            text-decoration: none;
            color: var(--accent);
            font-size: 14px;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: var(--bg-warm);
            transition: all 0.25s ease;
        }

        .btn-volver:hover {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(133, 112, 89, 0.25);
        }
    </style>
<style>
        @import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&display=swap');

        /* ===== MEJORAS PANEL: KPIS, FILTROS, PLEGABLES, DORADO ===== */
        .zona-titulo {
            font-family: 'Baloo 2', sans-serif; font-size: 13.5px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 2.5px; color: var(--accent-dark);
            display: flex; align-items: center; gap: 12px; margin: 26px 0 16px;
        }
        .zona-titulo::before { content: ''; width: 26px; height: 4px; border-radius: 3px;
            background: linear-gradient(90deg, #ffc107, #ecc998); }
        .kpis-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin: 0 0 8px; }
        @media (min-width: 1000px) { .kpis-grid { grid-template-columns: repeat(3, 1fr); } }
        .kpi-card {
            background: var(--bg-card); border: 1px solid #e8ddd0;
            border-left: 5px solid #ffc107; border-radius: 18px;
            padding: 22px 24px; min-height: 96px; display: flex; align-items: center; gap: 16px;
            box-shadow: 0 8px 22px rgba(90, 75, 59, 0.10); transition: all 0.25s ease;
        }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 12px 26px rgba(90, 75, 59, 0.16); }
        .kpi-icono { font-size: 26px; }
        .kpi-info { display: flex; flex-direction: column; }
        .kpi-valor { font-family: 'Baloo 2', sans-serif; font-size: 20px; font-weight: 800; color: #3a2e24; line-height: 1.1; }
        .kpi-etiqueta { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); font-weight: 700; margin-top: 3px; }

        .barra-filtros { display: flex; gap: 12px; align-items: center; flex-wrap: wrap;
            background: var(--bg-warm); border: 1px solid #e8ddd0;
            border-radius: 16px; padding: 16px 20px; margin: 20px 0 10px; }
        .input-buscador { flex: 1; min-width: 220px; padding: 11px 14px; border: 1px solid #e8ddd0; border-radius: 10px; font-size: 13.5px; background: #fff; color: #3a2e24; outline: none; transition: all 0.2s ease; }
        .input-buscador:focus { border-color: #857059; box-shadow: 0 0 0 3px rgba(133, 112, 89, 0.12); }
        .select-filtro { padding: 11px 12px; border: 1px solid #e8ddd0; border-radius: 10px; background: #fff; color: #5a4b3b; font-weight: 600; font-size: 13px; cursor: pointer; }
        .resultados-contador { font-size: 12px; color: var(--accent-dark); font-weight: 700; letter-spacing: 0.4px; }

        .orden-header { cursor: pointer; user-select: none; }
        .orden-header:hover { filter: brightness(1.03); }
        .plegable-flecha { margin-left: auto; font-size: 15px; color: #ffc107; transition: transform 0.3s ease; text-shadow: 0 1px 4px rgba(255, 193, 7, 0.35); }
        .orden-card.colapsada .grid-logistica,
        .orden-card.colapsada .card-footer { display: none; }
        .orden-card.colapsada .plegable-flecha { transform: rotate(-90deg); }
        .orden-card { border-top: 3px solid rgba(255, 193, 7, 0.55); }
        .header-title { font-family: 'Baloo 2', sans-serif !important; letter-spacing: 0.5px; }
        .badge-guia { background: linear-gradient(135deg, #ffc107, #ecc998) !important; color: #3f3428 !important; animation: none !important; text-shadow: none !important; }
        /* ===== MODO AMPLIO: RESPIRO VISUAL ===== */
        .container { max-width: 1240px; padding: 44px 28px 70px; }
        .header-panel { margin-bottom: 30px; padding: 30px 42px; }
        .kpis-grid { gap: 18px; margin: 22px 0; }
        .kpi-card { padding: 19px 22px; }
        .barra-filtros { margin-bottom: 26px; padding: 15px 18px; }
        .orden-card { margin-bottom: 36px; }
        .grid-logistica { gap: 20px; }
        .col-cliente { padding: 30px 34px; }
        .col-productos { padding: 30px 34px; }
        .data-row { padding: 13px 16px; margin: 6px 0; }
        .product-card { padding: 20px 22px; margin-bottom: 16px; }
        @media (max-width: 768px) {
            .container { padding: 26px 16px 50px; }
            .col-cliente, .col-productos { padding: 22px 20px; }
        }
    </style>
</head>
<body>

<div class="container">

    <style>
        @keyframes FloatIcon {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-4px) rotate(5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        .header-panel:hover .header-icon {
            animation: FloatIcon 1.5s ease infinite alternate;
        }
    </style>

    <!-- ===== HEADER ===== -->
    <div class="header-panel">
        <div class="header-left">
            <div class="header-icon">📦</div>
            <div>
                <h2 class="header-title">Panel de Historial de Pedidos</h2>
                <div class="header-subtitle">
                    <span class="pulse-dot"></span>
                    Sistema de Gestión de Ventas (STORE DANY) • 
                    <span class="guia-count"><?php echo count($pedidos); ?> Guías Cargadas</span>
                </div>
            </div>
        </div>
        <div class="header-actions">
            <?php if (!empty($pedidos)): ?>
                <form method="POST" onsubmit="return confirm('¿Estás seguro de vaciar el historial completo?');" style="margin: 0;">
                    <button type="submit" name="vaciar_historial" class="btn btn-danger">
                        🗑️ Vaciar Todo
                    </button>
                </form>
            <?php endif; ?>
            <a href="admin.php?action=logout" class="btn btn-accent">Salir</a>
        </div>
    </div>

<!-- ===== KPIS DE VENTAS ===== -->
    <div class="kpis-grid">
        <div class="kpi-card">
            <div class="kpi-icono">&#128176;</div>
            <div class="kpi-info">
                <span class="kpi-valor">$<?php echo number_format($ventasHoy, 0, ',', '.'); ?></span>
                <span class="kpi-etiqueta">Ventas de hoy</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icono">&#128200;</div>
            <div class="kpi-info">
                <span class="kpi-valor">$<?php echo number_format($ventasMes, 0, ',', '.'); ?></span>
                <span class="kpi-etiqueta">Ventas del mes</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icono">&#128230;</div>
            <div class="kpi-info">
                <span class="kpi-valor"><?php echo count($pedidos); ?></span>
                <span class="kpi-etiqueta">Pedidos totales</span>
            </div>
        </div>
    </div>

    <!-- ===== BUSCADOR Y FILTROS ===== -->
    <div class="barra-filtros">
        <input type="text" id="buscador-pedidos" class="input-buscador" placeholder="&#128269; Buscar por cliente, cedula o numero de guia...">
        <span class="resultados-contador" id="contador-resultados"></span>
    </div>
    <!-- ===== ALERTS ===== -->
    <?php if (isset($_GET['status']) && $_GET['status'] == 'vaciado'): ?>
        <div class="alert alert-success">✔️ El historial logístico ha sido reiniciado con éxito.</div>
    <?php endif; ?>

    <div class="zona-titulo">Guias de despacho</div>
    <?php if (empty($pedidos)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <p>No hay guías de despacho pendientes en el sistema.</p>
        </div>
    <?php else: ?>

        <?php foreach ($pedidos as $row): ?>

            <?php
                $estado_limpio = strtolower(trim($row['estado'] ?? 'completado'));
                $color_estado = 'var(--success)';
                $bg_estado = 'var(--success-bg)';
                $ancho_barra = '100%';

                if (strpos($estado_limpio, 'pend') !== false || strpos($estado_limpio, 'espera') !== false) {
                    $color_estado = 'var(--warning)';
                    $bg_estado = 'var(--warning-bg)';
                    $ancho_barra = '50%';
                } elseif (strpos($estado_limpio, 'canc') !== false || strpos($estado_limpio, 'devu') !== false) {
                    $color_estado = 'var(--danger)';
                    $bg_estado = 'var(--danger-bg)';
                    $ancho_barra = '100%';
                }
            ?>



            <div class="orden-card colapsada" data-busqueda="<?php echo strtolower(htmlspecialchars(($row['nombre'] ?? '') . ' ' . ($row['apellidos'] ?? '') . ' ' . ($row['cedula'] ?? '') . ' ' . $row['orden_id'])); ?>">
                
                <!-- Encabezado -->
                <div class="orden-header" onclick="toggleOrden(this)" title="Clic para ver u ocultar el detalle"><span class="plegable-flecha">&#9662;</span>
                    <span class="badge-guia">⚡ Guía de Despacho #<?php echo $row['orden_id']; ?></span>
                    <span class="orden-date">
                        📅 Fecha de compra: <strong> <?php echo (new DateTime($row['fecha_pedido']))->format('d/m/Y h:i A'); ?> 
                        </strong>
                    </span>
                </div>

                <!-- Grid -->
                <div class="grid-logistica">
                    
                    <!-- Izquierda: Datos del cliente -->
                    <div class="col-cliente">
                        <div class="section-header">
                            <div class="section-avatar">
                                <?php 
                                    $iniciales = strtoupper(substr($row['nombre'] ?? 'U', 0, 1) . substr($row['apellidos'] ?? 'N', 0, 1));
                                    echo $iniciales;
                                ?>
                            </div>
                            <div>
                                <h4 class="section-label">📍 Información de Despacho</h4>
                                <p class="section-desc">Verifica los datos antes de enviar</p>
                            </div>
                        </div>

                        <div class="data-row">
                            <span class="data-label">Cliente:</span>
                            <span class="data-value"><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']); ?></span>
                        </div>

                        <div class="data-row">
                            <span class="data-label">Cédula:</span>
                            <span class="data-value" style="font-family: monospace;"><?php echo htmlspecialchars($row['cedula']); ?></span>
                        </div>

                        <div class="data-row whatsapp" 
                             onclick="window.open('https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $row['celular']); ?>?text=Hola%20<?php echo urlencode($row['nombre']); ?>,%20te%20escribimos%20de%20Store%20Dany%20sobre%20tu%20gu%C3%ADa%20%23<?php echo $row['orden_id']; ?>', '_blank');"
                             title="Abrir chat en WhatsApp" style="cursor: pointer;">
                            <span class="data-label">Contacto: <span class="chat-badge">CHAT</span></span>
                            <span class="whatsapp-link">💬 <?php echo htmlspecialchars($row['celular']); ?></span>
                        </div>

                        <div class="data-row">
                            <span class="data-label">Dirección:</span>
                            <span class="data-value"><?php echo htmlspecialchars($row['direccion']); ?></span>
                        </div>

                        <div class="data-row">
                            <span class="data-label">Destino:</span>
                            <span class="data-value accent"><?php echo htmlspecialchars($row['municipio']); ?> — <?php echo htmlspecialchars($row['departamento']); ?></span>
                        </div>
                    </div>

                    <!-- Derecha: Productos -->
                    <div class="col-productos">
                        <div class="products-header">
                            <div class="products-header-left">
                                <div class="products-icon">👟</div>
                                <h4 class="products-title">Especificaciones de Empaque</h4>
                            </div>
                            <span class="control-badge">Control Interno</span>
                        </div>

                        
<?php
$sql_items = "SELECT d.cantidad, d.precio_unitario, d.talla, d.color, d.marca, p.nombre AS titulo 
FROM detallpago d
LEFT JOIN producto p ON d.id_producto = p.id
WHERE d.id_pedido = :pedido_id";

$stmt_items = $pdo->prepare($sql_items);
$stmt_items->execute([':pedido_id' => $row['orden_id']]);
$items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as $index => $item): 
$checkbox_id = "check_" . $row['orden_id'] . "_" . $index;
?>
                        
                            <div class="product-card">
                                <div class="product-left">
                                    <div class="check-col">
                                        <input type="checkbox" id="<?php echo $checkbox_id; ?>"
                                               onclick="const lbl = document.querySelector('label[for=<?php echo $checkbox_id; ?>]'); const specs = lbl.nextElementSibling; if(this.checked){lbl.style.opacity='0.35';lbl.style.textDecoration='line-through';specs.style.opacity='0.35';}else{lbl.style.opacity='1';lbl.style.textDecoration='none';specs.style.opacity='1';}">
                                        <span class="check-label">Marcar al empacar</span>
                                    </div>
                                    <div>
                                        <span class="product-name"><?php echo htmlspecialchars($item['marca'] ?? 'Producto Store Dany'); ?></span>
                                        <div class="product-specs">
                                            <span class="spec-tag">📐 Talla: <strong><?php echo htmlspecialchars($item['talla'] ?? 'Única'); ?></strong></span>
                                            <span class="spec-tag">🎨 Color: <strong><?php echo htmlspecialchars($item['color'] ?? 'Original'); ?></strong></span>
                                            <span class="spec-tag">📦 Cant: <strong><?php echo $item['cantidad']; ?> ud</strong></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="product-price">
                                    <div class="product-price-label">Subtotal</div>
                                    <div class="product-price-value">$<?php echo number_format($item['precio_unitario'] * $item['cantidad'], 0, ',', '.'); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Barra de progreso -->
                        <div class="progress-section">
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: <?php echo $ancho_barra; ?>; background: <?php echo $color_estado; ?>;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="card-footer">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span class="data-label">Estado:</span>
                        <span class="status-badge" style="background-color: <?php echo $bg_estado; ?>; color: <?php echo $color_estado; ?>;">
                            <span class="status-dot" style="background-color: <?php echo $color_estado; ?>;"></span>
                            <?php echo htmlspecialchars($row['estado']); ?>
                        </span>
                    </div>
                    <div class="total-section">
                        <div class="total-label">Valor Declarado</div>
                        <div class="total-value">$<?php echo number_format($row['total'], 0, ',', '.'); ?><span class="total-currency">COP</span></div>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="index.html" class="btn-volver">← Volver a la Tienda</a>
</div>

<script>
    function toggleOrden(header) {
        header.closest('.orden-card').classList.toggle('colapsada');
    }

    (function () {
        var inp = document.getElementById('buscador-pedidos');
        var cont = document.getElementById('contador-resultados');
        if (!inp || !cont) return;

        function filtrar() {
            var q = (inp.value || '').trim().toLowerCase();
            var visibles = 0, total = 0;
            var tarjetas = document.querySelectorAll('.orden-card');
            for (var k = 0; k < tarjetas.length; k++) {
                total++;
                var okQ = !q || (tarjetas[k].dataset.busqueda || '').indexOf(q) !== -1;
                var mostrar = okQ;
                tarjetas[k].style.display = mostrar ? '' : 'none';
                if (mostrar) visibles++;
            }
            cont.textContent = 'Mostrando ' + visibles + ' de ' + total + ' guias';
        }

        inp.addEventListener('input', filtrar);
        filtrar();
    })();
</script>
</body>
</html>
