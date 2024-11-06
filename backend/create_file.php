<?php
// create_file.php
header('Content-Type: application/json');
session_start();

// Evitar la salida de errores en la respuesta
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/ruta/a/tu/log/php_errors.log'); // Asegúrate de que este archivo existe y es escribible

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Prohibido
    echo json_encode(["status" => "error", "message" => "No estás autorizado."]);
    exit();
}

// Verificar si se pasó el nombre del archivo
if (!isset($_POST['name']) || empty(trim($_POST['name']))) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(["status" => "error", "message" => "Nombre de archivo no especificado."]);
    exit();
}

// Limpiar el nombre del archivo
$nombre_archivo = basename(trim($_POST['name']));

// Validar el nombre del archivo (permitir letras, números, guiones, guiones bajos, puntos y espacios)
if (!preg_match('/^[a-zA-Z0-9-_\. ]+$/', $nombre_archivo)) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(["status" => "error", "message" => "Nombre de archivo no válido."]);
    exit();
}

$uploads_dir = '../uploads/';
$archivo_path = $uploads_dir . $nombre_archivo;

// Verificar si el archivo ya existe
if (file_exists($archivo_path)) {
    http_response_code(409); // Conflicto
    echo json_encode(["status" => "error", "message" => "El archivo ya existe."]);
    exit();
}

// Intentar crear el archivo vacío
if (touch($archivo_path)) {
    http_response_code(201); // Creado
    echo json_encode([
        "status" => "success",
        "message" => "Archivo creado correctamente.",
        "tipo" => "archivo",
        "nombre" => $nombre_archivo
    ]);
} else {
    http_response_code(500); // Error interno del servidor
    echo json_encode(["status" => "error", "message" => "No se pudo crear el archivo."]);
}
?>

