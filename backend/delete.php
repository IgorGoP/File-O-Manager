<?php
// backend/delete.php

session_start();
header('Content-Type: application/json');

// Función para enviar respuestas JSON
function send_response($success, $message = '', $data = []) {
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $data));
    exit();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Prohibido
    send_response(false, 'No estás autorizado.');
}

// Obtener y decodificar la entrada JSON
$input = json_decode(file_get_contents('php://input'), true);

// Verificar que la decodificación fue exitosa
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Solicitud incorrecta
    send_response(false, 'Entrada JSON inválida.');
}

// Extraer 'file' y 'csrf_token' de la entrada
$file = $input['file'] ?? '';
$csrf_token = $input['csrf_token'] ?? '';

// Verificar que ambos campos estén presentes
if (empty($file) || empty($csrf_token)) {
    http_response_code(400); // Solicitud incorrecta
    send_response(false, 'Parámetros faltantes: archivo y/o token CSRF.');
}

// Validar el token CSRF
if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
    http_response_code(403); // Prohibido
    send_response(false, 'Token CSRF inválido.');
}

// Sanitizar el nombre del archivo/carpeta para prevenir Path Traversal
$file = basename($file);

// Validar el nombre del archivo/carpeta usando una expresión regular más estricta
// Permitimos letras, números, espacios, guiones bajos, guiones y puntos
if (!preg_match('/^[a-zA-Z0-9 _\-.]+$/', $file)) {
    http_response_code(400); // Solicitud incorrecta
    send_response(false, 'Nombre de archivo o carpeta no válido.');
}

// Definir el directorio de uploads
$uploads_dir = realpath(__DIR__ . '/../uploads/');

// Verificar que el directorio de uploads exista
if ($uploads_dir === false) {
    http_response_code(500); // Error interno del servidor
    send_response(false, 'Directorio de uploads no encontrado.');
}

// Construir la ruta completa del archivo/carpeta
$file_path = realpath($uploads_dir . DIRECTORY_SEPARATOR . $file);

// Verificar que la ruta construida está dentro del directorio de uploads
if ($file_path === false || strpos($file_path, $uploads_dir) !== 0) {
    http_response_code(400); // Solicitud incorrecta
    send_response(false, 'Ruta de archivo o carpeta inválida.');
}

// Verificar si el archivo o carpeta existe
if (!file_exists($file_path)) {
    http_response_code(404); // No encontrado
    send_response(false, 'Elemento no encontrado.');
}

// Función para eliminar directorios de forma recursiva
function delete_directory($dir) {
    if (!is_dir($dir)) return false;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            if (!delete_directory($path)) return false;
        } else {
            if (!unlink($path)) return false;
        }
    }
    return rmdir($dir);
}

// Determinar si es un archivo o directorio y proceder con la eliminación
if (is_file($file_path)) {
    if (unlink($file_path)) {
        send_response(true, 'Archivo eliminado correctamente.', ['nombre' => $file]);
    } else {
        http_response_code(500); // Error interno del servidor
        send_response(false, 'No se pudo eliminar el archivo.');
    }
} elseif (is_dir($file_path)) {
    if (is_dir_empty($file_path)) {
        // Carpeta vacía; eliminar directamente
        if (rmdir($file_path)) {
            send_response(true, 'Carpeta eliminada correctamente.', ['nombre' => $file]);
        } else {
            http_response_code(500); // Error interno del servidor
            send_response(false, 'No se pudo eliminar la carpeta.');
        }
    } else {
        // Carpeta no está vacía; eliminar recursivamente
        if (delete_directory($file_path)) {
            send_response(true, 'Carpeta eliminada correctamente.', ['nombre' => $file]);
        } else {
            http_response_code(500); // Error interno del servidor
            send_response(false, 'No se pudo eliminar la carpeta.');
        }
    }
} else {
    http_response_code(400); // Solicitud incorrecta
    send_response(false, 'El elemento no es ni un archivo ni una carpeta válida.');
}

// Función para verificar si una carpeta está vacía
function is_dir_empty($dir) {
    if (!is_readable($dir)) return false;
    return (count(scandir($dir)) == 2);
}
?>

