<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../fom.php");
    exit();
}

require_once('../config/db_config.php');

$user_id = $_SESSION['user_id'];
$user_rol = $_SESSION['rol'];

// Obtener los detalles del usuario
$query = "SELECT nombre, email, idioma, avatar FROM usuarios WHERE id = ?";
if ($stmt = $db->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($nombre, $email, $idioma, $avatar);
    $stmt->fetch();
    $stmt->close();
} else {
    die('Error al preparar la consulta.');
}

// Ruta del avatar
if (!empty($avatar)) {
    $avatar_file_path = __DIR__ . '/../public/avatars/' . $avatar;
    if (file_exists($avatar_file_path)) {
        // Ruta para el navegador
        $avatar_url = '/FileOManager/public/avatars/' . rawurlencode($avatar);
    } else {
        // El archivo no existe, usar avatar por defecto
        $avatar_url = '/FileOManager/public/avatars/Avatar_Default_1.jpg';
    }
} else {
    // No hay avatar configurado, usar avatar por defecto
    $avatar_url = '/FileOManager/public/avatars/Avatar_Default_1.jpg';
}

// Incluir archivo de idioma
$lang = include("../languages/lang_{$idioma}.php");

// Datos del logo
$logo_path = '../public/logo/Logo_Default.jpg';
// Obtener el logo desde la configuración si existe
$query = "SELECT setting_value FROM config WHERE setting_name = 'logo_path'";
if ($stmt = $db->prepare($query)) {
    $stmt->execute();
    $stmt->bind_result($logo_db_path);
    if ($stmt->fetch() && !empty($logo_db_path) && file_exists(__DIR__ . '/../' . $logo_db_path)) {
        $logo_path = '../' . htmlspecialchars($logo_db_path, ENT_QUOTES, 'UTF-8');
    }
    $stmt->close();
}

// Función para listar archivos de imagen en un directorio
function listarImagenes($dir) {
    $imagenes = [];
    foreach (scandir($dir) as $archivo) {
        if (in_array(strtolower(pathinfo($archivo, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
            $imagenes[] = $archivo;
        }
    }
    return $imagenes;
}

// Listar lenguajes disponibles
$language_dir = '../languages';
$language_files = [];
foreach (scandir($language_dir) as $file) {
    if (preg_match('/^lang_(\w+)\.php$/', $file, $matches)) {
        $language_files[] = $matches[1];
    }
}

// Listar avatares disponibles
$avatar_dir = '../public/avatars';
$avatar_images = listarImagenes($avatar_dir);

// Listar logos disponibles (solo si el usuario es admin o superadmin)
if ($user_rol == 'admin' || $user_rol == 'superadmin') {
    $logo_dir = '../public/logo';
    $logo_images = listarImagenes($logo_dir);
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($idioma); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($lang['settings']); ?></title>
    <link rel="stylesheet" href="../public/style.css">
    <script>
        function confirmarGuardado() {
            return confirm('<?php echo htmlspecialchars($lang['confirm_save_changes']); ?>');
        }
    </script>
</head>
<body>
    <h1><?php echo htmlspecialchars($lang['settings']); ?></h1>

    <!-- Mostrar mensajes al usuario -->
    <?php
    if (isset($_SESSION['mensaje'])) {
        echo "<p>" . htmlspecialchars($_SESSION['mensaje'], ENT_QUOTES, 'UTF-8') . "</p>";
        unset($_SESSION['mensaje']);
    }
    ?>

    <form action="../backend/update_settings.php" method="POST" enctype="multipart/form-data" onsubmit="return confirmarGuardado();">
        <!-- Seleccionar idioma -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['language']); ?></legend>
            <label for="language"><?php echo htmlspecialchars($lang['select_language']); ?>:</label>
            <select id="language" name="language">
                <?php foreach ($language_files as $language_code): ?>
                    <option value="<?php echo htmlspecialchars($language_code, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($idioma == $language_code ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($language_code, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>
            <label for="language_file"><?php echo htmlspecialchars($lang['upload_new_language']); ?>:</label>
            <input type="file" id="language_file" name="language_file" accept=".php"><br>
        </fieldset>

        <!-- Cambiar avatar -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['user_avatar']); ?></legend>
            <p><img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Avatar actual" style="max-height: 60px;"></p>
            <label for="avatar_selection"><?php echo htmlspecialchars($lang['select_avatar']); ?>:</label>
            <select id="avatar_selection" name="avatar_selection">
                <?php foreach ($avatar_images as $avatar_image): ?>
                    <option value="<?php echo htmlspecialchars($avatar_image, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($avatar == $avatar_image ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($avatar_image, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>
            <label for="avatar"><?php echo htmlspecialchars($lang['upload_new_avatar']); ?>:</label>
            <input type="file" id="avatar" name="avatar" accept="image/*"><br>
        </fieldset>

        <!-- Cambiar contraseña -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['change_password']); ?></legend>
            <label for="current_password"><?php echo htmlspecialchars($lang['current_password']); ?>:</label><br>
            <input type="password" id="current_password" name="current_password"><br>
            <label for="new_password"><?php echo htmlspecialchars($lang['new_password']); ?>:</label><br>
            <input type="password" id="new_password" name="new_password"><br>
            <label for="confirm_password"><?php echo htmlspecialchars($lang['confirm_password']); ?>:</label><br>
            <input type="password" id="confirm_password" name="confirm_password"><br>
        </fieldset>

        <?php if ($user_rol == 'admin' || $user_rol == 'superadmin'): ?>
        <!-- Sección para modificar el logo del proyecto -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['project_logo']); ?></legend>
            <p><img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Logo actual" style="max-height: 60px;"></p>
            <label for="logo_selection"><?php echo htmlspecialchars($lang['select_existing_logo']); ?>:</label>
            <select id="logo_selection" name="logo_selection">
                <?php foreach ($logo_images as $logo_image): ?>
                    <option value="<?php echo htmlspecialchars($logo_image, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($logo_image, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>
            <label for="logo"><?php echo htmlspecialchars($lang['upload_new_logo']); ?>:</label>
            <input type="file" id="logo" name="logo" accept="image/*"><br>
        </fieldset>
        <?php endif; ?>

        <button type="submit"><?php echo htmlspecialchars($lang['save_changes']); ?></button>
    </form>
</body>
</html>

