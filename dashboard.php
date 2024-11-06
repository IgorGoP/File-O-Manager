<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../fom.php");
    exit();
}

require_once('config/db_config.php');

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
$lang = include("languages/lang_{$idioma}.php");

// Ruta del avatar
if (!empty($avatar) && file_exists('public/avatars/' . $avatar)) {
    $avatar_path = 'public/avatars/' . htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8');
} else {
    $avatar_path = 'public/avatars/Avatar_Default_8.jpg';
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($idioma); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File OManager - <?php echo htmlspecialchars($lang['dashboard']); ?></title>
    <link rel="stylesheet" href="backend/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Estilos básicos para la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        /* Estilos para botones */
        .button-download, .button-delete {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            font-size: 1.2em;
        }
        .button-download:hover, .button-delete:hover {
            color: #007BFF;
        }
        /* Estilos para el modal */
        #modalEliminar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        #modalContent {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            width: 300px;
        }
        .message {
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav>
        <div class="nav-container">
            <div class="nav-left">
                <div class="avatar-container">
                    <a href="#" title="Regresar al Dashboard" id="avatarLink">
                        <img src="<?php echo $avatar_path; ?>" alt="Avatar" class="avatar" style="width:50px; height:50%; border-radius:50%;">
                    </a>
                </div>
                <form class="search-form">
                    <input type="text" placeholder="<?php echo htmlspecialchars($lang['search_placeholder'] ?? 'Buscar...'); ?>" name="search">
                </form>
            </div>
            <div class="nav-right">
                <button id="settingsButton" class="nav-button"><?php echo htmlspecialchars($lang['settings']); ?></button>
                <button id="logoutButton" class="nav-button"><?php echo htmlspecialchars($lang['logout']); ?></button>
            </div>
        </div>
    </nav>

    <!-- Barra lateral de acciones -->
    <aside class="sidebar">
        <ul>
            <li><button onclick="window.location.href='dashboard.php'" id="misArchivosBtn"><i class="fas fa-home"></i> Mi Contenido </button></li>
            <li><button id="crearCarpetaBtn"><i class="fas fa-folder-plus"></i> <?php echo htmlspecialchars($lang['create_folder'] ?? 'Crear Carpeta'); ?></button></li>
            <li><button id="crearArchivoBtn"><i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($lang['create_file'] ?? 'Crear Archivo'); ?></button></li>
            <li>
                <form id="uploadForm" action="backend/upload.php" method="POST" enctype="multipart/form-data" class="upload-form">
                    <label for="archivo" class="upload-button"><i class="fas fa-upload"></i> <?php echo htmlspecialchars($lang['upload_file'] ?? 'Subir Archivo'); ?></label>
                    <input type="file" id="archivo" name="archivo" style="display: none;" required>
                </form>
            </li>
        </ul>
        <footer class="sidebar-footer">&copy; 2024 File OManager</footer>
    </aside>

    <!-- Contenido principal -->
    <main>
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="message"><?php echo htmlspecialchars($_SESSION['mensaje'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <div id="configuracionContainer" style="display: none;"></div>

        <!-- Sección para listar archivos y carpetas -->
        <section class="files-section" id="filesSection">
            <?php
            $uploads_dir = 'uploads/';
            if (is_dir($uploads_dir) && $handle = opendir($uploads_dir)) {
                echo "<table><thead><tr><th>" . htmlspecialchars($lang['file_name'] ?? 'Nombre') . "</th><th>" . htmlspecialchars($lang['type'] ?? 'Tipo') . "</th><th>" . htmlspecialchars($lang['actions'] ?? 'Acciones') . "</th></tr></thead><tbody>";
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..') {
                        $file_path = $uploads_dir . $file;
                        $file_url = 'uploads/' . rawurlencode($file);
                        $file_name_escaped = htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
                        $type = is_file($file_path) ? '<i class="fas fa-file"></i> Archivo' : '<i class="fas fa-folder"></i> Carpeta';
                        $actions = "<a href='$file_url' download class='button-download'><i class='fas fa-download'></i></a> <button class='button-delete' data-file='$file_name_escaped'><i class='fas fa-trash-alt'></i></button>";
                        echo "<tr><td>$file_name_escaped</td><td>$type</td><td>$actions</td></tr>";
                    }
                }
                echo "</tbody></table>";
                closedir($handle);
            } else {
                echo "<p class='error'>Error al cargar los archivos.</p>";
            }
            ?>
        </section>
    </main>

    <!-- Modal para confirmar eliminación -->
    <div id="modalEliminar">
        <div id="modalContent">
            <p><?php echo htmlspecialchars($lang['delete_confirmation'] ?? '¿Estás seguro de que deseas eliminar este elemento?'); ?></p>
            <button id="confirmarEliminar" class="button-delete"><i class="fas fa-check"></i> <?php echo htmlspecialchars($lang['confirm'] ?? 'Confirmar'); ?></button>
            <button id="cancelarEliminar" class="button-cancel"><i class="fas fa-times"></i> <?php echo htmlspecialchars($lang['cancel'] ?? 'Cancelar'); ?></button>
        </div>
    </div>

    <!-- Importar el módulo de funciones desde dashside.js -->
    <script type="module">
        import { crearCarpeta, crearArchivo, asignarEventosEliminar, confirmarEliminar, cerrarModal } from './backend/dashside.js';

        document.getElementById('crearCarpetaBtn').addEventListener('click', crearCarpeta);
        document.getElementById('crearArchivoBtn').addEventListener('click', crearArchivo);
        document.addEventListener('DOMContentLoaded', () => {
            asignarEventosEliminar();
            document.getElementById('confirmarEliminar').addEventListener('click', confirmarEliminar);
            document.getElementById('cancelarEliminar').addEventListener('click', cerrarModal);
        });

        function resetearVista(event) {
            event.preventDefault();
            document.getElementById('filesSection').style.display = 'block';
            document.getElementById('configuracionContainer').style.display = 'none';
            window.history.pushState({}, '', 'dashboard.php');
        }
        document.getElementById('avatarLink').addEventListener('click', resetearVista);

        document.getElementById('settingsButton').onclick = async () => {
            document.getElementById('filesSection').style.display = 'none';
            const configuracionContainer = document.getElementById('configuracionContainer');
            configuracionContainer.style.display = 'block';

            try {
                const response = await fetch('frontend/settings.php');
                const html = await response.text();
                configuracionContainer.innerHTML = html;

                const script = document.createElement('script');
                script.src = '/FileOManager/backend/settings.js';
                script.defer = true;
                configuracionContainer.appendChild(script);
            } catch (error) {
                configuracionContainer.innerHTML = '<p>Error al cargar la configuración.</p>';
                console.error('Error al cargar settings.php:', error);
            }
        };

        document.getElementById('archivo').addEventListener('change', function() {
            const uploadForm = document.getElementById('uploadForm');
            if (this.files.length > 0) {
                uploadForm.submit();
            }
        });

        document.getElementById('logoutButton').onclick = () => {
            window.location.href = 'backend/logout.php';
        };
    </script>
</body>
</html>

