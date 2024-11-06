<?php
// create_folder.php
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

// Verificar si se pasó el nombre de la carpeta
if (!isset($_POST['name']) || empty(trim($_POST['name']))) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(["status" => "error", "message" => "Nombre de carpeta no especificado."]);
    exit();
}

// Limpiar el nombre de la carpeta
$nombre_carpeta = basename(trim($_POST['name']));

// Validar el nombre de la carpeta (permitir letras, números, guiones, guiones bajos, puntos y espacios)
if (!preg_match('/^[a-zA-Z0-9-_\. ]+$/', $nombre_carpeta)) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(["status" => "error", "message" => "Nombre de carpeta no válido."]);
    exit();
}

$uploads_dir = '../uploads/';
$carpeta_path = $uploads_dir . $nombre_carpeta;

// Verificar si la carpeta ya existe
if (file_exists($carpeta_path)) {
    http_response_code(409); // Conflicto
    echo json_encode(["status" => "error", "message" => "La carpeta ya existe."]);
    exit();
}

// Intentar crear la carpeta
if (mkdir($carpeta_path, 0755, true)) {
    http_response_code(201); // Creado
    echo json_encode([
        "status" => "success",
        "message" => "Carpeta creada correctamente.",
        "tipo" => "carpeta",
        "nombre" => $nombre_carpeta
    ]);
} else {
    http_response_code(500); // Error interno del servidor
    echo json_encode(["status" => "error", "message" => "No se pudo crear la carpeta."]);
}
?>

