<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../fom.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File OManager - Dashboard</title>
</head>
<body>
    <h1>Bienvenido al Dashboard de File OManager, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
    <p><a href="../backend/logout.php">Cerrar sesión</a></p>

    <!-- Sección para subir un archivo -->
    <h2>Subir un Archivo</h2>
    <form action="../backend/upload.php" method="POST" enctype="multipart/form-data">
        <label for="archivo">Seleccione un archivo:</label>
        <input type="file" id="archivo" name="archivo" required><br><br>
        <button type="submit">Subir Archivo</button>
    </form>

    <!-- Sección para listar archivos -->
    <h2>Archivos Subidos</h2>
    <?php
    // Mostrar la lista de archivos
    $directorio = '../uploads/';

    // Verificar si el directorio existe
    if (is_dir($directorio)) {
        // Abrir el directorio
        if ($handle = opendir($directorio)) {
            echo "<ul>";

            // Leer archivos del directorio
            while (false !== ($archivo = readdir($handle))) {
                if ($archivo != "." && $archivo != "..") {
                    $rutaArchivo = htmlspecialchars($directorio . $archivo, ENT_QUOTES, 'UTF-8');
                    $archivoEscaped = htmlspecialchars($archivo, ENT_QUOTES, 'UTF-8');
                    echo "<li><a href='$rutaArchivo' download>$archivoEscaped</a> | <a href='../backend/delete_file.php?archivo=$archivoEscaped'>Eliminar</a></li>";
                }
            }
            
            echo "</ul>";
            closedir($handle);
        } else {
            echo "Error: No se pudo abrir el directorio de archivos.";
        }
    } else {
        echo "Error: El directorio de archivos no existe.";
    }
    ?>
</body>
</html>

