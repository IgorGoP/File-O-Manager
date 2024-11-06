window.addEventListener('load', function() {
    const editButton = document.getElementById('editButton');
    const saveButton = document.getElementById('saveButton');
    const cancelButton = document.getElementById('cancelButton');
    const fields = document.querySelectorAll('#userSettingsForm input, #userSettingsForm select');
    const form = document.getElementById('userSettingsForm');

    // Función para habilitar el modo de edición
    function enableEditMode() {
        fields.forEach(field => {
            field.removeAttribute('readonly');
            field.removeAttribute('disabled');
        });
        editButton.style.display = 'none';
        saveButton.style.display = 'inline';
        cancelButton.style.display = 'inline';
    }

    // Función para habilitar el modo de solo lectura
    function disableEditMode() {
        fields.forEach(field => {
            if (field.tagName === 'SELECT') {
                field.setAttribute('disabled', true);
            } else {
                field.setAttribute('readonly', true);
            }
        });
        editButton.style.display = 'inline';
        saveButton.style.display = 'none';
        cancelButton.style.display = 'none';
    }

    // Evento para el botón "Editar" que habilita el modo de edición
    editButton.addEventListener('click', enableEditMode);

    // Evento para el botón "Cancelar" que vuelve a modo de solo lectura
    cancelButton.addEventListener('click', disableEditMode);

    // Evento para el formulario "submit" al presionar "Guardar"
    form.addEventListener('submit', function(event) {
        // Evita el submit predeterminado para el manejo adecuado de contenedores
        event.preventDefault(); 
        if (saveButton.style.display !== 'none') {
            form.submit(); // Envía el formulario solo si está en modo de edición
        }
    });

    // Inicializar en modo de solo lectura
    disableEditMode();
});

