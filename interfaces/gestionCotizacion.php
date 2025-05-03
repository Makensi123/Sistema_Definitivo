<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
include "../Controllers/Conexion.php";

// Mostrar mensajes de éxito/error
$mensaje = '';
$tipoMensaje = '';
if (isset($_SESSION['success'])) {
    $mensaje = $_SESSION['success'];
    $tipoMensaje = 'success';
    unset($_SESSION['success']);
} elseif (isset($_SESSION['error'])) {
    $mensaje = $_SESSION['error'];
    $tipoMensaje = 'danger';
    unset($_SESSION['error']);
}

// Obtener ID para resaltar
$highlightId = $_GET['highlight'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cotizaciones - Electrotop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .estado-pendiente {
            background-color: #fff3cd;
        }

        .estado-aprobado {
            background-color: #d4edda;
        }

        .estado-rechazado {
            background-color: #f8d7da;
        }

        .estado-venta {
            background-color: #cce5ff;
        }

        .cotizacion-row {
            cursor: pointer;
        }

        .cotizacion-row:hover {
            opacity: 0.8;
        }

        .highlight-row {
            animation: highlight 2s;
        }

        @keyframes highlight {
            0% {
                background-color: #d4edda;
            }

            100% {
                background-color: transparent;
            }

        }

        .action-buttons .btn {
            margin-right: 5px;
        }

        .action-buttons .btn:last-child {
            margin-right: 0;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Barra superior -->
        <div class="top-bar bg-primary py-2 text-white mb-4">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="../assets/logo1-original.png" alt="Logo" style="height: 30px;" class="me-2">
                    <h5 class="mb-0">Gestión de Cotizaciones</h5>
                </div>
                <div>
                    <span class="me-3"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user']) ?></span>
                    <a href="dashboard.php" class="btn btn-sm btn-light">Volver al Panel</a>
                </div>
            </div>
        </div>
        <!-- Añade este modal para confirmar eliminación -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Está seguro que desea eliminar esta cotización? Esta acción no se puede deshacer.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Mostrar mensajes -->
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                    <?= $mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" id="filtroEstado">
                                <option value="">Todos</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Aprobado">Aprobado</option>
                                <option value="Rechazado">Rechazado</option>
                                <option value="Convertido a Venta">Convertido a Venta</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha Desde</label>
                            <input type="date" class="form-control" id="filtroFechaDesde">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha Hasta</label>
                            <input type="date" class="form-control" id="filtroFechaHasta">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="btnFiltrar">
                                <i class="bi bi-funnel me-1"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Listado de cotizaciones -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Listado de Cotizaciones</h5>
                        <a href="CrearCotizacion.php" class="btn btn-light btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Nueva Cotización
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaCotizaciones">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Documento</th>
                                    <th class="text-end">Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoTabla">
                                <!-- Aquí se cargarán dinámicamente las cotizaciones -->
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div id="contadorRegistros">Mostrando 0 cotizaciones</div>
                        <nav>
                            <ul class="pagination pagination-sm" id="paginacion">
                                <!-- Paginación dinámica -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para editar cotización -->
    <div class="modal fade" id="editarCotizacionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Editar Cotización</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="formularioEdicion">
                    <!-- Formulario se cargará dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardarCambios">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalle -->
    <div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detalle de Cotización</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detalleCotizacion">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnImprimir">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Cargar cotizaciones al iniciar
            cargarCotizaciones();

            // Filtrar cotizaciones
            $('#btnFiltrar').click(cargarCotizaciones);

            // Ver detalle de cotización
            $(document).on('click', '.ver-detalle', function() {
                const id = $(this).data('id');
                verDetalleCotizacion(id);
            });

            // Reemplaza la función cargarCotizaciones con esta versión mejorada
            function cargarCotizaciones() {
                const estado = $('#filtroEstado').val();
                const fechaDesde = $('#filtroFechaDesde').val();
                const fechaHasta = $('#filtroFechaHasta').val();

                $.ajax({
                    url: '../Controllers/obtenerCotizaciones.php',
                    type: 'GET',
                    dataType: 'json', // Asegúrate de especificar el tipo de dato
                    data: {
                        estado: estado,
                        fechaDesde: fechaDesde,
                        fechaHasta: fechaHasta,
                        highlight: <?= $highlightId ? $highlightId : 'null' ?>
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#cuerpoTabla').html(response.html);
                            $('#contadorRegistros').text(`Mostrando ${response.total} cotización(es)`);

                            if (response.highlightId) {
                                const row = $(`tr[data-id="${response.highlightId}"]`);
                                row.addClass('highlight-row');
                                setTimeout(() => {
                                    row.removeClass('highlight-row');
                                }, 2000);
                            }
                        } else {
                            $('#contadorRegistros').text('Error al cargar cotizaciones');
                            console.error('Error:', response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#contadorRegistros').text('Error al conectar con el servidor');
                        console.error('AJAX Error:', status, error);
                    }
                });
            }

            function verDetalleCotizacion(id) {
                $.ajax({
                    url: '../Controllers/obtenerDetalleCotizacion.php',
                    type: 'GET',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        $('#detalleCotizacion').html(response);
                        $('#modalDetalle').modal('show');
                    }
                });
            }
            // Aprobar cotización
            $(document).on('click', '.aprobar-cotizacion', function() {
                const id = $(this).data('id');
                cambiarEstadoCotizacion(id, 'Aprobado');
            });

            // Rechazar cotización
            $(document).on('click', '.rechazar-cotizacion', function() {
                const id = $(this).data('id');
                cambiarEstadoCotizacion(id, 'Rechazado');
            });

            // Convertir a venta
            $(document).on('click', '.convertir-venta', function() {
                const id = $(this).data('id');
                convertirAVenta(id);
            });

            // Eliminar cotización
            let cotizacionAEliminar = null;
            $(document).on('click', '.eliminar-cotizacion', function() {
                cotizacionAEliminar = $(this).data('id');
                $('#confirmDeleteModal').modal('show');
            });

            $('#confirmDelete').click(function() {
                if (cotizacionAEliminar) {
                    eliminarCotizacion(cotizacionAEliminar);
                    $('#confirmDeleteModal').modal('hide');
                }
            });

            // Funciones auxiliares
            function cambiarEstadoCotizacion(id, estado) {
                $.ajax({
                    url: '../Controllers/cambiarEstadoCotizacion.php',
                    type: 'POST',
                    data: {
                        id: id,
                        estado: estado
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            mostrarMensaje('Estado actualizado correctamente', 'success');
                            cargarCotizaciones();
                        } else {
                            mostrarMensaje(response.error, 'danger');
                        }
                    }
                });
            }

            function convertirAVenta(id) {
                $.ajax({
                    url: '../Controllers/convertirVenta.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            mostrarMensaje('Cotización convertida a venta correctamente', 'success');
                            cargarCotizaciones();
                        } else {
                            mostrarMensaje(response.error, 'danger');
                        }
                    }
                });
            }

            function eliminarCotizacion(id) {
                $.ajax({
                    url: '../Controllers/eliminarCotizacion.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            mostrarMensaje('Cotización eliminada correctamente', 'success');
                            cargarCotizaciones();
                        } else {
                            mostrarMensaje(response.error, 'danger');
                        }
                    }
                });
            }

            function mostrarMensaje(texto, tipo) {
                const alerta = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                    ${texto}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
                $('.container').prepend(alerta);
            }
            // Manejar clic en editar cotización
            $(document).on('click', '.editar-cotizacion', function() {
                const id = $(this).data('id');
                cargarFormularioEdicion(id);
            });

            function cargarFormularioEdicion(id) {
                $.ajax({
                    url: '../Controllers/obtenerDetalleCotizacion.php',
                    type: 'GET',
                    data: {
                        id: id,
                        formato: 'edicion'
                    },
                    success: function(response) {
                        $('#formularioEdicion').html(response);
                        $('#editarCotizacionModal').modal('show');
                    }
                });
            }

            // Guardar cambios
            $('#guardarCambios').click(function() {
                const id = $('#editId').val();
                const datos = {
                    cliente: $('#editCliente').val(),
                    tipo_documento: $('#editTipoDocumento').val(),
                    numero_documento: $('#editNumeroDocumento').val(),
                    condiciones: $('#editCondiciones').val(),
                    forma_pago: $('#editFormaPago').val(),
                    validez: $('#editValidez').val(),
                    cuenta: $('#editCuenta').val(),
                    productos: obtenerProductosEditados()
                };

                $.ajax({
                    url: '../Controllers/editarCotizacion.php',
                    type: 'POST',
                    data: {
                        id: id,
                        datos: JSON.stringify(datos)
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            mostrarMensaje('Cotización actualizada correctamente', 'success');
                            $('#editarCotizacionModal').modal('hide');
                            cargarCotizaciones();
                        } else {
                            mostrarMensaje(response.error, 'danger');
                        }
                    }
                });
            });

            function obtenerProductosEditados() {
                const productos = [];
                $('.producto-editable').each(function() {
                    productos.push({
                        id: $(this).data('id'),
                        codigo: $(this).find('.edit-codigo').val(),
                        descripcion: $(this).find('.edit-descripcion').val(),
                        precio: $(this).find('.edit-precio').val(),
                        cantidad: $(this).find('.edit-cantidad').val(),
                        notas: $(this).find('.edit-notas').val()
                    });
                });
                return productos;
            }
        });
    </script>
</body>

</html>