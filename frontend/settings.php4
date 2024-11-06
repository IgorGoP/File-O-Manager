<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../../fom.php");
    exit();
}

require_once('../config/db_config.php');

// Obtener el ID del usuario desde la sesión
$user_id = $_SESSION['user_id'];

// Obtener los detalles del usuario desde la base de datos
$query = "SELECT nom_completo, email, idioma, avatar, rol FROM usuarios WHERE id = ?";
if ($stmt = $db->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($nom_completo, $email, $idioma, $avatar, $rol);
    $stmt->fetch();
    $stmt->close();
} else {
    die('Error al preparar la consulta.');
}

// Ruta del avatar
if (!empty($avatar) && file_exists('FileOManager/public/avatars/' . $avatar)) {
    $avatar_path = 'FileOManager/public/avatars/' . htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8');
} else {
    $avatar_path = 'FileOManager/public/avatars/Avatar_Default_8.jpg';
}

// Procesar la actualización de la configuración
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];
    $idioma = $_POST['idioma'];
    $avatar_subido = $_FILES['avatar']['name'];

    // Comenzar la transacción
    $db->begin_transaction();

    try {
        // Actualizar los datos del usuario
        $updateQuery = "UPDATE usuarios SET nom_completo = ?, email = ?, idioma = ? WHERE id = ?";
        if ($stmt = $db->prepare($updateQuery)) {
            $stmt->bind_param("sssi", $nombre_completo, $email, $idioma, $user_id);
            $stmt->execute();
            $stmt->close();
        }

        // Subir nuevo avatar si se ha seleccionado uno
        if (!empty($avatar_subido)) {
            $target_dir = "FileOManager/public/avatars/";
            $target_file = $target_dir . basename($avatar_subido);
            move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file);

            // Actualizar el campo avatar en la base de datos
            $updateAvatarQuery = "UPDATE usuarios SET avatar = ? WHERE id = ?";
            if ($stmt = $db->prepare($updateAvatarQuery)) {
                $stmt->bind_param("si", $avatar_subido, $user_id);
                $stmt->execute();
                $stmt->close();
            }
        }

        // Confirmar la transacción
        $db->commit();
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $db->rollback();
        die('Error al actualizar los datos: ' . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($idioma); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - File OManager</title>
    <link rel="stylesheet" href="../backend/settings.css">
    <style>
        /* Estilos básicos para el formulario */
        form {
            margin: 20px 0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Configuración</h1>
    <div>
        <img src="<?php echo $avatar_path; ?>" alt="Avatar" class="avatar-preview">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre_completo">Nombre Completo</label>
                <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo htmlspecialchars($nom_completo, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña (dejar vacío si no se desea cambiar)</label>
                <input type="password" id="contrasena" name="contrasena">
            </div>
            <div class="form-group">
                <label for="idioma">Idioma</label>
                <select id="idioma" name="idioma" required>
                    <option value="es" <?php echo ($idioma == 'es') ? 'selected' : ''; ?>>Español</option>
                    <option value="en" <?php echo ($idioma == 'en') ? 'selected' : ''; ?>>Inglés</option>
                    <!-- Agrega más idiomas según sea necesario -->
                </select>
            </div>
            <div class="form-group">
                <label for="avatar">Seleccionar nuevo avatar (opcional)</label>
                <input type="file" id="avatar" name="avatar">
            </div>
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>

