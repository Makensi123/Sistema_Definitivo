<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

include "../Controllers/Conexion.php";
include "../Controllers/Productos.php";

// Obtener nombre de usuario logueado
$usuario = $_SESSION['user'] ?? 'Administrador';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos | Electrotop Perú</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="../Styles//GestionProductos.css">
</head>

<body>
    <!-- Loading overlay (se muestra con JavaScript) -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="spinner"></div>
        <h4 class="text-electro-blue mt-3">Procesando...</h4>
    </div>

    <!-- Notificación flotante (se muestra con JavaScript) -->
    <div id="notification" class="floating-alert" style="display: none;">
        <div class="alert alert-success alert-dismissible fade show shadow-lg" role="alert">
            <strong><i class="bi bi-check-circle-fill me-2"></i>Éxito!</strong> <span id="notificationMessage">Operación completada correctamente</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>

    <div class="dashboard-container animate__animated animate__fadeIn">
        <div class="header-section">
            <img src="../assets/logo1-original.png" alt="Electrotop Perú" class="company-logo hover-grow">
            <h2 class="text-electro-blue mb-2 fw-bold">Gestión de Productos</h2>
            <p class="text-muted">Administra el catálogo de productos de Electrotop Perú</p>
        </div>

        <!-- Card de gestión -->
        <div class="card mb-4 border-0 hover-float">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h5 class="mb-0 text-white fw-semibold"><i class="bi bi-box-seam me-2"></i>Inventario de Productos</h5>
                <div class="d-flex mt-3 mt-md-0">
                    <div class="search-box me-3">
                        <i class="bi bi-search"></i>
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar productos...">
                    </div>
                    <button class="btn btn-electro animate__animated animate__pulse animate__infinite" data-bs-toggle="modal" data-bs-target="#productoModal">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Código</th>
                                <th>Descripción</th>
                                <th class="text-end pe-4">Precio</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="productosTable">
                            <?php if (count($productos) > 0): ?>
                                <?php foreach ($productos as $producto): ?>
                                    <tr class="animate__animated animate__fadeIn">
                                        <td class="ps-4">
                                            <span class="badge badge-electro">
                                                <i class="bi bi-upc-scan me-1"></i><?= htmlspecialchars($producto['codigo']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($producto['descripcion']) ?></td>
                                        <td class="price-cell text-end pe-4"><?= number_format($producto['precio'], 2) ?></td>
                                        <td class="text-center">
                                            <button class="action-btn edit-btn me-2 edit-btn"
                                                data-id="<?= $producto['id'] ?>"
                                                data-codigo="<?= htmlspecialchars($producto['codigo']) ?>"
                                                data-descripcion="<?= htmlspecialchars($producto['descripcion']) ?>"
                                                data-precio="<?= $producto['precio'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="action-btn delete-btn delete-btn"
                                                data-id="<?= $producto['id'] ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <button class="action-btn detail-btn info-btn"
                                                data-id="<?= $producto['id'] ?>"
                                                data-codigo="<?= htmlspecialchars($producto['codigo']) ?>"
                                                data-descripcion="<?= htmlspecialchars($producto['descripcion']) ?>"
                                                data-precio="<?= number_format($producto['precio'], 2) ?>"
                                                title="Ver detalles">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center empty-state py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-box text-electro-blue"></i>
                                            <h4 class="mt-3 text-electro-blue fw-bold">No hay productos registrados</h4>
                                            <p class="text-muted mb-4">Comienza agregando tu primer producto al inventario</p>
                                            <button class="btn btn-electro animate__animated animate__pulse animate__infinite" data-bs-toggle="modal" data-bs-target="#productoModal">
                                                <i class="bi bi-plus-lg me-1"></i> Agregar Producto
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if (count($productos) > 0): ?>
                <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Mostrando <strong><?= count($productos) ?></strong> productos
                    </div>
                    <div class="d-flex">
                        <button class="btn btn-outline-electro me-2" id="exportBtn">
                            <i class="bi bi-download me-1"></i> Exportar
                        </button>
                        <a href="../interfaces/dashboard.php" class="btn btn-back">
                            <i class="bi bi-arrow-left me-1"></i> Volver al Dashboard
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para agregar/editar producto -->
    <div class="modal fade" id="productoModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content hover-grow">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"><i class="bi bi-box-seam me-2"></i> Nuevo Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="productoForm" method="post" class="needs-validation" novalidate>
                    <div class="modal-body">
                        <input type="hidden" name="accion" id="accion" value="agregar">
                        <input type="hidden" name="id" id="productoId">

                        <div class="mb-4">
                            <label for="codigo" class="form-label fw-semibold">Código del Producto</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                <input type="text" class="form-control" id="codigo" name="codigo" placeholder="EJ: PROD-001" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese un código válido
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Descripción detallada del producto..." required></textarea>
                            <div class="invalid-feedback">
                                Por favor ingrese una descripción
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="precio" class="form-label fw-semibold">Precio Unitario</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="precio" name="precio" placeholder="0.00" required>
                                <div class="invalid-feedback">
                                    Precio debe ser mayor a 0
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-electro">
                            <i class="bi bi-check-lg me-1"></i> Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content hover-grow">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-column align-items-center text-center">
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle mb-3 animate__animated animate__pulse">
                            <i class="bi bi-exclamation-octagon-fill text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <h4 class="fw-bold">¿Eliminar este producto?</h4>
                        <p class="text-muted">Esta acción no se puede deshacer. ¿Estás seguro de continuar?</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </button>
                    <form id="deleteForm" method="post" style="display: inline;">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Sí, Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de detalles del producto -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content hover-grow">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-info-circle-fill me-2"></i>Detalles del Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <div class="bg-electro-light p-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                                <i class="bi bi-box-seam text-electro-blue" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h4 id="detailCodigo" class="fw-bold text-electro-blue mb-2"></h4>
                            <p id="detailDescripcion" class="text-muted"></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <h5 id="detailPrecio" class="fw-bold mb-0"></h5>
                                    <small class="text-muted">Precio unitario</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <h6 class="fw-bold mb-3"><i class="bi bi-person-circle me-2"></i>Información de Registro</h6>
                        <div class="list-group">
                            <div class="list-group-item border-0 py-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold">Registrado por</span>
                                    <span id="detailUsuario" class="text-muted"><?= htmlspecialchars($usuario) ?></span>
                                </div>
                            </div>
                            <div class="list-group-item border-0 py-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold">Fecha de registro</span>
                                    <span id="detailFecha" class="text-muted"><?= date('d/m/Y H:i') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-electro" data-bs-dismiss="modal">
                        <i class="bi bi-check-lg me-1"></i> Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="../Js/GestionProductos.js"></script>