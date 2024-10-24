<?php

// Incluir el archivo de configuración
require_once __DIR__ . '/../config/db_config.php';

// Crear conexión
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Solicitar nueva contraseña al usuario
echo "Iniciando la actualización de contraseñas...<br>";

if (isset($_POST['nueva_contrasena'])) {
    $nueva_contrasena = $_POST['nueva_contrasena'];

    // Obtener todos los usuarios actuales
    $sql = "SELECT id FROM usuarios";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($usuario = $result->fetch_assoc()) {
            $usuario_id = $usuario['id'];

            // Actualizar la contraseña en la base de datos
            $contrasenaHash = password_hash($nueva_contrasena, PASSWORD_BCRYPT);
            $update_sql = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("si", $contrasenaHash, $usuario_id);

            if ($stmt->execute()) {
                echo "Contraseña del usuario con ID $usuario_id actualizada correctamente.<br>";
            } else {
                echo "Error al actualizar la contraseña del usuario con ID $usuario_id: " . $stmt->error . "<br>";
            }
        }
    } else {
        echo "No se encontraron usuarios en la base de datos.<br>";
    }

    // Cerrar la conexión
    $conn->close();
    echo "Proceso de actualización de contraseñas completado.";
} else {
    // Mostrar formulario para ingresar la nueva contraseña
    echo '<form method="POST" action="">
            <label for="nueva_contrasena">Nueva Contraseña:</label>
            <input type="password" id="nueva_contrasena" name="nueva_contrasena" required><br><br>
            <button type="submit">Actualizar Contraseñas</button>
          </form>';
}

?>

