<?php
require_once 'auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    if (iniciarSesion($email, $contrasena)) {
        header("Location: ../frontend/dashboard.php");
        exit();
    } else {
        echo "Error: Correo electrónico o contraseña incorrectos.";
    }
}
?>

