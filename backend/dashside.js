// Exportar funciones para crear carpetas y archivos
export function crearCarpeta() {
    const nombreCarpeta = prompt("Ingrese el nombre de la nueva carpeta:");
    if (nombreCarpeta) {
        fetch('backend/create_folder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `name=${encodeURIComponent(nombreCarpeta)}`
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.status === "success") {
                agregarElementoAlDashboard(data.tipo, data.nombre);
            } else {
                alert(data.message || "Error al crear el elemento.");
            }
        })
        .catch(error => {
            console.error("Error al crear la carpeta:", error);
            alert(error.message || "Error al crear la carpeta.");
        });
    }
}

export function crearArchivo() {
    const nombreArchivo = prompt("Ingrese el nombre del nuevo archivo:");
    if (nombreArchivo) {
        fetch('backend/create_file.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `name=${encodeURIComponent(nombreArchivo)}`
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.status === "success") {
                agregarElementoAlDashboard(data.tipo, data.nombre);
            } else {
                alert(data.message || "Error al crear el elemento.");
            }
        })
        .catch(error => {
            console.error("Error al crear el archivo:", error);
            alert(error.message || "Error al crear el archivo.");
        });
    }
}

// Asignar eventos de eliminación dinámicamente
export function asignarEventosEliminar() {
    const botonesEliminar = document.querySelectorAll('.files-section .button-delete[data-file]');
    botonesEliminar.forEach(button => {
        button.addEventListener('click', function() {
            const fileName = this.getAttribute('data-file');
            confirmarEliminacion(fileName);
        });
    });
}

// Modal y confirmación de eliminación
let elementoAEliminar = null;

export function confirmarEliminacion(fileName) {
    elementoAEliminar = fileName;
    document.getElementById('modalEliminar').style.display = 'block';
}

export function cerrarModal() {
    document.getElementById('modalEliminar').style.display = 'none';
    elementoAEliminar = null;
}

export function confirmarEliminar() {
    if (elementoAEliminar) {
        fetch('backend/delete_file.php', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `file=${encodeURIComponent(elementoAEliminar)}`
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.status === "success") {
                eliminarElementoDelDashboard(data.nombre);
            } else {
                alert(data.message || "Error al eliminar el elemento.");
            }
            cerrarModal();
        })
        .catch(error => {
            console.error("Error al eliminar el elemento:", error);
            alert(error.message || "Error al eliminar el elemento.");
            cerrarModal();
        });
    }
}

// Función para eliminar el elemento del dashboard sin recargar la página
function eliminarElementoDelDashboard(nombre) {
    const botonesEliminar = document.querySelectorAll('.files-section .button-delete[data-file]');
    botonesEliminar.forEach(button => {
        if (button.getAttribute('data-file') === nombre) {
            const fila = button.closest('tr');
            if (fila) {
                fila.remove();
            }
        }
    });
}

// Función para agregar elementos al dashboard
function agregarElementoAlDashboard(tipo, nombre) {
    const filesSection = document.getElementById('filesSection');
    const table = filesSection.querySelector('table');
    const tbody = table.querySelector('tbody') || table;

    // Crear una nueva fila
    const nuevaFila = document.createElement('tr');

    // Crear y añadir la celda del nombre
    const celdaNombre = document.createElement('td');
    celdaNombre.innerHTML = htmlspecialchars(nombre);
    nuevaFila.appendChild(celdaNombre);

    // Crear y añadir la celda del tipo
    const celdaTipo = document.createElement('td');
    if (tipo === "carpeta") {
        celdaTipo.innerHTML = '<i class="fas fa-folder"></i> Carpeta';
    } else {
        celdaTipo.innerHTML = '<i class="fas fa-file"></i> Archivo';
    }
    nuevaFila.appendChild(celdaTipo);

    // Crear y añadir la celda de acciones
    const celdaAcciones = document.createElement('td');
    if (tipo === "archivo") {
        const botonDescargar = document.createElement('a');
        botonDescargar.href = `uploads/${encodeURIComponent(nombre)}`;
        botonDescargar.download = nombre;
        botonDescargar.classList.add('button-download');
        botonDescargar.title = 'Descargar';
        botonDescargar.innerHTML = '<i class="fas fa-download"></i>';
        celdaAcciones.appendChild(botonDescargar);
    }

    const botonEliminar = document.createElement('button');
    botonEliminar.classList.add('button-delete');
    botonEliminar.setAttribute('data-file', nombre);
    botonEliminar.title = 'Eliminar';
    botonEliminar.innerHTML = '<i class="fas fa-trash-alt"></i>';
    celdaAcciones.appendChild(botonEliminar);

    nuevaFila.appendChild(celdaAcciones);
    tbody.appendChild(nuevaFila);

    // Re-asignar los event listeners para el nuevo botón de eliminar
    asignarEventosEliminar();
}

// Función para sanitizar strings
function htmlspecialchars(str) {
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

