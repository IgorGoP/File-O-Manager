<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../fom.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File OManager - Dashboard</title>
    <script>
        function confirmarEliminacion(archivo) {
            const modal = document.getElementById('modalEliminar');
            const confirmarBtn = document.getElementById('confirmarEliminar');
            modal.style.display = 'block';
            confirmarBtn.onclick = function() {
                eliminarArchivo(archivo);
            };
        }

        function cerrarModal() {
            document.getElementById('modalEliminar').style.display = 'none';
        }

        function eliminarArchivo(archivo) {
            const formData = new FormData();
            formData.append('archivo', archivo);

            fetch('../backend/delete_file.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Mostrar mensaje de éxito
                    alert(data.message);
                    // Recargar la lista de archivos
                    window.location.reload();
                } else {
                    // Mostrar mensaje de error
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Error al eliminar el archivo: ' + error);
            });

            cerrarModal();
        }
    </script>
    <style>
        /* Estilos para el modal */
        #modalEliminar {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        #modalContent {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 300px;
            text-align: center;
        }
        #cerrarModal {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Bienvenido al Dashboard de File OManager, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
    <p><a href="../backend/logout.php">Cerrar sesión</a></p>

    <!-- Mostrar mensaje de resultado de la subida o eliminación de archivo -->
    <?php
    if (isset($_SESSION['mensaje'])) {
        echo "<p>" . htmlspecialchars($_SESSION['mensaje'], ENT_QUOTES, 'UTF-8') . "</p>";
        unset($_SESSION['mensaje']); // Eliminar el mensaje después de mostrarlo
    }
    ?>

    <!-- Sección para subir un archivo -->
    <h2>Subir un Archivo</h2>
    <form action="../backend/upload.php" method="POST" enctype="multipart/form-data">
        <label for="archivo">Seleccione un archivo:</label>
        <input type="file" id="archivo" name="archivo" required><br><br>
        <button type="submit">Subir Archivo</button>
    </form>

    <!-- Sección para listar archivos -->
    <h2>Archivos Subidos</h2>
    <?php
    // Mostrar la lista de archivos
    $directorio = '../uploads/';

    // Verificar si el directorio existe
    if (is_dir($directorio)) {
        // Abrir el directorio
        if ($handle = opendir($directorio)) {
            echo "<ul>";

            // Leer archivos del directorio
            while (false !== ($archivo = readdir($handle))) {
                if ($archivo != "." && $archivo != "..") {
                    $rutaArchivo = htmlspecialchars($directorio . $archivo, ENT_QUOTES, 'UTF-8');
                    $archivoEscaped = htmlspecialchars($archivo, ENT_QUOTES, 'UTF-8');
                    echo "<li><a href='$rutaArchivo' download>$archivoEscaped</a> | <a href='#' onclick=\"confirmarEliminacion('$archivoEscaped')\">Eliminar</a></li>";
                }
            }

            echo "</ul>";
            closedir($handle);
        } else {
            echo "Error: No se pudo abrir el directorio de archivos.";
        }
    } else {
        echo "Error: El directorio de archivos no existe.";
    }
    ?>

    <!-- Modal para confirmar eliminación -->
    <div id="modalEliminar">
        <div id="modalContent">
            <p>¿Estás seguro de que deseas eliminar este archivo?</p>
            <button id="confirmarEliminar">Aceptar</button>
            <button id="cerrarModal" onclick="cerrarModal()">Cancelar</button>
        </div>
    </div>
</body>
</html>

