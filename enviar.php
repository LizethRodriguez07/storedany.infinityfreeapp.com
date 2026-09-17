<?php
// Configuración de la conexión real en InfinityFree
$host = 'sql201.infinityfree.com';
$db   = 'if0_41988386_gst_ventasonline';
$user = 'if0_41988386';
$pass = '********';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Normaliza un nombre para compararlo de forma tolerante:
// mayúsculas, sin tildes y con espacios simples ("Juan  Pérez" == "JUAN PEREZ")
function normalizarNombreComparacion($texto) {
    $texto = trim((string)$texto);
    $texto = function_exists('mb_strtoupper') ? mb_strtoupper($texto, 'UTF-8') : strtoupper($texto);
    $texto = strtr($texto, array(
        'Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A', 'Ã' => 'A',
        'É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E',
        'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I', 'Î' => 'I',
        'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O', 'Ô' => 'O', 'Õ' => 'O',
        'Ú' => 'U', 'Ù' => 'U', 'Ü' => 'U', 'Û' => 'U',
        'Ñ' => 'N', 'Ç' => 'C'
    ));
    return preg_replace('/\s+/', ' ', $texto);
}

// Muestra un aviso con el mismo diseño de las notificaciones del sitio
// (el toast de las tallas) y luego regresa a la página indicada.
// No depende de main.js para que funcione igual en InfinityFree.
function mostrarAvisoYVolver($titulo, $mensaje, $tipo, $icono, $destino = 'personal-data.html', $milisegundos = 3000) {
    $titulo  = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
    $mensaje = htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');
    $tipo    = preg_replace('/[^a-z]/', '', $tipo);
    $icono   = htmlspecialchars($icono, ENT_QUOTES, 'UTF-8');
    $destino = htmlspecialchars($destino, ENT_QUOTES, 'UTF-8');
    $ms      = intval($milisegundos);

    echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Aviso - STORE DANY</title>
<link rel="stylesheet" href="Style.css?v=15">
</head>
<body>
<div class="store-dany-toasts" id="store-dany-toasts">
    <div class="store-dany-notificacion $tipo">
        <div class="store-dany-notif-icono">$icono</div>
        <div class="store-dany-notif-contenido">
            <span class="store-dany-notif-titulo">$titulo</span>
            <span class="store-dany-notif-mensaje">$mensaje</span>
        </div>
        <button type="button" class="store-dany-notif-cerrar" aria-label="Cerrar">✕</button>
    </div>
</div>
<script>
    (function () {
        var aviso = document.querySelector(".store-dany-notificacion");
        if (aviso) {
            requestAnimationFrame(function () {
                requestAnimationFrame(function () { aviso.classList.add("visible"); });
            });
        }
        var destino = "$destino";
        function volver() { window.location.href = destino; }
        var cerrar = document.querySelector(".store-dany-notif-cerrar");
        if (cerrar) { cerrar.addEventListener("click", volver); }
        setTimeout(volver, $ms);
    })();
</script>
</body>
</html>
HTML;
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Identificar de qué formulario provienen los datos enviados
        $origen = isset($_POST['origen_formulario']) ? trim($_POST['origen_formulario']) : 'clientes';

        if ($origen === 'contacto') {
            // =========================================================================
            // LÓGICA PARA EL FORMULARIO DE CONSULTAS -> ADAPTADA A TU TABLA CHATONLINE
            // =========================================================================
            $nombre    = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellidos = isset($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
            $email     = isset($_POST['email']) ? trim($_POST['email']) : '';
            $telefono  = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
            $mensaje_txt = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : (isset($_POST['textarea']) ? trim($_POST['textarea']) : '');
            $tipo_consulta = isset($_POST['tipo_consulta']) ? trim($_POST['tipo_consulta']) : 'OTRO';
            // Unimos Nombre y Apellido para guardarlo en tu columna 'usuario'
            $usuario_completo = trim($nombre . ' ' . $apellidos);
            // Agregamos teléfono y correo al mensaje si existen
            $info_extra = [];
            if ($email) { $info_extra[] = "Email: " . $email; }
            if ($telefono) { $info_extra[] = "Tel: " . $telefono; }
            $mensaje_completo = "[$tipo_consulta] " . $mensaje_txt;
            if (!empty($info_extra)) { $mensaje_completo .= "\n\nContacto → " . implode(" | ", $info_extra); }

            // Consulta SQL adaptada exactamente a tu tabla 'chatonline'
            $sql_consulta = "INSERT INTO chatonline (usuario, mensaje) 
                             VALUES (:usuario, :mensaje)";
            
            $stmt_consulta = $pdo->prepare($sql_consulta);
            $stmt_consulta->execute([
                ':usuario' => $usuario_completo,
                ':mensaje' => $mensaje_completo
            ]);
            echo "<script>
                    alert('¡Tu consulta ha sido registrada con éxito en el Chat Online!');
                    window.location.href = 'contactar.html';
                  </script>";

        } else {

            
            // =========================================================================
            // LÓGICA ORIGINAL PARA EL FORMULARIO DE CLIENTES -> REGISTRO EN LA BD
            // =========================================================================
            $nombre       = isset($_POST['nombre']) ? trim($_POST['nombre']) : ''; 
            $apellidos    = isset($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
            $cedula       = isset($_POST['cedula']) ? trim($_POST['cedula']) : '';
            $celular      = isset($_POST['celular']) ? trim($_POST['celular']) : '';
            $email        = isset($_POST['email']) ? trim($_POST['email']) : ''; 
            $departamento = isset($_POST['departamento']) ? trim($_POST['departamento']) : '';
            $barrio       = isset($_POST['barrio']) ? trim($_POST['barrio']) : ''; 

            // ✨ TU PARCHE ANTIBLOQUEO ORIGINAL
            if (isset($_POST['municipio']) && trim($_POST['municipio']) !== '') {
                $municipio = trim($_POST['municipio']);
            } elseif (isset($_POST['municipi']) && trim($_POST['municipi']) !== '') {
                $municipio = trim($_POST['municipi']);
            } else {
                $municipio = 'No especificado';
            }

            // 🔎 VALIDACIÓN DE CÉDULA: repetirla solo se permite si el nombre completo es del titular
            $cedulaNormalizada     = preg_replace('/[^0-9]/', '', $cedula);
            $idClienteMismaCedula  = null;

            if ($cedulaNormalizada !== '') {
                $stmtCedula = $pdo->prepare("SELECT id, nombre, apellidos FROM clientes
                                             WHERE REPLACE(REPLACE(REPLACE(cedula, '.', ''), ' ', ''), '-', '') = :cedula");
                $stmtCedula->execute([':cedula' => $cedulaNormalizada]);
                $clientesConCedula = $stmtCedula->fetchAll();

                if (count($clientesConCedula) > 0) {
                    $nombreIngresado = normalizarNombreComparacion($nombre . ' ' . $apellidos);
                    foreach ($clientesConCedula as $clienteCedula) {
                        if (normalizarNombreComparacion($clienteCedula['nombre'] . ' ' . $clienteCedula['apellidos']) === $nombreIngresado) {
                            $idClienteMismaCedula = intval($clienteCedula['id']);
                            break;
                        }
                    }

                    // 🚫 Cédula repetida con un nombre que NO corresponde al titular → no es correcta
                    if ($idClienteMismaCedula === null) {
                        mostrarAvisoYVolver(
                            'Cédula no válida',
                            'El número de cédula no es correcto. Si ya compraste antes y tus datos cambiaron, comunícate con la asesora para darte solución.',
                            'error',
                            '⛔',
                            'personal-data.html',
                            3200
                        );
                    }
                }
            }

            if ($idClienteMismaCedula !== null) {
                // 🔁 Mismo cliente otra vez: actualizamos sus datos (sin duplicar la cédula)
                $sql = "UPDATE clientes
                        SET nombre = :nombre, apellidos = :apellidos, celular = :celular,
                            correo = :email, departamento = :departamento, municipio = :municipio, direccion = :barrio
                        WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nombre'       => $nombre,
                    ':apellidos'    => $apellidos,
                    ':celular'      => $celular,
                    ':email'        => $email,
                    ':departamento' => $departamento,
                    ':municipio'    => $municipio,
                    ':barrio'       => $barrio,
                    ':id'           => $idClienteMismaCedula
                ]);
                $idNuevoCliente = $idClienteMismaCedula;
            } else {
                // 🆕 Cliente nuevo
                $sql = "INSERT INTO clientes (nombre, apellidos, cedula, celular, correo, departamento, municipio, direccion) 
                        VALUES (:nombre, :apellidos, :cedula, :celular, :email, :departamento, :municipio, :barrio)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nombre'       => $nombre,
                    ':apellidos'    => $apellidos,
                    ':cedula'       => $cedula,
                    ':celular'      => $celular,
                    ':email'        => $email,
                    ':departamento' => $departamento,
                    ':municipio'    => $municipio,
                    ':barrio'       => $barrio
                ]);
                $idNuevoCliente = $pdo->lastInsertId();
            }
            
            // Guardar el cliente registrado en el navegador para vincularlo a su compra
            $datosClienteJS = json_encode(array(
                'id'       => intval($idNuevoCliente),
                'nombre'   => trim($nombre . ' ' . $apellidos),
                'telefono' => $celular
            ), JSON_UNESCAPED_UNICODE);
            echo "<script>
                    try { localStorage.setItem('cliente_dany', " . json_encode($datosClienteJS) . "); } catch(e) {}
                    alert('Datos registrados con exito');
                    window.location.href = 'shopping-cart.html';
                  </script>";
        }
    }

} catch (\PDOException $e) {
    die("Error de conexión a gst_ventasonline: " . $e->getMessage());
}
?>
