<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File OManager - Inicio de Sesión</title>
</head>
<body>
    <h1>Bienvenido a File OManager - Inicie Sesión</h1>

    <!-- Formulario de inicio de sesión -->
	<form action="FileOManager/backend/login.php" method="POST">
        <label for="username">Usuario:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Ingresar</button>
    </form>

    <!-- Enlace para registrarse -->
    <p>¿No tienes una cuenta? <a href="frontend/register.html">Regístrate aquí</a></p>
</body>
</html>

