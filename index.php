<?php
// Inicializar variables para mensajes
$mensaje = "";
$conexion_exitosa = false;

// Obtener la IP o dominio del host desde la variable de servidor
$ip_host = $_SERVER['HTTP_HOST'];

// Verificar si el formulario fue enviado para validación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["validar"])) {
    $host = $_POST["db_host"];
    $usuario = $_POST["db_user"];
    $contrasena = $_POST["db_password"];
    $nombre_db = $_POST["db_name"];

    // Intentar conectar a la base de datos
    $conexion = new mysqli($host, $usuario, $contrasena, $nombre_db);

    if ($conexion->connect_error) {
        $mensaje = "<p class='error'>Sin conexión: " . $conexion->connect_error . "</p>";
    } else {
        // Crear el contenido para el archivo de configuración
        $config_content = "<?php\n";
        $config_content .= "define('DB_SERVER', '$host');\n";
        $config_content .= "define('DB_USERNAME', '$usuario');\n";
        $config_content .= "define('DB_PASSWORD', '$contrasena');\n";
        $config_content .= "define('DB_NAME', '$nombre_db');\n\n";
        $config_content .= "// Crear conexión a la base de datos\n";
        $config_content .= "\$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);\n\n";
        $config_content .= "// Verificar conexión\n";
        $config_content .= "if (\$db->connect_error) {\n";
        $config_content .= "    die('Error de conexión: ' . \$db->connect_error);\n";
        $config_content .= "}\n";
        $config_content .= "?>";

        // Escribir en el archivo config/db_config.php
        $config_path = __DIR__ . '/config/db_config.php';
        if (file_put_contents($config_path, $config_content)) {
            $mensaje = "<p class='success'>Conexión hecha correctamente. Configuración guardada.</p>";
            $conexion_exitosa = true;
        } else {
            $mensaje = "<p class='error'>Conexión exitosa, pero no se pudo guardar el archivo de configuración.</p>";
        }
    }
    $conexion->close();
}

// Función para ejecutar el archivo SQL y ocultar mensajes de éxito y duplicados
function ejecutar_archivo_sql($conexion, $archivo_sql) {
    $consulta = file_get_contents($archivo_sql);
    if ($consulta === false) {
        return "<p class='error'>Error al leer el archivo SQL.</p>";
    }
    // Ejecutar cada consulta del archivo
    $sentencias = explode(";", $consulta);
    $mensaje = "";
    foreach ($sentencias as $sql) {
        $sql = trim($sql);
        if ($sql) { // Ejecuta si no es una línea vacía
            if ($conexion->query($sql) !== TRUE) {
                // Ignorar errores de duplicado
                if (strpos($conexion->error, 'Duplicate entry') === false) {
                    $mensaje .= "<p class='error'>Error al ejecutar la consulta: " . $conexion->error . "</p>";
                }
            }
        }
    }
    return $mensaje ?: "<p class='success'>Estructura de la base de datos creada correctamente.</p>";
}

// Si se hace clic en "Actualizar", ejecutar el archivo SQL para crear las tablas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["actualizar"])) {
    require_once __DIR__ . '/config/db_config.php'; // Conexión a la BD

    // Archivo que contiene las sentencias SQL para crear las tablas
    $archivo_sql = __DIR__ . '/config/struct.sql';

    // Ejecutar las consultas SQL del archivo
    $mensaje = ejecutar_archivo_sql($db, $archivo_sql);

    // Mover el archivo "fom.php" a una carpeta anterior
    $ruta_actual = __DIR__ . '/fom.php';
    $nueva_ruta = dirname(__DIR__) . '/fom.php';
    if (file_exists($ruta_actual) && rename($ruta_actual, $nueva_ruta)) {
        // Mensaje final después de mover el archivo
        $mensaje .= "<p class='success'>Preparación Exitosa</p>
                     <p>Favor de iniciar sesión en:</p>
                     <p><a href='http://$ip_host/fom.php' target='_blank'>http://$ip_host/fom.php</a></p>
                     <p>Ingresar con el usuario:<br><strong>admin</strong></p>
                     <p>y la contraseña:<br><strong>superadmin123</strong></p>";
        
        // Mostrar el mensaje dentro de la estructura HTML y con estilo
        echo "<!DOCTYPE html>
              <html lang='es'>
              <head>
                  <meta charset='UTF-8'>
                  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                  <title>Preparación Exitosa</title>
                  <style>
                      body {
                          font-family: Arial, sans-serif;
                          background-color: #f9fafb;
                          color: #333;
                          display: flex;
                          justify-content: center;
                          align-items: center;
                          height: 100vh;
                          margin: 0;
                      }
                      .container {
                          max-width: 400px;
                          background-color: #fff;
                          padding: 20px;
                          border-radius: 8px;
                          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                          text-align: center;
                      }
                      .success { color: #28a745; font-weight: bold; margin-top: 10px; }
                      .error { color: #dc3545; font-weight: bold; margin-top: 10px; }
                  </style>
              </head>
              <body>
                  <div class='container'>$mensaje</div>
              </body>
              </html>";
        exit;
    } else {
        $mensaje .= "<p class='error'>No se pudo mover el archivo 'fom.php'. Verifica permisos y ubicación.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Base de Datos</title>
    <style>
        /* Estilos para darle un aspecto profesional */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 400px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            font-size: 0.9rem;
            color: #555;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-submit {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            width: 100%;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .success, .info, .error {
            font-weight: bold;
            margin-top: 10px;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$conexion_exitosa): ?>
            <h1>Validación de Conexión a Base de Datos</h1>
            <form method="POST">
                <div class="form-group">
                    <label for="db_host">Host de la base de datos:</label>
                    <input type="text" name="db_host" id="db_host" required>
                </div>
                <div class="form-group">
                    <label for="db_user">Usuario de la base de datos:</label>
                    <input type="text" name="db_user" id="db_user" required>
                </div>
                <div class="form-group">
                    <label for="db_password">Contraseña de la base de datos:</label>
                    <input type="password" name="db_password" id="db_password">
                </div>
                <div class="form-group">
                    <label for="db_name">Nombre de la base de datos:</label>
                    <input type="text" name="db_name" id="db_name" required>
                </div>
                <button type="submit" name="validar" class="btn-submit">Validar Base de Datos</button>
            </form>
        <?php else: ?>
            <p class="success">Se procederá a actualizar la Base de Datos.</p>
            <form method="POST">
                <button type="submit" name="actualizar" class="btn-submit">Actualizar Base de Datos</button>
            </form>
        <?php endif; ?>

        <?php
        if (isset($mensaje) && !$conexion_exitosa) {
            echo $mensaje;
        }
        ?>
    </div>
</body>
</html>

