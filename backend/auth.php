<?php

// Incluir el archivo de configuración
require_once __DIR__ . '/../config/db_config.php';

// Crear conexión
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para iniciar sesión
function iniciarSesion($email, $contrasena) {
    global $conn;

    // Consultar el usuario en la base de datos
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si el usuario existe
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($contrasena, $usuario['contrasena'])) {
            // Iniciar sesión exitosa
            session_start();
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            return true;
        } else {
            // Contraseña incorrecta
            return false;
        }
    } else {
        // Usuario no encontrado
        return false;
    }
}

// Función para registrarse
function registrarUsuario($nombre, $email, $contrasena) {
    global $conn;

    // Verificar si el correo ya está registrado
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El correo ya está en uso
        return false;
    } else {
        // Registrar nuevo usuario
        $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);
        $sql = "INSERT INTO usuarios (nombre, email, contrasena) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nombre, $email, $contrasenaHash);
        return $stmt->execute();
    }
}

// Cerrar la conexión
function cerrarConexion() {
    global $conn;
    $conn->close();
}

?>

