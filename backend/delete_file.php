<?php
if (isset($_GET['archivo'])) {
    $archivo = $_GET['archivo'];
    $directorio = '../uploads/';
    $rutaArchivo = $directorio . $archivo;

    // Verificar si el archivo existe
    if (file_exists($rutaArchivo)) {
        // Intentar eliminar el archivo
        if (unlink($rutaArchivo)) {
            echo "El archivo '$archivo' ha sido eliminado correctamente.";
        } else {
            echo "Error: No se pudo eliminar el archivo '$archivo'.";
        }
    } else {
        echo "Error: El archivo '$archivo' no existe.";
    }
} else {
    echo "Error: No se especificó ningún archivo para eliminar.";
}

// Agregar un enlace para regresar a la lista de archivos
echo "<br><a href='list_files.php'>Volver a la lista de archivos</a>";
?>

