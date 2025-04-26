<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

include "Controllers/Conexion.php";
include "Productos.php"
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos | Electrotop Perú</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0056b3;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --electro-blue: #1a4b8c;
            --electro-light: #e6f0ff;
            --electro-accent: #4a90e2;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        
        .header-section {
            text-align: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--electro-accent);
            position: relative;
        }
        
        .header-section::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--electro-accent);
            border-radius: 2px;
        }
        
        .company-logo {
            height: 80px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .company-logo:hover {
            transform: scale(1.05) rotate(-2deg);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--electro-blue), var(--electro-accent));
            color: white;
            border-radius: 8px 8px 0 0 !important;
        }
        
        .table-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .table thead {
            background: linear-gradient(135deg, var(--electro-blue), var(--electro-accent));
            color: white;
        }
        
        .table th {
            font-weight: 500;
            padding: 1rem;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:nth-child(even) {
            background-color: rgba(230, 240, 255, 0.3);
        }
        
        .table tbody tr:hover {
            background-color: var(--electro-light);
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }
        
        .btn-electro {
            background: var(--electro-blue);
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-electro:hover {
            background: var(--electro-accent);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(26, 75, 140, 0.2);
        }
        
        .btn-back {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-back:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.2);
        }
        
        .badge-electro {
            background: var(--electro-light);
            color: var(--electro-blue);
            font-weight: 500;
            padding: 0.35rem 0.75rem;
        }
        
        .search-box {
            position: relative;
            max-width: 400px;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary);
        }
        
        .search-box input {
            padding-left: 45px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            border-color: var(--electro-accent);
            box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25);
        }
        
        .action-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
        
        .edit-btn {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }
        
        .edit-btn:hover {
            background: var(--success);
            color: white;
        }
        
        .delete-btn {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }
        
        .delete-btn:hover {
            background: var(--danger);
            color: white;
        }
        
        .price-cell {
            font-weight: 600;
            color: var(--electro-blue);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--electro-blue), var(--electro-accent));
            color: white;
            border-radius: 0;
        }
        
        .modal-title {
            font-weight: 500;
        }
        
        .form-control, .form-select {
            border-radius: 6px;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--electro-accent);
            box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25);
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .table-responsive {
                border: none;
            }
        }
        
        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .dashboard-container {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header-section">
            <img src="assets/logo1-original.png" alt="Electrotop Perú" class="company-logo">
            <h2 class="text-electro-blue mb-2">Gestión de Productos</h2>
            <p class="text-muted">Administra el catálogo de productos de Electrotop Perú</p>
        </div>
        
        <!-- Card de gestión -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center py-3">
                <h5 class="mb-0 text-white"><i class="bi bi-box-seam me-2"></i>Inventario de Productos</h5>
                <div class="d-flex mt-3 mt-md-0">
                    <div class="search-box me-3">
                        <i class="bi bi-search"></i>
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar productos...">
                    </div>
                    <button class="btn btn-electro" data-bs-toggle="modal" data-bs-target="#productoModal">
                        <i class="bi bi-plus-lg"></i> Nuevo Producto
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
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge badge-electro">
                                                <?= htmlspecialchars($producto['codigo']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($producto['descripcion']) ?></td>
                                        <td class="price-cell text-end pe-4">S/ <?= number_format($producto['precio'], 2) ?></td>
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
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                                            <h5 class="mt-3 text-muted">No hay productos registrados</h5>
                                            <p class="text-muted">Comienza agregando tu primer producto</p>
                                            <button class="btn btn-electro mt-2" data-bs-toggle="modal" data-bs-target="#productoModal">
                                                <i class="bi bi-plus-lg"></i> Agregar Producto
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
                <div>
                    <a href="dashboard.php" class="btn btn-back">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Modal para agregar/editar producto -->
    <div class="modal fade" id="productoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"><i class="bi bi-box-seam me-2"></i> Nuevo Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="productoForm" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="accion" id="accion" value="agregar">
                        <input type="hidden" name="id" id="productoId">
                        
                        <div class="mb-4">
                            <label for="codigo" class="form-label">Código del Producto</label>
                            <input type="text" class="form-control" id="codigo" name="codigo" placeholder="EJ: PROD-001" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Descripción detallada del producto..." required></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="precio" class="form-label">Precio Unitario</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" step="0.01" class="form-control" id="precio" name="precio" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-electro">
                            <i class="bi bi-check-lg"></i> Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-column align-items-center text-center">
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle mb-3">
                            <i class="bi bi-exclamation-octagon text-danger" style="font-size: 2rem;"></i>
                        </div>
                        <h5>¿Eliminar este producto?</h5>
                        <p class="text-muted">Esta acción no se puede deshacer. ¿Estás seguro de continuar?</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteForm" method="post" style="display: inline;">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Sí, Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Búsqueda en tiempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#productosTable tr').forEach(row => {
                if (row.querySelector('td')) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                }
            });
        });
        
        // Configurar modal para editar
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
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
        
        // Configurar modal para eliminar
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('deleteId').value = this.dataset.id;
                new bootstrap.Modal(document.getElementById('confirmModal')).show();
            });
        });
        
        // Limpiar modal al cerrar
        document.getElementById('productoModal').addEventListener('hidden.bs.modal', function() {
            this.querySelector('form').reset();
            document.getElementById('modalTitle').innerHTML = '<i class="bi bi-box-seam me-2"></i> Nuevo Producto';
            document.getElementById('accion').value = 'agregar';
        });
        
        // Validación de formulario
        document.getElementById('productoForm').addEventListener('submit', function(e) {
            const precio = parseFloat(document.getElementById('precio').value);
            if (isNaN(precio) || precio <= 0) {
                e.preventDefault();
                alert('Por favor ingrese un precio válido mayor a cero');
                document.getElementById('precio').focus();
            }
        });
    </script>
</body>
</html>