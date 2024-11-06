<?php
// delete_file.php
header('Content-Type: application/json');
session_start();

// Registro para depuración
error_log("delete_file.php llamado por usuario ID: " . ($_SESSION['user_id'] ?? 'no autenticado'));

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    error_log("Usuario no autenticado");
    http_response_code(403); // Prohibido
    echo json_encode(["status" => "error", "message" => "No estás autorizado."]);
    exit();
}

// Ruta del directorio de uploads
$uploads_dir = '../uploads/';

// Verificar si se pasó el nombre del archivo o carpeta
if (!isset($_POST['file']) || empty($_POST['file'])) {
    error_log("Archivo no especificado");
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(["status" => "error", "message" => "Archivo no especificado."]);
    exit();
}

// Log del nombre recibido
error_log("Archivo recibido para eliminación: " . $_POST['file']);

// Limpiar el nombre para evitar problemas de seguridad
$file = basename($_POST['file']);

// Log después de sanitización
error_log("Archivo después de sanitización: " . $file);

// Validar el nombre del archivo o carpeta
if (!preg_match('/^[a-zA-Z0-9-_\. ]+$/', $file)) {
    error_log("Nombre de archivo inválido después de sanitización: " . $file);
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(["status" => "error", "message" => "Nombre de archivo no válido."]);
    exit();
}

$file_path = $uploads_dir . $file;

// Verificar que el archivo o carpeta existe
if (file_exists($file_path)) {
    if (is_file($file_path)) {
        // Es un archivo; intentar eliminarlo
        if (unlink($file_path)) {
            error_log("Archivo eliminado: " . $file_path);
            http_response_code(200); // Éxito
            echo json_encode([
                "status" => "success",
                "message" => "Archivo eliminado correctamente.",
                "nombre" => $file
            ]);
        } else {
            error_log("Error al eliminar el archivo: " . $file_path);
            http_response_code(500); // Error interno del servidor
            echo json_encode(["status" => "error", "message" => "No se pudo eliminar el archivo."]);
        }
    } elseif (is_dir($file_path)) {
        // Es una carpeta; intentar eliminarla
        if (is_dir_empty($file_path)) {
            // Carpeta vacía; eliminar directamente
            if (rmdir($file_path)) {
                error_log("Carpeta eliminada: " . $file_path);
                http_response_code(200); // Éxito
                echo json_encode([
                    "status" => "success",
                    "message" => "Carpeta eliminada correctamente.",
                    "nombre" => $file
                ]);
            } else {
                error_log("Error al eliminar la carpeta: " . $file_path);
                http_response_code(500); // Error interno del servidor
                echo json_encode(["status" => "error", "message" => "No se pudo eliminar la carpeta."]);
            }
        } else {
            // Carpeta no está vacía; eliminar recursivamente
            if (delete_directory($file_path)) {
                error_log("Carpeta eliminada recursivamente: " . $file_path);
                http_response_code(200); // Éxito
                echo json_encode([
                    "status" => "success",
                    "message" => "Carpeta eliminada correctamente.",
                    "nombre" => $file
                ]);
            } else {
                error_log("Error al eliminar la carpeta recursivamente: " . $file_path);
                http_response_code(500); // Error interno del servidor
                echo json_encode(["status" => "error", "message" => "No se pudo eliminar la carpeta."]);
            }
        }
    } else {
        error_log("El elemento no es ni un archivo ni una carpeta: " . $file_path);
        http_response_code(400); // Solicitud incorrecta
        echo json_encode(["status" => "error", "message" => "El elemento no es ni un archivo ni una carpeta válida."]);
    }
} else {
    error_log("Elemento no encontrado: " . $file_path);
    http_response_code(404); // No encontrado
    echo json_encode(["status" => "error", "message" => "Elemento no encontrado."]);
}

// Función para verificar si una carpeta está vacía
function is_dir_empty($dir) {
    if (!is_readable($dir)) return null;
    return (count(scandir($dir)) == 2);
}

// Función para eliminar una carpeta recursivamente
function delete_directory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!delete_directory($dir . DIRECTORY_SEPARATOR . $item)) return false;
    }
    return rmdir($dir);
}
?>

