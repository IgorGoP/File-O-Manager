<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File OManager - Inicio de Sesión</title>
</head>
<body>
    <div style="text-align: center;">
        <!-- Logo del proyecto -->
        <img src="/FileOManager/public/logo/Logo_Default.jpg" alt="Logo File OManager" style="width: 200px; height: auto;">

        <!-- Título de bienvenida -->
        <h1>Bienvenido a File OManager - Inicie Sesión</h1>

        <!-- Formulario de inicio de sesión -->
        <form action="FileOManager/backend/login.php" method="POST">
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

        <!-- Enlace para registrarse -->
        <p>¿No tienes una cuenta? <a href="frontend/register.html">Regístrate aquí</a></p>
    </div>
</body>
</html>

