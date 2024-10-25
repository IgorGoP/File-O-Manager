<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Depuración para ver qué se está enviando
    echo "Nombre de usuario recibido: " . htmlspecialchars($username) . "<br>";

    // Conexión a la base de datos
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Verificar el usuario
    $sql = "SELECT * FROM usuarios WHERE nombre = ?";
    $stmt = $conn->prepare($sql);

    // Verificar si la preparación de la consulta fue exitosa
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Mostrar el hash de la contraseña que se recupera de la base de datos
        echo "Hash de la contraseña en la base de datos: " . $row['contrasena'] . "<br>";

        // Verificar la contraseña usando password_verify()
        if (password_verify($password, $row['contrasena'])) {
            // Inicio de sesión exitoso
            $_SESSION['username'] = $username;
            header("Location: ../frontend/dashboard.php");
            exit();
        } else {
            echo "Usuario o contraseña incorrectos (contraseña incorrecta).";
        }
    } else {
        // Usuario no encontrado
        echo "Usuario o contraseña incorrectos (usuario no encontrado).";
    }

    $stmt->close();
    $conn->close();
}
?>

