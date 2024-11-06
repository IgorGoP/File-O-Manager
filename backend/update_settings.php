<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit();
}

require_once('../config/db_config.php');

$user_id = $_SESSION['user_id'];
$user_rol = $_SESSION['rol'];

// Establece el encabezado de tipo de contenido para devolver JSON
header('Content-Type: application/json');

// Verifica si se recibió una acción
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // Cambiar idioma
    if ($action == 'save_language' && isset($_POST['language'])) {
        $language = $_POST['language'];
        $query = "UPDATE usuarios SET idioma = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("si", $language, $user_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true]);
        exit();
    }

    // Cambiar avatar
    elseif ($action == 'save_avatar') {
        $avatar = null;

        // Verifica si se seleccionó un avatar predeterminado
        if (!empty($_POST['avatar_selection'])) {
            $avatar = $_POST['avatar_selection'];
        }
        // Maneja la carga de un archivo de avatar personalizado
        elseif (!empty($_FILES['avatar']['name'])) {
            $target_dir = "../public/avatars/";
            $avatar_filename = basename($_FILES["avatar"]["name"]);
            $target_file = $target_dir . $avatar_filename;

            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                $avatar = $avatar_filename;
            }
        }

        // Si se ha establecido un avatar, actualiza la base de datos
        if ($avatar) {
            $query = "UPDATE usuarios SET avatar = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("si", $avatar, $user_id);
            $stmt->execute();
            $stmt->close();

            $avatar_url = '/FileOManager/public/avatars/' . rawurlencode($avatar);
            echo json_encode(['success' => true, 'avatar_url' => $avatar_url]);
        } else {
            echo json_encode(['success' => true]);
        }
        exit();
    }

    // Cambiar configuración del usuario
    elseif ($action == 'save_user_settings') {
        // Obtener los datos del formulario
        $nom_completo = $_POST['nom_completo'] ?? '';
        $email = $_POST['email'] ?? '';
        $rol = $_POST['rol'] ?? '';
        $language = $_POST['language'] ?? '';

        // Actualizar en la base de datos
        $query = "UPDATE usuarios SET nom_completo = ?, email = ?, rol = ?, idioma = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssi", $nom_completo, $email, $rol, $language, $user_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true]);
        exit();
    }

    // Cambiar logo de la empresa (solo para admin o superadmin)
    elseif ($action == 'save_logo' && in_array($user_rol, ['admin', 'superadmin'])) {
        $logo = null;

        // Verifica si se seleccionó un logo predeterminado
        if (!empty($_POST['logo_selection'])) {
            $logo = $_POST['logo_selection'];
        }
        // Maneja la carga de un archivo de logo personalizado
        elseif (!empty($_FILES['logo']['name'])) {
            $target_dir = "../public/logo/";
            $logo_filename = basename($_FILES["logo"]["name"]);
            $target_file = $target_dir . $logo_filename;

            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $logo = $logo_filename;
            }
        }

        // Si se ha establecido un logo, actualiza la base de datos
        if ($logo) {
            $query = "UPDATE config SET setting_value = ? WHERE setting_name = 'company_logo'";
            $stmt = $db->prepare($query);
            $stmt->bind_param("s", $logo);
            $stmt->execute();
            $stmt->close();

            $logo_url = '/FileOManager/public/logo/' . rawurlencode($logo);
            echo json_encode(['success' => true, 'logo_url' => $logo_url]);
        } else {
            echo json_encode(['success' => true]);
        }
        exit();
    }
}

// Si no se recibe una acción válida, devuelve un éxito genérico
echo json_encode(['success' => true]);
exit();
?>

