<?php
session_start();
include "Conexion.php";

// Verificar sesión
if (!isset($_SESSION['user'])) {
    die("Acceso no autorizado");
}

// Obtener parámetros
$id = $_GET['id'] ?? null;
$formato = $_GET['formato'] ?? 'visualizacion';

if (!$id) {
    die("ID de cotización no especificado");
}

// Consultar la cotización
$sql = "SELECT * FROM gestionCotizacion WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cotizacion = $result->fetch_assoc();

if (!$cotizacion) {
    die("Cotización no encontrada");
}

// Decodificar productos
$productos = json_decode($cotizacion['productos'], true);

// Función para determinar color de estado
function getColorEstado($estado) {
    switch($estado) {
        case 'Pendiente': return 'warning';
        case 'Aprobado': return 'success';
        case 'Rechazado': return 'danger';
        case 'Convertido a Venta': return 'info';
        default: return 'secondary';
    }
}

// Formato de edición
if ($formato === 'edicion') {
    ?>
    <form id="formEditarCotizacion">
        <input type="hidden" id="editId" value="<?= $cotizacion['id'] ?>">
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Cliente</label>
                <input type="text" class="form-control" id="editCliente" value="<?= htmlspecialchars($cotizacion['cliente']) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tipo Documento</label>
                <select class="form-select" id="editTipoDocumento" required>
                    <option value="DNI" <?= $cotizacion['tipo_documento'] == 'DNI' ? 'selected' : '' ?>>DNI</option>
                    <option value="RUC" <?= $cotizacion['tipo_documento'] == 'RUC' ? 'selected' : '' ?>>RUC</option>
                    <option value="CE" <?= $cotizacion['tipo_documento'] == 'CE' ? 'selected' : '' ?>>Carnet Extranjería</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Número Documento</label>
                <input type="text" class="form-control" id="editNumeroDocumento" value="<?= $cotizacion['numero_documento'] ?>" required>
            </div>
        </div>

        <div class="table-responsive mb-3">
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th width="5%">Item</th>
                        <th width="15%">Código</th>
                        <th width="30%">Descripción</th>
                        <th width="15%">P. Unit.</th>
                        <th width="10%">Cantidad</th>
                        <th width="25%">Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($productos as $index => $producto): ?>
                    <tr class="producto-editable" data-id="<?= $producto['id'] ?? '' ?>">
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td><input type="text" class="form-control form-control-sm edit-codigo" value="<?= $producto['codigo'] ?? '' ?>" required></td>
                        <td><textarea class="form-control form-control-sm edit-descripcion" rows="2" required><?= $producto['descripcion'] ?? '' ?></textarea></td>
                        <td><input type="number" step="0.01" min="0.01" class="form-control form-control-sm edit-precio" value="<?= $producto['precio'] ?? '' ?>" required></td>
                        <td><input type="number" min="1" class="form-control form-control-sm edit-cantidad" value="<?= $producto['cantidad'] ?? '' ?>" required></td>
                        <td><textarea class="form-control form-control-sm edit-notas" rows="2"><?= $producto['notas'] ?? '' ?></textarea></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-8">
                <label class="form-label">Condiciones Comerciales</label>
                <textarea class="form-control" id="editCondiciones" rows="3" required><?= htmlspecialchars($cotizacion['condiciones']) ?></textarea>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Forma de Pago</label>
                    <select class="form-select" id="editFormaPago" required>
                        <option value="CONTADO" <?= $cotizacion['forma_pago'] == 'CONTADO' ? 'selected' : '' ?>>Contado</option>
                        <option value="CREDITO_15D" <?= $cotizacion['forma_pago'] == 'CREDITO_15D' ? 'selected' : '' ?>>Crédito 15 días</option>
                        <option value="CREDITO_30D" <?= $cotizacion['forma_pago'] == 'CREDITO_30D' ? 'selected' : '' ?>>Crédito 30 días</option>
                        <option value="TARJETA_CREDITO" <?= $cotizacion['forma_pago'] == 'TARJETA_CREDITO' ? 'selected' : '' ?>>Tarjeta de Crédito</option>
                        <option value="TRANSFERENCIA" <?= $cotizacion['forma_pago'] == 'TRANSFERENCIA' ? 'selected' : '' ?>>Transferencia</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Validez (días)</label>
                    <input type="number" min="1" class="form-control" id="editValidez" value="<?= $cotizacion['validez'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cuenta Bancaria</label>
                    <textarea class="form-control" id="editCuenta" rows="2" required><?= htmlspecialchars($cotizacion['cuenta']) ?></textarea>
                </div>
            </div>
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
                <h4>Cliente: <?= htmlspecialchars($cotizacion['cliente']) ?></h4>
                <p>Documento: <?= $cotizacion['tipo_documento'] ?>: <?= $cotizacion['numero_documento'] ?></p>
                <p>Fecha: <?= date('d/m/Y', strtotime($cotizacion['fecha'])) ?></p>
            </div>
            <div class="col-md-6 text-end">
                <h4>COT-<?= str_pad($cotizacion['id'], 5, '0', STR_PAD_LEFT) ?></h4>
                <p>Estado: <span class="badge bg-<?= getColorEstado($cotizacion['estado']) ?>"><?= $cotizacion['estado'] ?></span></p>
                <p>Vendedor: <?= htmlspecialchars($cotizacion['usuario']) ?></p>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th width="5%">Item</th>
                        <th width="15%">Código</th>
                        <th width="35%">Descripción</th>
                        <th width="15%">P. Unit.</th>
                        <th width="10%">Cantidad</th>
                        <th width="20%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($productos as $producto): ?>
                    <tr>
                        <td class="text-center"><?= $producto['item'] ?></td>
                        <td><?= $producto['codigo'] ?></td>
                        <td>
                            <?= $producto['descripcion'] ?>
                            <?php if(!empty($producto['notas'])): ?>
                            <div class="text-muted"><small><i class="bi bi-info-circle"></i> <?= $producto['notas'] ?></small></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">S/ <?= number_format($producto['precio'], 2) ?></td>
                        <td class="text-center"><?= $producto['cantidad'] ?></td>
                        <td class="text-end">S/ <?= number_format($producto['total'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">TOTAL:</th>
                        <th class="text-end">S/ <?= number_format($cotizacion['total'], 2) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row">
            <div class="col-md-8">
                <h5>Condiciones Comerciales</h5>
                <p><?= nl2br(htmlspecialchars($cotizacion['condiciones'])) ?></p>
            </div>
            <div class="col-md-4">
                <h5>Forma de Pago</h5>
                <p><?= $cotizacion['forma_pago'] ?></p>
                <h5>Validez</h5>
                <p><?= $cotizacion['validez'] ?> días</p>
                <h5>Cuenta Bancaria</h5>
                <p><?= htmlspecialchars($cotizacion['cuenta']) ?></p>
            </div>
        </div>
    </div>
    <?php
}

$stmt->close();
$mysqli->close();
?>