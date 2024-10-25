<?php

// Mostrar errores para depuración (puedes desactivarlo en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir el archivo de configuración
require_once __DIR__ . '/../config/db_config.php';

// Crear conexión
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nueva_contrasena'])) {
        $nueva_contrasena = $_POST['nueva_contrasena'];

        // Hashear la nueva contraseña
        $contrasenaHash = password_hash($nueva_contrasena, PASSWORD_BCRYPT);

        // Actualizar la contraseña para el usuario con ID 1
        $update_sql = "UPDATE usuarios SET contrasena = ? WHERE id = 1";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("s", $contrasenaHash);

        if ($stmt->execute()) {
            echo "Contraseña del usuario con ID 1 restablecida correctamente.";
        } else {
            echo "Error al restablecer la contraseña del usuario con ID 1: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Mostrar formulario para ingresar la nueva contraseña
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña del Usuario con ID 1</title>
</head>
<body>
    <h1>Restablecer Contraseña del Usuario con ID 1</h1>
    <form method="POST" action="">
        <label for="nueva_contrasena">Nueva Contraseña:</label>
        <input type="password" id="nueva_contrasena" name="nueva_contrasena" required><br><br>
        <button type="submit">Restablecer Contraseña</button>
    </form>
</body>
</html>
<?php
// Cerrar la conexión
$conn->close();
?>

