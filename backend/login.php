<?php
// Iniciar sesión y conectar a la base de datos
session_start();
require_once('../config/db_config.php');

// Configurar la cabecera para devolver JSON
header('Content-Type: application/json');

// Verificar si se enviaron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Preparar y ejecutar la consulta para obtener los datos del usuario
    $query = "SELECT id, nombre, contrasena, rol FROM usuarios WHERE nombre = ?";
    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_id, $nombre_usuario, $hashed_password, $user_rol);
        
        if ($stmt->fetch()) {
            // Verificar la contraseña
            if (password_verify($password, $hashed_password)) {
                // Almacenar datos en la sesión
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $nombre_usuario;
                $_SESSION['rol'] = $user_rol;

                // Devolver una respuesta de éxito en JSON
                echo json_encode(["status" => "success"]);
            } else {
                // Contraseña incorrecta
                echo json_encode(["status" => "error", "message" => "Contraseña incorrecta."]);
            }
        } else {
            // Usuario no encontrado
            echo json_encode(["status" => "error", "message" => "Usuario no encontrado."]);
        }
        $stmt->close();
    } else {
        // Error al preparar la consulta
        echo json_encode(["status" => "error", "message" => "Error en el servidor."]);
    }
} else {
    // Si no se accedió por POST, devolver un error
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
?>

