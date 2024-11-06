<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('FileOManager/config/db_config.php');

// Verificar si el usuario está autenticado
$isAuthenticated = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File OManager</title>
    <link rel="stylesheet" href="FileOManager/backend/fomstyle.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php if (!$isAuthenticated): ?>
        <!-- Contenedor principal de la página de login -->
        <div id="loginPageContainer">
            <h1>Bienvenido a File OManager - Inicie Sesión</h1>
            <div id="loginContent">
                <!-- Logo de la aplicación -->
                <div id="logoContainer">
                    <img src="/FileOManager/public/logo/Logo_Default.jpg" alt="Logo File OManager">
                </div>

                <!-- Formulario de login -->
                <div id="formContainer">
                    <form id="loginForm" action="FileOManager/backend/login.php" method="POST">
                        <div>
                            <label for="username">Usuario:</label><br>
                            <input type="text" id="username" name="username" required><br><br>
                        </div>
                        <div>
                            <label for="password">Contraseña:</label><br>
                            <input type="password" id="password" name="password" required><br><br>
                        </div>
                        <button type="submit">Ingresar</button>
                    </form>
                    <p class="register-link">¿No tienes una cuenta? <a href="FileOManager/frontend/register.html">Regístrate aquí</a></p>
                </div>
            </div>
        </div>

        <script>
            // Enviar formulario de inicio de sesión con AJAX
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'FileOManager/backend/login.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status === "success") {
                            // Redirigir al dashboard
                            window.location.href = 'FileOManager/dashboard.php';
                        } else {
                            // Mostrar mensaje de error
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert("Error al comunicarse con el servidor.");
                    }
                });
            });
        </script>

    <?php else: ?>
        <?php include 'FileOManager/dashboard.php'; ?>
    <?php endif; ?>
</body>
</html>

