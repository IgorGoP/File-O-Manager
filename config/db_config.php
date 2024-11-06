<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'adfom');
define('DB_PASSWORD', '4d3f0m');
define('DB_NAME', 'fileoma');

// Crear conexión a la base de datos
$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar conexión
if ($db->connect_error) {
    die('Error de conexión: ' . $db->connect_error);
}
?>
