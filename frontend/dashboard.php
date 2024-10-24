<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - File OManager</title>
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>
    <p>Has iniciado sesión correctamente.</p>
    <a href="../backend/logout.php">Cerrar Sesión</a>
</body>
</html>

