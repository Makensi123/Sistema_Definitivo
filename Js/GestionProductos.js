// Mostrar notificación si hay un mensaje en la URL
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const message = urlParams.get('message');

    if (success && message) {
        showNotification(message, success === 'true');
    }

    // Inicializar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Función para mostrar notificación
function showNotification(message, isSuccess) {
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notificationMessage');

    notificationMessage.textContent = message;
    notification.style.display = 'block';

    const alert = notification.querySelector('.alert');
    alert.classList.remove('alert-success', 'alert-danger');
    alert.classList.add(isSuccess ? 'alert-success' : 'alert-danger');

    // Ocultar después de 5 segundos
    setTimeout(() => {
        notification.style.display = 'none';
    }, 5000);
}

// Búsqueda en tiempo real con debounce
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function () {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const term = this.value.toLowerCase();
        let hasResults = false;

        document.querySelectorAll('#productosTable tr').forEach(row => {
            if (row.querySelector('td')) {
                const text = row.textContent.toLowerCase();
                const isMatch = text.includes(term);
                row.style.display = isMatch ? '' : 'none';

                if (isMatch) {
                    row.classList.add('animate__animated', 'animate__fadeIn');
                    hasResults = true;
                }
            }
        });

        // Mostrar mensaje si no hay resultados
        const noResultsRow = document.querySelector('#productosTable tr td[colspan]');
        if (!hasResults && !noResultsRow) {
            const tbody = document.getElementById('productosTable');
            tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">No se encontraron resultados</h5>
                        <p class="text-muted">Intenta con otros términos de búsqueda</p>
                    </div>
                </td>
            </tr>
        `;
        } else if (hasResults && noResultsRow && noResultsRow.getAttribute('colspan')) {
            // Restaurar tabla si había mensaje de no resultados
            location.reload();
        }
    }, 300);
});

// Configurar modal para editar
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const modal = new bootstrap.Modal(document.getElementById('productoModal'));
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square me-2"></i> Editar Producto';
        document.getElementById('accion').value = 'editar';
        document.getElementById('productoId').value = this.dataset.id;
        document.getElementById('codigo').value = this.dataset.codigo;
        document.getElementById('descripcion').value = this.dataset.descripcion;
        document.getElementById('precio').value = this.dataset.precio;
        modal.show();
    });
});

// Configurar modal para detalles
document.querySelectorAll('.info-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('detailCodigo').textContent = this.dataset.codigo;
        document.getElementById('detailDescripcion').textContent = this.dataset.descripcion;
        document.getElementById('detailPrecio').textContent = 'S/ ' + this.dataset.precio;

        new bootstrap.Modal(document.getElementById('detailModal')).show();
    });
});

// Configurar modal para eliminar
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('deleteId').value = this.dataset.id;
        new bootstrap.Modal(document.getElementById('confirmModal')).show();
    });
});

// Limpiar modal al cerrar
document.getElementById('productoModal').addEventListener('hidden.bs.modal', function () {
    this.querySelector('form').reset();
    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-box-seam me-2"></i> Nuevo Producto';
    document.getElementById('accion').value = 'agregar';
    document.getElementById('productoId').value = '';
    // Resetear clases de validación
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.classList.remove('was-validated');
    });
});

// Validación de formulario con Bootstrap
(function () {
    'use strict';

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation');

    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            } else {
                // Mostrar loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';
            }

            form.classList.add('was-validated');
        }, false);
    });
})();

// Exportar a Excel
document.getElementById('exportBtn').addEventListener('click', function () {
    // Crear una hoja de trabajo
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.table_to_sheet(document.querySelector('table'));

    // Añadir la hoja al libro
    XLSX.utils.book_append_sheet(wb, ws, "Productos");

    // Generar el archivo Excel
    XLSX.writeFile(wb, `Productos_Electrotop_${new Date().toISOString().slice(0, 10)}.xlsx`);

    // Mostrar notificación
    Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Exportación completada',
        text: 'Los datos se han exportado correctamente',
        showConfirmButton: false,
        timer: 2000,
        backdrop: false
    });
});

// Efecto de carga para los botones de acción
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function () {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Procesando...';
            submitBtn.disabled = true;
        }
    });
});

// Mostrar loading overlay en los formularios de eliminación
document.getElementById('deleteForm').addEventListener('submit', function () {
    document.getElementById('loadingOverlay').style.display = 'flex';
});

// Configurar el modal de detalles para mostrar información del usuario
document.getElementById('detailModal').addEventListener('show.bs.modal', function () {
    // Esta información ya está pre-cargada desde PHP
    document.getElementById('detailFecha').textContent = new Date().toLocaleString('es-PE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
});

// Mejorar la experiencia de búsqueda con tecla Enter
document.getElementById('searchInput').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        // Forzar la búsqueda inmediata al presionar Enter
        clearTimeout(searchTimeout);
        const term = this.value.toLowerCase();

        document.querySelectorAll('#productosTable tr').forEach(row => {
            if (row.querySelector('td')) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            }
        });
    }
});