<?php
// Verificar si se ha enviado un archivo
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
    $directorioSubida = '../uploads/';
    
    // Asegurarse de que el directorio de subida exista
    if (!is_dir($directorioSubida)) {
        mkdir($directorioSubida, 0755, true);
    }
    
    // Nombre del archivo subido
    $nombreArchivo = basename($_FILES['archivo']['name']);
    $rutaDestino = $directorioSubida . $nombreArchivo;

    // Mover el archivo al directorio de destino
    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaDestino)) {
        echo "El archivo se ha subido exitosamente.";
    } else {
        echo "Hubo un error al subir el archivo.";
    }
} else {
    echo "No se ha seleccionado ningún archivo o hubo un error al subirlo.";
}
?>

