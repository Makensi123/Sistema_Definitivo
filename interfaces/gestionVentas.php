<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
include "../Controllers/Conexion.php";

// Mostrar mensajes
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

$highlightId = $_GET['highlight'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ventas - Electrotop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .venta-row { 
            cursor: pointer; 
        }
        .venta-row:hover { 
            opacity: 0.8; 
        }
        .highlight-row { 
            animation: highlight 2s; 
        }
        @keyframes highlight {
            0% { background-color: #d4edda; }
            100% { background-color: transparent; }
        }
        .action-buttons .btn { 
            margin-right: 5px; 
        }
        .action-buttons .btn:last-child { 
            margin-right: 0; 
        }
        .estado-completado {
            background-color: #d4edda;
        }
        .estado-pendiente {
            background-color: #fff3cd;
        }
        .estado-anulado {
            background-color: #f8d7da;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Barra superior -->
        <div class="top-bar bg-primary py-2 text-white mb-4">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="../assets/logo1-original.jpg" alt="Logo" style="height: 30px;" class="me-2">
                    <h5 class="mb-0">Gestión de Ventas</h5>
                </div>
                <div>
                    <span class="me-3"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user']) ?></span>
                    <a href="dashboard.php" class="btn btn-sm btn-light">Volver al Panel</a>
                </div>
            </div>
        </div>

        <!-- Modal para confirmar eliminación -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Está seguro que desea anular esta venta? Esta acción no se puede deshacer.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">Anular</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Mostrar mensajes -->
            <?php if($mensaje): ?>
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
                                <option value="COMPLETADO">Completado</option>
                                <option value="PENDIENTE">Pendiente</option>
                                <option value="ANULADO">Anulado</option>
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

            <!-- Listado de ventas -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Listado de Ventas</h5>
                        <a href="nueva_venta.php" class="btn btn-light btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Nueva Venta
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaVentas">
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
                                <!-- Aquí se cargarán dinámicamente las ventas -->
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div id="contadorRegistros">Mostrando 0 ventas</div>
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

    <!-- Modal para ver detalle -->
    <div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detalle de Venta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detalleVenta">
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

    <!-- Modal para editar venta -->
    <div class="modal fade" id="editarVentaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Editar Venta</h5>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Cargar ventas al iniciar
            cargarVentas();

            // Filtrar ventas
            $('#btnFiltrar').click(cargarVentas);

            // Ver detalle de venta
            $(document).on('click', '.ver-detalle', function() {
                const id = $(this).data('id');
                verDetalleVenta(id);
            });

            // Editar venta
            $(document).on('click', '.editar-venta', function() {
                const id = $(this).data('id');
                cargarFormularioEdicion(id);
            });

            // Anular venta
            let ventaAAnular = null;
            $(document).on('click', '.anular-venta', function() {
                ventaAAnular = $(this).data('id');
                $('#confirmDeleteModal').modal('show');
            });

            $('#confirmDelete').click(function() {
                if (ventaAAnular) {
                    cambiarEstadoVenta(ventaAAnular, 'ANULADO');
                    $('#confirmDeleteModal').modal('hide');
                }
            });

            // Completar venta
            $(document).on('click', '.completar-venta', function() {
                const id = $(this).data('id');
                cambiarEstadoVenta(id, 'COMPLETADO');
            });

            // Guardar cambios en edición
            $('#guardarCambios').click(function() {
                const id = $('#editId').val();
                const datos = {
                    cliente: $('#editCliente').val(),
                    tipo_documento: $('#editTipoDocumento').val(),
                    numero_documento: $('#editNumeroDocumento').val(),
                    metodo_pago: $('#editMetodoPago').val(),
                    observaciones: $('#editObservaciones').val(),
                    productos: obtenerProductosEditados()
                };

                $.ajax({
                    url: '../Controllers/editarVenta.php',
                    type: 'POST',
                    data: { id: id, datos: JSON.stringify(datos) },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            mostrarMensaje('Venta actualizada correctamente', 'success');
                            $('#editarVentaModal').modal('hide');
                            cargarVentas();
                        } else {
                            mostrarMensaje(response.error, 'danger');
                        }
                    }
                });
            });

            // Funciones auxiliares
            function cargarVentas() {
                const estado = $('#filtroEstado').val();
                const fechaDesde = $('#filtroFechaDesde').val();
                const fechaHasta = $('#filtroFechaHasta').val();

                $.ajax({
                    url: '../Controllers/obtenerVentas.php',
                    type: 'GET',
                    data: {
                        estado: estado,
                        fechaDesde: fechaDesde,
                        fechaHasta: fechaHasta,
                        highlight: <?= $highlightId ? $highlightId : 'null' ?>
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#cuerpoTabla').html(response.html);
                            $('#contadorRegistros').text(`Mostrando ${response.total} venta(s)`);
                            
                            if (response.highlightId) {
                                const row = $(`tr[data-id="${response.highlightId}"]`);
                                row.addClass('highlight-row');
                                setTimeout(() => {
                                    row.removeClass('highlight-row');
                                }, 2000);
                            }
                        } else {
                            mostrarMensaje(response.error, 'danger');
                        }
                    },
                    error: function() {
                        mostrarMensaje('Error al cargar ventas', 'danger');
                    }
                });
            }

            function verDetalleVenta(id) {
                $.ajax({
                    url: '../Controllers/obtenerDetalleVenta.php',
                    type: 'GET',
                    data: { id: id },
                    success: function(response) {
                        $('#detalleVenta').html(response);
                        $('#modalDetalle').modal('show');
                    }
                });
            }

            function cargarFormularioEdicion(id) {
                $.ajax({
                    url: '../Controllers/obtenerDetalleVenta.php',
                    type: 'GET',
                    data: { id: id, formato: 'edicion' },
                    success: function(response) {
                        $('#formularioEdicion').html(response);
                        $('#editarVentaModal').modal('show');
                    }
                });
            }

            function cambiarEstadoVenta(id, estado) {
                $.ajax({
                    url: '../Controllers/cambiarEstadoVenta.php',
                    type: 'POST',
                    data: { id: id, estado: estado },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            mostrarMensaje(`Venta ${estado.toLowerCase()} correctamente`, 'success');
                            cargarVentas();
                        } else {
                            mostrarMensaje(response.error, 'danger');
                        }
                    }
                });
            }

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

            function mostrarMensaje(texto, tipo) {
                const alerta = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                    ${texto}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
                $('.container').prepend(alerta);
                setTimeout(() => {
                    $('.alert').alert('close');
                }, 5000);
            }
        });
    </script>
</body>
</html>