<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Cotización - Electrotop Perú</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="Styles/CrearCotizacion.css">
</head>
<body>
    <div class="container py-4">
        <form action="nuevaCotizacion.php" method="post">
            <div class="cotizacion-container">
                <!-- Encabezado de la empresa -->
                <div class="header-section">
                    <div class="company-name">ELECTROTOP PERÚ S.A.C.</div>
                    <div class="company-slogan">EQUIPOS DE SEGURIDAD REDES Y TELECOMUNICACIONES</div>
                    
                    <div class="company-info">
                        <div class="mb-3">
                            <label for="direccion" class="form-label">DIRECCIÓN</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">MAIL</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="ruc" class="form-label">RUC</label>
                            <input type="text" class="form-control" id="ruc" name="ruc" required>
                        </div>
                        <div class="mb-3">
                            <label for="celular" class="form-label">CEL</label>
                            <input type="tel" class="form-control" id="celular" name="celular" required>
                        </div>
                    </div>
                </div>

                <!-- Información del cliente -->
                <div class="client-info">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente" class="form-label">CLIENTE</label>
                            <input type="text" class="form-control" id="cliente" name="cliente" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="fecha" class="form-label">FECHA</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="tipo_documento" class="form-label">TIPO DOCUMENTO</label>
                            <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="CE">Carnet Extranjería</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="numero_documento" class="form-label">NÚMERO DOCUMENTO</label>
                            <input type="text" class="form-control" id="numero_documento" name="numero_documento" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tipo_operacion" class="form-label">TIPO OPERACIÓN</label>
                            <select class="form-select" id="tipo_operacion" name="tipo_operacion" required>
                                <option value="COTIZACION">COTIZACIÓN</option>
                                <option value="VENTA">VENTA</option>
                                <option value="PROFORMA">PROFORMA</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Agregar productos -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-primary">Productos/Servicios</h4>
                    <button type="button" class="btn btn-add-product text-white" id="btnAddProduct">
                        <i class="bi bi-plus-circle"></i> Agregar Producto
                    </button>
                </div>

                <!-- Tabla de productos - Ahora responsiva -->
                <div class="table-responsive">
                    <table class="table products-table">
                        <thead>
                            <tr>
                                <th>ITEM</th>
                                <th>CÓDIGO</th>
                                <th>DESCRIPCIÓN</th>
                                <th>P. UND</th>
                                <th>CANT.</th>
                                <th>P. TOTAL</th>
                                <th>ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody id="productosBody">
                            <!-- Los productos se agregarán aquí dinámicamente -->
                            <tr class="text-center text-muted">
                                <td colspan="7">No hay productos agregados</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totales -->
                <div class="total-section">
                    <div class="input-group mb-3 justify-content-end">
                        <span class="input-group-text">TOTAL S/</span>
                        <input type="text" class="form-control" id="total" name="total" value="0.00" readonly style="max-width: 180px; font-weight: bold;">
                    </div>
                </div>

                <!-- Condiciones de pago -->
                <div class="payment-conditions">
                    <div>
                        <label for="condiciones" class="form-label">CONDICIONES COMERCIALES</label>
                        <textarea class="form-control" id="condiciones" name="condiciones" rows="4" placeholder="Ej: Precios incluyen IGV. Tiempo de entrega: 5 días hábiles. Garantía de 1 año."></textarea>
                    </div>
                    <div>
                        <label for="forma_pago" class="form-label">FORMA DE PAGO</label>
                        <select class="form-select" id="forma_pago" name="forma_pago">
                            <option value="CONTADO">CONTADO</option>
                            <option value="CREDITO_15D">CRÉDITO 15 DÍAS</option>
                            <option value="CREDITO_30D">CRÉDITO 30 DÍAS</option>
                            <option value="TARJETA_CREDITO">TARJETA DE CRÉDITO</option>
                            <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                        </select>
                    </div>
                    <div>
                        <label for="validez" class="form-label">VALIDEZ DE COTIZACIÓN (días)</label>
                        <input type="number" class="form-control" id="validez" name="validez" value="15" min="1">
                    </div>
                    <div>
                        <label for="cuenta" class="form-label">NÚMERO DE CUENTA</label>
                        <input type="text" class="form-control" id="cuenta" name="cuenta" placeholder="Cuenta bancaria para transferencias">
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-between mt-5">
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <div>
                        <button type="button" class="btn btn-print text-white me-2" onclick="window.print()">
                            <i class="bi bi-printer-fill"></i> Imprimir
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save-fill"></i> Guardar Cotización
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal para agregar productos -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Producto/Servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="producto_codigo" class="form-label">Código</label>
                            <input type="text" class="form-control" id="producto_codigo" placeholder="Código interno del producto">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="producto_cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="producto_cantidad" value="1" min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="producto_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="producto_descripcion" rows="4" placeholder="Descripción detallada del producto o servicio"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="producto_precio" class="form-label">Precio Unitario (S/)</label>
                            <input type="number" step="0.01" class="form-control" id="producto_precio" placeholder="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Producto</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="text" class="form-control" id="producto_total" value="0.00" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnSaveProduct">Agregar Producto</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Js/Cotizacion.js"></script>
</body>
</html>