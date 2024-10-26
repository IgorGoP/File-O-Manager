<?php
// Iniciar sesión y conectar a la base de datos
session_start();
require_once('../config/db_config.php');

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

                // Redirigir al dashboard
                header("Location: ../frontend/dashboard.php");
                exit();
            } else {
                // Contraseña incorrecta
                $_SESSION['mensaje'] = 'Contraseña incorrecta.';
                header("Location: ../../fom.php");
                exit();
            }
        } else {
            // Usuario no encontrado
            $_SESSION['mensaje'] = 'Usuario no encontrado.';
            header("Location: ../../fom.php");
            exit();
        }
        $stmt->close();
    } else {
        die('Error al preparar la consulta.');
    }
} else {
    // Si no se accedió por POST, redirigir al formulario de inicio de sesión
    header("Location: ../../fom.php");
    exit();
}
?>

