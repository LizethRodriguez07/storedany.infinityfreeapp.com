<?php
// Configuración de la conexión real en InfinityFree
$host = 'sql201.infinityfree.com';
$db   = 'if0_41988386_gst_ventasonline';
$user = 'if0_41988386';
$pass = 'NJvVj32GYWri';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

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
            $mensaje_txt = isset($_POST['textarea']) ? trim($_POST['textarea']) : '';
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

            // Consulta SQL estructurada
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
            
            // Guardar el cliente registrado en el navegador para vincularlo a su compra
            $idNuevoCliente = $pdo->lastInsertId();
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