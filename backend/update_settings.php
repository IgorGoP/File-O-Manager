<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../fom.php");
    exit();
}

require_once('../config/db_config.php');

$user_id = $_SESSION['user_id'];
$user_rol = $_SESSION['rol'];

// Obtener los datos del formulario
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$language = $_POST['language'] ?? 'es';
$avatar = $_FILES['avatar'] ?? null;
$avatar_selection = $_POST['avatar_selection'] ?? '';
$logo = $_FILES['logo'] ?? null;
$logo_selection = $_POST['logo_selection'] ?? '';
$language_file = $_FILES['language_file'] ?? null;

// 1. Procesar la actualización del logo si el usuario es admin o superadmin
if (($user_rol == 'admin' || $user_rol == 'superadmin') && ($logo || $logo_selection)) {
    $logo_dir = '../public/logo/';

    // Procesar la subida del nuevo logo
    if ($logo && $logo['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($logo['type'], $allowed_types)) {
            $ext = pathinfo($logo['name'], PATHINFO_EXTENSION);
            $logo_name = 'Logo_Default.' . $ext;
            $logo_path = $logo_dir . $logo_name;
            move_uploaded_file($logo['tmp_name'], $logo_path);
            $_SESSION['mensaje'] = 'Logo actualizado exitosamente.';
        } else {
            $_SESSION['mensaje'] = 'Tipo de archivo no permitido para el logo.';
        }
    } elseif ($logo_selection) {
        // Procesar la selección de un logo existente
        $selected_logo = basename($logo_selection);
        $logo_source = $logo_dir . $selected_logo;
        $logo_destination = $logo_dir . 'Logo_Default.jpg';

        if (file_exists($logo_source)) {
            copy($logo_source, $logo_destination);
            $_SESSION['mensaje'] = 'Logo actualizado exitosamente.';
        } else {
            $_SESSION['mensaje'] = 'El logo seleccionado no existe.';
        }
    }
}

// 2. Cambiar contraseña
if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
    if ($new_password === $confirm_password) {
        // Verificar la contraseña actual
        $query = "SELECT contrasena FROM usuarios WHERE id = ?";
        if ($stmt = $db->prepare($query)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            $stmt->close();

            if (password_verify($current_password, $hashed_password)) {
                // Actualizar la contraseña
                $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $update_query = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
                if ($update_stmt = $db->prepare($update_query)) {
                    $update_stmt->bind_param("si", $new_hashed_password, $user_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                    $_SESSION['mensaje'] = 'Contraseña actualizada exitosamente.';
                }
            } else {
                $_SESSION['mensaje'] = 'La contraseña actual es incorrecta.';
            }
        }
    } else {
        $_SESSION['mensaje'] = 'Las nuevas contraseñas no coinciden.';
    }
}

// 3. Actualizar avatar
$avatar_dir = '../public/avatars/';
if ($avatar && $avatar['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($avatar['type'], $allowed_types)) {
        $ext = pathinfo($avatar['name'], PATHINFO_EXTENSION);
        $avatar_name = 'avatar_' . $user_id . '.' . $ext;
        $avatar_path = $avatar_dir . $avatar_name;
        move_uploaded_file($avatar['tmp_name'], $avatar_path);

        // Actualizar la ruta del avatar en la base de datos
        $avatar_db_path = 'public/avatars/' . $avatar_name;
        $update_query = "UPDATE usuarios SET avatar = ? WHERE id = ?";
        if ($update_stmt = $db->prepare($update_query)) {
            $update_stmt->bind_param("si", $avatar_db_path, $user_id);
            $update_stmt->execute();
            $update_stmt->close();
            $_SESSION['mensaje'] = 'Avatar actualizado exitosamente.';
        }
    } else {
        $_SESSION['mensaje'] = 'Tipo de archivo no permitido para el avatar.';
    }
} elseif ($avatar_selection) {
    // Procesar la selección de un avatar existente
    $selected_avatar = basename($avatar_selection);
    $avatar_source = $avatar_dir . $selected_avatar;

    if (file_exists($avatar_source)) {
        $avatar_destination = $avatar_dir . 'avatar_' . $user_id . '.' . pathinfo($selected_avatar, PATHINFO_EXTENSION);
        copy($avatar_source, $avatar_destination);

        // Actualizar la ruta del avatar en la base de datos
        $avatar_db_path = 'public/avatars/' . basename($avatar_destination);
        $update_query = "UPDATE usuarios SET avatar = ? WHERE id = ?";
        if ($update_stmt = $db->prepare($update_query)) {
            $update_stmt->bind_param("si", $avatar_db_path, $user_id);
            $update_stmt->execute();
            $update_stmt->close();
            $_SESSION['mensaje'] = 'Avatar actualizado exitosamente.';
        }
    } else {
        $_SESSION['mensaje'] = 'El avatar seleccionado no existe.';
    }
}

// 4. Actualizar idioma
if ($language) {
    $update_query = "UPDATE usuarios SET idioma = ? WHERE id = ?";
    if ($update_stmt = $db->prepare($update_query)) {
        $update_stmt->bind_param("si", $language, $user_id);
        $update_stmt->execute();
        $update_stmt->close();
        $_SESSION['language'] = $language; // Actualizar en la sesión
        $_SESSION['mensaje'] = 'Idioma actualizado exitosamente.';
    }
}

// Procesar la subida de un nuevo archivo de idioma
if ($language_file && $language_file['error'] == 0) {
    $language_dir = '../languages/';
    $language_filename = basename($language_file['name']);

    if (preg_match('/^lang_\w+\.php$/', $language_filename)) {
        $language_path = $language_dir . $language_filename;
        move_uploaded_file($language_file['tmp_name'], $language_path);
        $_SESSION['mensaje'] = 'Archivo de idioma subido exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Nombre de archivo de idioma no válido.';
    }
}

header("Location: ../frontend/settings.php");
exit();
?>

