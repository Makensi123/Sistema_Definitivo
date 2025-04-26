<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

include "Controllers/Conexion.php";

// Obtener cotizaciones
$query = "SELECT c.*, u.nombre as usuario_nombre 
          FROM cotizaciones c
          JOIN usuarios u ON c.usuario_id = u.id
          ORDER BY c.fecha DESC, c.hora DESC";
$result = $mysqli->query($query);

// Procesar eliminación
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    // Verificar permisos o si la cotización pertenece al usuario
    if ($_SESSION['user']['rol'] === 'ADMIN') {
        $stmt = $mysqli->prepare("DELETE FROM cotizaciones WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Cotización eliminada correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar la cotización";
        }

        header("Location: GestionCotizaciones.php");
        exit;
    }
}

// Procesar cambio de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estado'])) {
    $id = $_POST['id'];
    $estado = $_POST['estado'];

    $stmt = $mysqli->prepare("UPDATE cotizaciones SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $estado, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Estado de cotización actualizado";
    } else {
        $_SESSION['error'] = "Error al actualizar estado";
    }

    header("Location: GestionCotizaciones.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cotizaciones - Electrotop Perú</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Styles/GestionCotizaciones.css">
</head>

<body>
    <div class="container-fluid px-0">
        <!-- Barra superior -->
        <div class="top-bar bg-primary py-2 text-white">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="assets/logo-icono.png" alt="Icono Electrotop" class="me-2" style="height: 30px;">
                    <span class="fw-bold">GESTIÓN DE COTIZACIONES</span>
                </div>
                <div class="user-info">
                    <i class="bi bi-person-circle me-1"></i>
                    <?php echo $_SESSION['user']['nombre'] ?? 'Usuario'; ?>
                </div>
            </div>
        </div>

        <div class="container py-4">
            <!-- Mensajes de alerta -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error'];
                    unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success'];
                    unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Encabezado y botones -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Listado de Cotizaciones</h2>
                <div>
                    <a href="CrearCotizacion.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle-fill me-1"></i> Nueva Cotización
                    </a>
                    <button class="btn btn-outline-secondary" id="btnFiltrar">
                        <i class="bi bi-funnel-fill me-1"></i> Filtrar
                    </button>
                </div>
            </div>

            <!-- Panel de filtros (inicialmente oculto) -->
            <div class="card mb-4 filter-panel" style="display: none;">
                <div class="card-body">
                    <form method="get" class="row g-3">
                        <div class="col-md-3">
                            <label for="fecha_desde" class="form-label">Desde</label>
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_hasta" class="form-label">Hasta</label>
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta">
                        </div>
                        <div class="col-md-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="">Todos</option>
                                <option value="PENDIENTE">Pendiente</option>
                                <option value="APROBADA">Aprobada</option>
                                <option value="RECHAZADA">Rechazada</option>
                                <option value="FACTURADA">Facturada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="cliente" class="form-label">Cliente</label>
                            <input type="text" class="form-control" id="cliente" name="cliente" placeholder="Nombre del cliente">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btnLimpiarFiltros">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de cotizaciones -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="15%">Fecha</th>
                                    <th width="20%">Cliente</th>
                                    <th width="15%">Documento</th>
                                    <th width="10%">Total (S/)</th>
                                    <th width="10%">Estado</th>
                                    <th width="15%">Usuario</th>
                                    <th width="10%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($cotizacion = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo str_pad($cotizacion['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                            <td>
                                                <?php echo date('d/m/Y', strtotime($cotizacion['fecha'])); ?><br>
                                                <small class="text-muted"><?php echo substr($cotizacion['hora'], 0, 5); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($cotizacion['cliente']); ?></td>
                                            <td>
                                                <?php echo $cotizacion['tipo_documento']; ?>:<br>
                                                <?php echo $cotizacion['numero_documento']; ?>
                                            </td>
                                            <td class="text-end"><?php echo number_format($cotizacion['total'], 2); ?></td>
                                            <td>
                                                <span class="badge estado-<?php echo strtolower($cotizacion['estado']); ?>">
                                                    <?php echo $cotizacion['estado']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $cotizacion['usuario_nombre']; ?></td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                                        id="dropdownMenuButton<?php echo $cotizacion['id']; ?>"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-gear"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo $cotizacion['id']; ?>">
                                                        <li>
                                                            <a class="dropdown-item" href="VerCotizacion.php?id=<?php echo $cotizacion['id']; ?>">
                                                                <i class="bi bi-eye-fill me-2"></i>Ver
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="EditarCotizacion.php?id=<?php echo $cotizacion['id']; ?>">
                                                                <i class="bi bi-pencil-fill me-2"></i>Editar
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="GenerarPDF.php?id=<?php echo $cotizacion['id']; ?>" target="_blank">
                                                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>PDF
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form method="post" action="GestionCotizaciones.php" class="d-inline">
                                                                <input type="hidden" name="id" value="<?php echo $cotizacion['id']; ?>">
                                                                <input type="hidden" name="estado" value="APROBADA">
                                                                <button type="submit" name="cambiar_estado" class="dropdown-item">
                                                                    <i class="bi bi-check-circle-fill me-2 text-success"></i>Aprobar
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="post" action="GestionCotizaciones.php" class="d-inline">
                                                                <input type="hidden" name="id" value="<?php echo $cotizacion['id']; ?>">
                                                                <input type="hidden" name="estado" value="RECHAZADA">
                                                                <button type="submit" name="cambiar_estado" class="dropdown-item">
                                                                    <i class="bi bi-x-circle-fill me-2 text-danger"></i>Rechazar
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <?php if ($_SESSION['user']['rol'] === 'ADMIN'): ?>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="GestionCotizaciones.php?eliminar=<?php echo $cotizacion['id']; ?>"
                                                                    onclick="return confirm('¿Está seguro de eliminar esta cotización?');">
                                                                    <i class="bi bi-trash-fill me-2"></i>Eliminar
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="bi bi-file-earmark-excel" style="font-size: 2.5rem; color: #6c757d;"></i>
                                                <p class="mt-3">No se encontraron cotizaciones</p>
                                                <a href="CrearCotizacion.php" class="btn btn-primary mt-2">
                                                    <i class="bi bi-plus-circle-fill me-1"></i> Crear primera cotización
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Paginación -->
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar/ocultar panel de filtros
        document.getElementById('btnFiltrar').addEventListener('click', function() {
            const panel = document.querySelector('.filter-panel');
            if (panel.style.display === 'none') {
                panel.style.display = 'block';
                this.innerHTML = '<i class="bi bi-funnel me-1"></i> Ocultar filtros';
            } else {
                panel.style.display = 'none';
                this.innerHTML = '<i class="bi bi-funnel-fill me-1"></i> Filtrar';
            }
        });

        // Limpiar filtros
        document.getElementById('btnLimpiarFiltros')?.addEventListener('click', function() {
            document.querySelectorAll('.filter-panel input').forEach(input => {
                input.value = '';
            });
            document.querySelector('.filter-panel select').value = '';
        });
    </script>
</body>

</html>