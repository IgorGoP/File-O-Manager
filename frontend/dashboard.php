<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../../fom.php");
    exit();
}

require_once('../config/db_config.php');

// Obtener el ID y nombre del usuario desde la sesión
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Obtener los detalles del usuario desde la base de datos
$query = "SELECT idioma, avatar FROM usuarios WHERE id = ?";
if ($stmt = $db->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($idioma, $avatar);
    $stmt->fetch();
    $stmt->close();
} else {
    die('Error al preparar la consulta.');
}

// Incluir archivo de idioma según la preferencia del usuario
$lang = include("../languages/lang_{$idioma}.php");

// Datos del logo
$logo_path = '../public/logo/Logo_Default.jpg'; // Ruta por defecto
$query = "SELECT setting_value FROM config WHERE setting_name = 'logo_path'";
if ($stmt = $db->prepare($query)) {
    $stmt->execute();
    $stmt->bind_result($logo_db_path);
    if ($stmt->fetch()) {
        $logo_path = '../' . htmlspecialchars($logo_db_path, ENT_QUOTES, 'UTF-8');
    }
    $stmt->close();
}

// Ruta del avatar
if (!empty($avatar) && file_exists('../public/avatars/' . $avatar)) {
    $avatar_path = '../public/avatars/' . htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8');
} else {
    // Usar Avatar_Default_8.jpg como avatar por defecto
    $avatar_path = '../public/avatars/Avatar_Default_8.jpg';
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($idioma); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File OManager - <?php echo htmlspecialchars($lang['dashboard']); ?></title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <!-- Barra de navegación -->
    <nav>
        <div class="nav-container">
            <div class="nav-left">
                <!-- Avatar del usuario -->
                <div class="avatar" title="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
                    <img src="<?php echo $avatar_path; ?>" alt="Avatar">
                </div>
                <!-- Campo de búsqueda -->
                <form class="search-form">
                    <input type="text" placeholder="<?php echo htmlspecialchars($lang['search_placeholder'] ?? 'Buscar...'); ?>" name="search">
                </form>
            </div>
            <div class="nav-right">
                <a href="../frontend/settings.php"><?php echo htmlspecialchars($lang['settings']); ?></a>
                <a href="../backend/logout.php"><?php echo htmlspecialchars($lang['logout']); ?></a>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main>
        <!-- Mostrar mensajes al usuario -->
        <?php
        if (isset($_SESSION['mensaje'])) {
            echo "<div class='message'>" . htmlspecialchars($_SESSION['mensaje'], ENT_QUOTES, 'UTF-8') . "</div>";
            unset($_SESSION['mensaje']); // Eliminar el mensaje después de mostrarlo
        }
        ?>

        <!-- Sección para subir un archivo -->
        <section class="upload-section">
            <form action="../backend/upload.php" method="POST" enctype="multipart/form-data">
                <label for="archivo"><?php echo htmlspecialchars($lang['select_file']); ?></label>
                <input type="file" id="archivo" name="archivo" required>
                <button type="submit"><?php echo htmlspecialchars($lang['upload_button']); ?></button>
            </form>
        </section>

        <!-- Sección para listar archivos -->
        <section class="files-section">
            <?php
            // Listar archivos desde el directorio 'uploads'
            $uploads_dir = '../uploads/';

            if (is_dir($uploads_dir)) {
                if ($handle = opendir($uploads_dir)) {
                    echo "<table>";
                    echo "<tr><th>" . htmlspecialchars($lang['file_name']) . "</th><th>" . htmlspecialchars($lang['actions']) . "</th></tr>";
                    while (false !== ($file = readdir($handle))) {
                        if ($file != '.' && $file != '..') {
                            $file_path = $uploads_dir . $file;
                            $file_url = '../uploads/' . rawurlencode($file);
                            $file_name_escaped = htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
                            echo "<tr>";
                            echo "<td>$file_name_escaped</td>";
                            echo "<td><a href='$file_url' download class='button-download'>" . htmlspecialchars($lang['download']) . "</a> ";
                            echo "<button onclick=\"confirmarEliminacion('$file_name_escaped')\" class='button-delete'>" . htmlspecialchars($lang['delete']) . "</button></td>";
                            echo "</tr>";
                        }
                    }
                    echo "</table>";
                    closedir($handle);
                } else {
                    echo "<p class='error'>" . htmlspecialchars($lang['error_loading_files']) . "</p>";
                }
            } else {
                echo "<p class='error'>" . htmlspecialchars($lang['error_directory_not_exist'] ?? 'El directorio no existe.') . "</p>";
            }
            ?>
        </section>
    </main>

    <!-- Modal para confirmar eliminación -->
    <div id="modalEliminar">
        <div id="modalContent">
            <p><?php echo htmlspecialchars($lang['delete_confirmation']); ?></p>
            <button id="confirmarEliminar" class="button-delete"><?php echo htmlspecialchars($lang['delete']); ?></button>
            <button id="cerrarModal" onclick="cerrarModal()" class="button-cancel"><?php echo htmlspecialchars($lang['cancel']); ?></button>
        </div>
    </div>

    <!-- Pie de página -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> File OManager</p>
    </footer>

    <!-- Scripts -->
    <script>
        var archivoAEliminar = null;

        function confirmarEliminacion(fileName) {
            archivoAEliminar = fileName;
            document.getElementById('modalEliminar').style.display = 'block';
        }

        function cerrarModal() {
            document.getElementById('modalEliminar').style.display = 'none';
            archivoAEliminar = null;
        }

        document.getElementById('confirmarEliminar').addEventListener('click', function() {
            if (archivoAEliminar) {
                window.location.href = '../backend/delete_file.php?file=' + encodeURIComponent(archivoAEliminar);
            }
        });
    </script>
</body>
</html>

