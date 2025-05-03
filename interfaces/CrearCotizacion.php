<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
include "../Controllers/Conexion.php";
$usuario = $_SESSION['user'];
date_default_timezone_set('America/Lima');
$fecha_actual = date('Y-m-d');
$hora_actual = date('H:i');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Cotización - Electrotop Perú</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="../Styles/CrearCotizacion.css">
</head>

<body>
    <div class="container-fluid px-0">
        <!-- Barra superior azul -->
        <div class="top-bar bg-primary py-2 text-white">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="../assets/logo1-original.png" alt="Icono Electrotop" class="me-2" style="height: 30px;">
                    <span class="fw-bold">SISTEMA DE COTIZACIONES</span>
                </div>
                <div class="user-info">
                    <i class="bi bi-person-circle me-1"></i>
                    <?php echo htmlspecialchars($usuario); ?>
                </div>
            </div>
        </div>

        <div class="container py-4">
            <form action="../Controllers/guardarCotizacion.php" method="post" id="cotizacionForm">
                <div class="cotizacion-container" id="cotizacionContent">
                    <!-- Encabezado de la empresa con nuevo diseño -->
                    <div class="header-section position-relative">
                        <div class="company-brand">
                            <img src="../assets/logo1-original.png" alt="Electrotop Perú" class="company-logo">
                            <div class="company-text">
                                <div class="company-name">ELECTROTOP PERÚ S.A.C.</div>
                                <div class="company-slogan">Soluciones Integrales en Seguridad y Telecomunicaciones</div>
                            </div>
                        </div>

                        <div class="company-details">
                            <div class="detail-item">
                                <i class="bi bi-geo-alt-fill"></i>
                                <div>
                                    <label>DIRECCIÓN</label>
                                    <div>Calle Nueva 209 Int.201 C.C. Los Portales</div>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="bi bi-envelope-fill"></i>
                                <div>
                                    <label>CORREO</label>
                                    <div>ventas@electrotopperu.com</div>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="bi bi-file-text-fill"></i>
                                <div>
                                    <label>RUC</label>
                                    <div>20559256405</div>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="bi bi-phone-fill"></i>
                                <div>
                                    <label>TELÉFONO</label>
                                    <div>953760540 / 955696775</div>
                                </div>
                            </div>
                        </div>

                        <div class="document-type-badge">
                            <span id="documentTypeBadge">COTIZACIÓN</span>
                        </div>
                    </div>

                    <!-- Información del cliente con nuevo diseño -->
                    <div class="client-info-section">
                        <h5 class="section-title"><i class="bi bi-person-lines-fill me-2"></i>INFORMACIÓN DEL CLIENTE</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea class="form-control auto-expand" id="cliente" name="cliente"
                                        placeholder="Nombre del cliente" required style="height: 80px"></textarea>
                                    <label for="cliente">CLIENTE</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="fecha" name="fecha"
                                        value="<?php echo $fecha_actual; ?>" required>
                                    <label for="fecha">FECHA</label>
                                </div>
                                <input type="hidden" id="hora" name="hora" value="<?php echo $hora_actual; ?>">
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>
                                        <option value="CE">Carnet Extranjería</option>
                                    </select>
                                    <label for="tipo_documento">TIPO DOCUMENTO</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea class="form-control auto-expand" id="numero_documento"
                                        name="numero_documento" placeholder="Número de documento" required></textarea>
                                    <label for="numero_documento">NÚMERO DOCUMENTO</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="tipo_operacion" name="tipo_operacion" required>
                                        <option value="COTIZACION">COTIZACIÓN</option>
                                        <option value="VENTA">VENTA</option>
                                        <option value="PROFORMA">PROFORMA</option>
                                    </select>
                                    <label for="tipo_operacion">TIPO OPERACIÓN</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de productos mejorada -->
                    <div class="products-section">
                        <div class="section-header">
                            <h5 class="section-title"><i class="bi bi-cart-plus-fill me-2"></i>PRODUCTOS/SERVICIOS</h5>
                            <button type="button" class="btn btn-add-product" id="btnAddProduct">
                                <i class="bi bi-plus-circle-fill me-1"></i> Agregar Producto
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table products-table">
                                <thead class="table-primary">
                                    <tr>
                                        <th width="5%">ITEM</th>
                                        <th width="15%">CÓDIGO</th>
                                        <th width="35%">DESCRIPCIÓN</th>
                                        <th width="12%">P. UNIT.</th>
                                        <th width="8%">CANT.</th>
                                        <th width="15%">P. TOTAL</th>
                                        <th width="10%">ACCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody id="productosBody">
                                    <tr class="empty-row">
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <i class="bi bi-box-seam"></i>
                                                <p>No hay productos agregados</p>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddFirstProduct">
                                                    <i class="bi bi-plus-circle me-1"></i> Agregar primer producto
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Totales con nuevo diseño -->
                    <div class="totals-section">
                        <div class="total-display">
                            <span class="total-label">TOTAL:</span>
                            <span class="total-currency">S/</span>
                            <input type="text" class="total-amount" id="total" name="total" value="0.00" readonly>
                        </div>
                    </div>

                    <!-- Condiciones de pago mejoradas -->
                    <div class="conditions-section">
                        <h5 class="section-title"><i class="bi bi-file-text-fill me-2"></i>CONDICIONES COMERCIALES</h5>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="form-floating">
                                    <textarea class="form-control auto-expand" id="condiciones" name="condiciones"
                                        placeholder="Condiciones comerciales" style="height: 80px">Precios incluyen IGV. Tiempo de entrega: 5 días hábiles. Garantía de 1 año.</textarea>
                                    <label for="condiciones">CONDICIONES COMERCIALES</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="forma_pago" name="forma_pago">
                                        <option value="CONTADO">CONTADO</option>
                                        <option value="CREDITO_15D">CRÉDITO 15 DÍAS</option>
                                        <option value="CREDITO_30D">CRÉDITO 30 DÍAS</option>
                                        <option value="TARJETA_CREDITO">TARJETA DE CRÉDITO</option>
                                        <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                    </select>
                                    <label for="forma_pago">FORMA DE PAGO</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="validez" name="validez" value="15" min="1">
                                    <label for="validez">VALIDEZ (días)</label>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-floating">
                                    <textarea class="form-control auto-expand" id="cuenta" name="cuenta"
                                        placeholder="Cuenta bancaria" style="height: 80px">300-3003687630 CTA. CTE. INTERBANK</textarea>
                                    <label for="cuenta">CUENTA BANCARIA</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción mejorados -->
                    <div class="action-buttons no-print">
                        <a href="dashboard.php" class="btn btn-back">
                            <i class="bi bi-arrow-left-circle-fill me-1"></i> Volver al Panel
                        </a>
                        <div class="action-group">
                            <button type="submit" class="btn btn-save">
                                <i class="bi bi-save-fill me-1"></i> Guardar Cotización
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de búsqueda de productos mejorado -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-search me-2"></i>BUSCAR PRODUCTO</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="search-section mb-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" class="form-control" id="searchProduct" placeholder="Ingrese código o nombre del producto">
                            <button class="btn btn-primary" id="btnSearchProduct">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                        </div>
                        <div class="search-tips mt-2">
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Puede buscar por código, nombre o descripción del producto</small>
                        </div>
                    </div>

                    <div class="results-section">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="productSearchResults">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Código</th>
                                        <th width="45%">Descripción</th>
                                        <th width="15%">Precio (S/)</th>
                                        <th width="15%">Stock</th>
                                        <th width="10%">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="no-results">
                                        <td colspan="5">
                                            <div class="text-center py-4">
                                                <i class="bi bi-search" style="font-size: 2rem; color: #6c757d;"></i>
                                                <p class="mt-2">Ingrese un término de búsqueda para encontrar productos</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="selected-product-section mt-4" id="selectedProductDetails" style="display: none;">
                        <h5 class="border-bottom pb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Producto Seleccionado</h5>
                        <form id="productForm" class="row g-3">
                            <input type="hidden" id="producto_id">
                            <div class="col-md-6">
                                <label for="producto_codigo" class="form-label">Código</label>
                                <input type="text" class="form-control" id="producto_codigo" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="producto_cantidad" class="form-label">Cantidad</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" id="decrementQty">-</button>
                                    <input type="number" class="form-control text-center" id="producto_cantidad" value="1" min="1">
                                    <button class="btn btn-outline-secondary" type="button" id="incrementQty">+</button>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="producto_descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="producto_descripcion" rows="2" readonly></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="producto_precio" class="form-label">Precio Unitario (S/)</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" step="0.01" class="form-control" id="producto_precio" min="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Producto</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="text" class="form-control fw-bold" id="producto_total" value="0.00" readonly>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" id="producto_notas" placeholder="Notas adicionales" style="height: 80px"></textarea>
                                    <label for="producto_notas">Notas adicionales (opcional)</label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnAddToQuote" disabled>
                        <i class="bi bi-plus-circle-fill me-1"></i> Agregar a Cotización
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variables globales
        let productosCotizacion = [];
        let itemCounter = 1;
        let currentEditIndex = null;

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar eventos
            setupEventListeners();

            // Actualizar tipo de documento visual
            document.getElementById('tipo_operacion').addEventListener('change', function() {
                document.getElementById('documentTypeBadge').textContent = this.value;
            });
        });

        function setupEventListeners() {
            // Botón para agregar primer producto
            document.getElementById('btnAddFirstProduct')?.addEventListener('click', showProductModal);

            // Botón para agregar producto
            document.getElementById('btnAddProduct').addEventListener('click', showProductModal);

            // Búsqueda de productos
            document.getElementById('searchProduct').addEventListener('input', searchProducts);
            document.getElementById('btnSearchProduct').addEventListener('click', searchProducts);

            // Control de cantidad
            document.getElementById('incrementQty')?.addEventListener('click', function() {
                const input = document.getElementById('producto_cantidad');
                input.value = parseInt(input.value) + 1;
                calcularTotalProducto();
            });

            document.getElementById('decrementQty')?.addEventListener('click', function() {
                const input = document.getElementById('producto_cantidad');
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                    calcularTotalProducto();
                }
            });

            // Cálculos de producto
            document.getElementById('producto_cantidad').addEventListener('input', calcularTotalProducto);
            document.getElementById('producto_precio').addEventListener('input', calcularTotalProducto);

            // Agregar producto a cotización
            document.getElementById('btnAddToQuote').addEventListener('click', addProductToQuote);

            // Autoajuste de textareas
            document.querySelectorAll('.auto-expand').forEach(textarea => {
                textarea.addEventListener('input', autoExpandTextarea);
                // Disparar el evento input para ajustar inicialmente
                const event = new Event('input');
                textarea.dispatchEvent(event);
            });

            // Validación antes de enviar
            document.getElementById('cotizacionForm').addEventListener('submit', function(e) {
                if (productosCotizacion.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cotización vacía',
                        text: 'Debe agregar al menos un producto para guardar la cotización',
                        confirmButtonColor: '#1a4b8c'
                    });
                    return;
                }

                // Validar campos obligatorios
                const cliente = document.getElementById('cliente').value.trim();
                const numeroDocumento = document.getElementById('numero_documento').value.trim();

                if (cliente === '' || numeroDocumento === '') {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Campos incompletos',
                        text: 'Por favor complete todos los campos obligatorios',
                        confirmButtonColor: '#1a4b8c'
                    });
                    return;
                }

                // Crear input oculto con los productos en formato JSON
                const productosInput = document.createElement('input');
                productosInput.type = 'hidden';
                productosInput.name = 'productos_json';
                productosInput.value = JSON.stringify(productosCotizacion);
                this.appendChild(productosInput);
            });
        }

        function autoExpandTextarea() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        }

        function showProductModal() {
            // Limpiar búsqueda previa
            document.getElementById('searchProduct').value = '';
            document.getElementById('productSearchResults').querySelector('tbody').innerHTML =
                '<tr class="no-results"><td colspan="5"><div class="text-center py-4"><i class="bi bi-search" style="font-size: 2rem; color: #6c757d;"></i><p class="mt-2">Ingrese un término de búsqueda para encontrar productos</p></div></td></tr>';
            document.getElementById('selectedProductDetails').style.display = 'none';
            document.getElementById('btnAddToQuote').disabled = true;
            document.getElementById('btnAddToQuote').innerHTML = '<i class="bi bi-plus-circle-fill me-1"></i> Agregar a Cotización';
            currentEditIndex = null;

            var modal = new bootstrap.Modal(document.getElementById('productModal'));
            modal.show();
        }

        function searchProducts() {
            const searchTerm = this.value?.trim() || document.getElementById('searchProduct').value.trim();
            const tbody = document.getElementById('productSearchResults').querySelector('tbody');

            if (searchTerm.length === 0) {
                tbody.innerHTML = '<tr class="no-results"><td colspan="5"><div class="text-center py-4"><i class="bi bi-search" style="font-size: 2rem; color: #6c757d;"></i><p class="mt-2">Ingrese un término de búsqueda para encontrar productos</p></div></td></tr>';
                return;
            }

            // Mostrar carga
            tbody.innerHTML = '<tr class="no-results"><td colspan="5"><div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2">Buscando productos...</p></div></td></tr>';

            fetch(`../Controllers/buscarProductos.php?termino=${encodeURIComponent(searchTerm)}`)
                .then(response => {
                    if (!response.ok) throw new Error('Error en la red');
                    return response.json();
                })
                .then(data => {
                    tbody.innerHTML = '';

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr class="no-results"><td colspan="5"><div class="text-center py-4"><i class="bi bi-exclamation-circle" style="font-size: 2rem; color: #6c757d;"></i><p class="mt-2">No se encontraron productos</p></div></td></tr>';
                        return;
                    }

                    data.forEach(producto => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                        <td>${producto.codigo}</td>
                        <td>${producto.descripcion}</td>
                        <td class="text-end">S/ ${parseFloat(producto.precio).toFixed(2)}</td>
                        <td class="text-center">${producto.stock || 'N/A'}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary btn-select-product"
                                data-id="${producto.id}"
                                data-codigo="${producto.codigo}"
                                data-descripcion="${producto.descripcion}"
                                data-precio="${producto.precio}">
                                <i class="bi bi-plus"></i> Seleccionar
                            </button>
                        </td>
                    `;
                        tbody.appendChild(tr);
                    });

                    // Agregar eventos a los botones de selección
                    document.querySelectorAll('.btn-select-product').forEach(btn => {
                        btn.addEventListener('click', function() {
                            selectProduct(
                                this.dataset.id,
                                this.dataset.codigo,
                                this.dataset.descripcion,
                                this.dataset.precio
                            );
                        });
                    });
                })
                .catch(error => {
                    tbody.innerHTML = '<tr class="no-results"><td colspan="5"><div class="text-center py-4"><i class="bi bi-exclamation-triangle" style="font-size: 2rem; color: #dc3545;"></i><p class="mt-2">Error al buscar productos</p></div></td></tr>';
                    console.error('Error:', error);
                });
        }

        function selectProduct(id, codigo, descripcion, precio) {
            // Llena los campos del modal con el producto seleccionado
            document.getElementById('producto_id').value = id;
            document.getElementById('producto_codigo').value = codigo;
            document.getElementById('producto_descripcion').value = descripcion;
            document.getElementById('producto_precio').value = parseFloat(precio).toFixed(2);
            document.getElementById('producto_cantidad').value = 1;
            document.getElementById('producto_total').value = parseFloat(precio).toFixed(2);

            document.getElementById('selectedProductDetails').style.display = 'block';
            document.getElementById('btnAddToQuote').disabled = false;

            // Enfocar el campo de cantidad
            document.getElementById('producto_cantidad').focus();
        }

        function calcularTotalProducto() {
            const cantidad = parseInt(document.getElementById('producto_cantidad').value) || 0;
            const precio = parseFloat(document.getElementById('producto_precio').value) || 0;
            const total = cantidad * precio;
            document.getElementById('producto_total').value = total.toFixed(2);
        }

        function addProductToQuote() {
            const cantidad = parseInt(document.getElementById('producto_cantidad').value);
            const precio = parseFloat(document.getElementById('producto_precio').value);

            if (isNaN(cantidad) || cantidad < 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Cantidad inválida',
                    text: 'Por favor ingrese una cantidad válida (mínimo 1)',
                    confirmButtonColor: '#1a4b8c'
                });
                return;
            }

            if (isNaN(precio) || precio <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Precio inválido',
                    text: 'Por favor ingrese un precio válido (mayor que 0)',
                    confirmButtonColor: '#1a4b8c'
                });
                return;
            }

            const producto = {
                item: currentEditIndex !== null ? productosCotizacion[currentEditIndex].item : itemCounter++,
                id: document.getElementById('producto_id').value,
                codigo: document.getElementById('producto_codigo').value,
                descripcion: document.getElementById('producto_descripcion').value,
                precio: parseFloat(document.getElementById('producto_precio').value).toFixed(2),
                cantidad: cantidad,
                total: parseFloat(document.getElementById('producto_total').value).toFixed(2),
                notas: document.getElementById('producto_notas').value
            };

            if (currentEditIndex !== null) {
                // Editar producto existente
                productosCotizacion[currentEditIndex] = producto;
            } else {
                // Agregar nuevo producto
                productosCotizacion.push(producto);
            }

            actualizarTablaProductos();
            calcularTotalCotizacion();

            // Mostrar notificación
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: currentEditIndex !== null ? 'Producto actualizado' : 'Producto agregado',
                showConfirmButton: false,
                timer: 1500,
                toast: true
            });

            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
        }

        function actualizarTablaProductos() {
            const tbody = document.getElementById('productosBody');

            if (productosCotizacion.length > 0) {
                tbody.innerHTML = '';

                productosCotizacion.forEach((producto, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td class="text-center">${producto.item}</td>
                    <td>${producto.codigo}</td>
                    <td>
                        ${producto.descripcion}
                        ${producto.notas ? `<div class="product-notes"><small><i class="bi bi-info-circle"></i> ${producto.notas}</small></div>` : ''}
                    </td>
                    <td class="text-end">S/ ${producto.precio}</td>
                    <td class="text-center">${producto.cantidad}</td>
                    <td class="text-end">S/ ${producto.total}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-edit me-1" data-index="${index}" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-delete" data-index="${index}" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                    tbody.appendChild(row);
                });

                // Agregar eventos a los botones de editar y eliminar
                document.querySelectorAll('.btn-edit').forEach(btn => {
                    btn.addEventListener('click', function() {
                        editProduct(parseInt(this.getAttribute('data-index')));
                    });
                });

                document.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.addEventListener('click', function() {
                        deleteProduct(parseInt(this.getAttribute('data-index')));
                    });
                });
            } else {
                tbody.innerHTML = `
                <tr class="empty-row">
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-box-seam"></i>
                            <p>No hay productos agregados</p>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddFirstProduct">
                                <i class="bi bi-plus-circle me-1"></i> Agregar primer producto
                            </button>
                        </div>
                    </td>
                </tr>
            `;

                // Agregar evento al botón de agregar primer producto
                document.getElementById('btnAddFirstProduct')?.addEventListener('click', showProductModal);
            }
        }

        function editProduct(index) {
            const producto = productosCotizacion[index];
            currentEditIndex = index;

            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('productModal'));
            modal.show();

            // Llenar datos del producto
            document.getElementById('producto_id').value = producto.id;
            document.getElementById('producto_codigo').value = producto.codigo;
            document.getElementById('producto_descripcion').value = producto.descripcion;
            document.getElementById('producto_precio').value = producto.precio;
            document.getElementById('producto_cantidad').value = producto.cantidad;
            document.getElementById('producto_total').value = producto.total;
            document.getElementById('producto_notas').value = producto.notas || '';

            document.getElementById('selectedProductDetails').style.display = 'block';
            document.getElementById('btnAddToQuote').disabled = false;

            // Cambiar texto del botón
            document.getElementById('btnAddToQuote').innerHTML = '<i class="bi bi-save-fill me-1"></i> Actualizar Producto';
        }

        function deleteProduct(index) {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    productosCotizacion.splice(index, 1);

                    // Reorganizar los números de item
                    itemCounter = 1;
                    productosCotizacion.forEach(producto => {
                        producto.item = itemCounter++;
                    });

                    actualizarTablaProductos();
                    calcularTotalCotizacion();

                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Producto eliminado',
                        showConfirmButton: false,
                        timer: 1500,
                        toast: true
                    });
                }
            });
        }

        function calcularTotalCotizacion() {
            const total = productosCotizacion.reduce((sum, producto) => sum + parseFloat(producto.total), 0);
            document.getElementById('total').value = total.toFixed(2);
        }
    </script>
</body>

</html>