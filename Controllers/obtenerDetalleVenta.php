<?php
session_start();
include "Conexion.php";

// Verificar sesión
if (!isset($_SESSION['user'])) {
    die(json_encode(['error' => 'Acceso no autorizado']));
}

// Obtener parámetros
$id = $_GET['id'] ?? null;
$formato = $_GET['formato'] ?? 'visualizacion';

if (!$id) {
    die(json_encode(['error' => 'ID de venta no especificado']));
}

try {
    // Consultar la venta
    $sqlVenta = "SELECT v.*, g.id as id_cotizacion, g.condiciones as condiciones_cotizacion 
                FROM ventas v 
                LEFT JOIN gestionCotizacion g ON v.id_cotizacion = g.id 
                WHERE v.id = ?";
    $stmtVenta = $mysqli->prepare($sqlVenta);
    
    if (!$stmtVenta) {
        throw new Exception("Error al preparar consulta de venta: " . $mysqli->error);
    }
    
    $stmtVenta->bind_param("i", $id);
    
    if (!$stmtVenta->execute()) {
        throw new Exception("Error al ejecutar consulta de venta: " . $stmtVenta->error);
    }
    
    $resultVenta = $stmtVenta->get_result();
    $venta = $resultVenta->fetch_assoc();

    if (!$venta) {
        die(json_encode(['error' => 'Venta no encontrada']));
    }

    // Consultar productos de la venta (solo de detalle_venta ya que no hay relación con productos)
    $sqlProductos = "SELECT * FROM detalle_venta WHERE id_venta = ?";
    $stmtProductos = $mysqli->prepare($sqlProductos);
    
    if (!$stmtProductos) {
        throw new Exception("Error al preparar consulta de productos: " . $mysqli->error);
    }
    
    $stmtProductos->bind_param("i", $id);
    
    if (!$stmtProductos->execute()) {
        throw new Exception("Error al ejecutar consulta de productos: " . $stmtProductos->error);
    }
    
    $resultProductos = $stmtProductos->get_result();
    $productos = [];

    while ($producto = $resultProductos->fetch_assoc()) {
        $productos[] = $producto;
    }

    // Formato de edición
    if ($formato === 'edicion') {
        ?>
        <form id="formEditarVenta">
            <input type="hidden" id="editId" value="<?= htmlspecialchars($venta['id']) ?>">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Cliente</label>
                    <input type="text" class="form-control" id="editCliente" value="<?= htmlspecialchars($venta['cliente']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo Documento</label>
                    <select class="form-select" id="editTipoDocumento" required>
                        <option value="DNI" <?= $venta['tipo_documento'] == 'DNI' ? 'selected' : '' ?>>DNI</option>
                        <option value="RUC" <?= $venta['tipo_documento'] == 'RUC' ? 'selected' : '' ?>>RUC</option>
                        <option value="CE" <?= $venta['tipo_documento'] == 'CE' ? 'selected' : '' ?>>Carnet Extranjería</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Número Documento</label>
                    <input type="text" class="form-control" id="editNumeroDocumento" value="<?= htmlspecialchars($venta['numero_documento']) ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Método de Pago</label>
                    <select class="form-select" id="editMetodoPago" required>
                        <option value="Efectivo" <?= $venta['metodo_pago'] == 'Efectivo' ? 'selected' : '' ?>>Efectivo</option>
                        <option value="Tarjeta" <?= $venta['metodo_pago'] == 'Tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
                        <option value="Transferencia" <?= $venta['metodo_pago'] == 'Transferencia' ? 'selected' : '' ?>>Transferencia</option>
                        <option value="Credito" <?= $venta['metodo_pago'] == 'Credito' ? 'selected' : '' ?>>Crédito</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Observaciones</label>
                    <input type="text" class="form-control" id="editObservaciones" value="<?= htmlspecialchars($venta['observaciones']) ?>">
                </div>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th width="5%">Item</th>
                            <th width="15%">Código</th>
                            <th width="40%">Descripción</th>
                            <th width="15%">P. Unit.</th>
                            <th width="10%">Cantidad</th>
                            <th width="15%">Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($productos as $index => $producto): ?>
                        <tr class="producto-editable" data-id="<?= htmlspecialchars($producto['id']) ?>">
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td><input type="text" class="form-control form-control-sm edit-codigo" value="<?= htmlspecialchars($producto['codigo']) ?>" required></td>
                            <td><textarea class="form-control form-control-sm edit-descripcion" rows="2" required><?= htmlspecialchars($producto['descripcion']) ?></textarea></td>
                            <td><input type="number" step="0.01" min="0.01" class="form-control form-control-sm edit-precio" value="<?= htmlspecialchars($producto['precio_unitario']) ?>" required></td>
                            <td><input type="number" min="1" class="form-control form-control-sm edit-cantidad" value="<?= htmlspecialchars($producto['cantidad']) ?>" required></td>
                            <td><textarea class="form-control form-control-sm edit-notas" rows="2"><?= htmlspecialchars($producto['notas']) ?></textarea></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
        <?php
    } 
    // Formato de visualización normal
    else {
        ?>
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Cliente: <?= htmlspecialchars($venta['cliente']) ?></h4>
                    <p>Documento: <?= htmlspecialchars($venta['tipo_documento']) ?>: <?= htmlspecialchars($venta['numero_documento']) ?></p>
                    <p>Fecha: <?= date('d/m/Y', strtotime($venta['fecha'])) ?></p>
                    <?php if($venta['id_cotizacion']): ?>
                    <p>Cotización: COT-<?= str_pad($venta['id_cotizacion'], 5, '0', STR_PAD_LEFT) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 text-end">
                    <h4>VEN-<?= str_pad($venta['id'], 5, '0', STR_PAD_LEFT) ?></h4>
                    <p>Estado: <span class="badge bg-<?= $venta['estado'] == 'Pagada' ? 'success' : ($venta['estado'] == 'Anulada' ? 'danger' : 'warning') ?>"><?= $venta['estado'] ?></span></p>
                    <p>Vendedor: <?= htmlspecialchars($venta['usuario']) ?></p>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th width="5%">Item</th>
                            <th width="15%">Código</th>
                            <th width="40%">Descripción</th>
                            <th width="15%">P. Unit.</th>
                            <th width="10%">Cantidad</th>
                            <th width="15%">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($productos as $index => $producto): ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($producto['codigo']) ?></td>
                            <td>
                                <?= htmlspecialchars($producto['descripcion']) ?>
                                <?php if(!empty($producto['notas'])): ?>
                                <div class="text-muted"><small><i class="bi bi-info-circle"></i> <?= htmlspecialchars($producto['notas']) ?></small></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">S/ <?= number_format($producto['precio_unitario'], 2) ?></td>
                            <td class="text-center"><?= htmlspecialchars($producto['cantidad']) ?></td>
                            <td class="text-end">S/ <?= number_format($producto['total'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Subtotal:</th>
                            <th class="text-end">S/ <?= number_format($venta['subtotal'] ?? $venta['total'], 2) ?></th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-end">IGV (18%):</th>
                            <th class="text-end">S/ <?= number_format($venta['igv'] ?? ($venta['total'] * 0.18), 2) ?></th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-end">TOTAL:</th>
                            <th class="text-end">S/ <?= number_format($venta['total'], 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h5>Método de Pago</h5>
                    <p><?= htmlspecialchars($venta['metodo_pago']) ?></p>
                    <?php if(!empty($venta['observaciones'])): ?>
                    <h5>Observaciones</h5>
                    <p><?= nl2br(htmlspecialchars($venta['observaciones'])) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <?php if(!empty($venta['condiciones_cotizacion'])): ?>
                    <h5>Condiciones de Cotización</h5>
                    <p><?= nl2br(htmlspecialchars($venta['condiciones_cotizacion'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
} catch (Exception $e) {
    die(json_encode(['error' => $e->getMessage()]));
} finally {
    if (isset($stmtVenta)) $stmtVenta->close();
    if (isset($stmtProductos)) $stmtProductos->close();
    $mysqli->close();
}
?>