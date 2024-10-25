<?php
session_start();

header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['archivo'])) {
    $archivo = $_POST['archivo'];
    $directorio = '../uploads/';
    $rutaArchivo = $directorio . $archivo;

    // Verificar si el archivo existe
    if (file_exists($rutaArchivo)) {
        // Intentar eliminar el archivo
        if (unlink($rutaArchivo)) {
            $response['status'] = 'success';
            $response['message'] = "El archivo '$archivo' ha sido eliminado correctamente.";
        } else {
            $response['status'] = 'error';
            $response['message'] = "Error: No se pudo eliminar el archivo '$archivo'.";
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = "Error: El archivo '$archivo' no existe.";
    }
} else {
    $response['status'] = 'error';
    $response['message'] = "Error: No se especificó ningún archivo para eliminar.";
}

echo json_encode($response);
exit();
?>

