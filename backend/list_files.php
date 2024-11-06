<?php
$directorio = '../uploads/';

// Verificar si el directorio existe
if (is_dir($directorio)) {
    // Abrir el directorio
    if ($handle = opendir($directorio)) {
        echo "<h2>Archivos Subidos:</h2>";
        echo "<ul>";

        // Leer archivos del directorio
        while (false !== ($archivo = readdir($handle))) {
            if ($archivo != "." && $archivo != "..") {
                $rutaArchivo = $directorio . $archivo;
                echo "<li><a href='$rutaArchivo' download>$archivo</a> | <a href='delete_file.php?archivo=$archivo'>Eliminar</a></li>";
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

