<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

include "Controllers/Conexion.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: GestionCotizaciones.php");
    exit;
}

$id = $_GET['id'];

// Obtener cotización
$stmt = $mysqli->prepare("SELECT c.*, u.nombre as usuario_nombre 
                         FROM cotizaciones c
                         JOIN usuarios u ON c.usuario_id = u.id
                         WHERE c.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$cotizacion = $stmt->get_result()->fetch_assoc();

if (!$cotizacion) {
    header("Location: GestionCotizaciones.php");
    exit;
}

// Obtener productos de la cotización
$stmt_productos = $mysqli->prepare("SELECT * FROM cotizacion_productos WHERE cotizacion_id = ?");
$stmt_productos->bind_param("i", $id);
$stmt_productos->execute();
$productos = $stmt_productos->get_result()->fetch_all(MYSQLI_ASSOC);

// Generar HTML para el PDF
ob_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización #<?php echo $id; ?> - Electrotop Perú</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .cotizacion-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4a90e2;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1a4b8c;
            margin-bottom: 5px;
        }

        .company-slogan {
            font-size: 14px;
            color: #0d3b66;
            margin-bottom: 15px;
        }

        .company-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
            text-align: left;
        }

        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .detail-item label {
            font-size: 10px;
            font-weight: bold;
            color: #6c757d;
            margin-bottom: 2px;
            display: block;
        }

        .detail-item div {
            font-size: 12px;
        }

        .document-type-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #1a4b8c;
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-weight: bold;
            font-size: 14px;
        }

        .client-info-section {
            margin-bottom: 20px;
        }

        .section-title {
            color: #1a4b8c;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e6f0ff;
            font-size: 14px;
        }

        .info-field {
            margin-bottom: 10px;
        }

        .info-field label {
            font-size: 10px;
            font-weight: bold;
            color: #6c757d;
            margin-bottom: 2px;
            display: block;
        }

        .info-field div {
            font-size: 12px;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .products-table th {
            background-color: #1a4b8c;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }

        .products-table td {
            padding: 8px;
            border-bottom: 1px solid #e0e9ff;
            font-size: 12px;
        }

        .products-table tr:nth-child(even) {
            background-color: #f5f9ff;
        }

        .product-notes {
            color: #6c757d;
            font-size: 10px;
            margin-top: 3px;
        }

        .totals-section {
            text-align: right;
            margin: 20px 0;
        }

        .total-display {
            display: inline-flex;
            align-items: center;
            background-color: #e6f0ff;
            padding: 10px 20px;
            border-radius: 5px;
            border: 1px solid #7fb3ff;
            font-size: 16px;
            font-weight: bold;
        }

        .total-label {
            font-weight: bold;
            color: #0d3b66;
            margin-right: 10px;
        }

        .total-currency {
            font-weight: bold;
            color: #1a4b8c;
            margin-right: 5px;
        }

        .conditions-section {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed #c7d8ff;
        }

        .signatures-section {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e9ff;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin: 15px auto;
            width: 80%;
        }

        .signature-label {
            font-size: 12px;
            color: #6c757d;
        }

        .company-stamp img {
            height: 60px;
            opacity: 0.8;
        }

        .additional-info {
            font-size: 10px;
            color: #6c757d;
        }

        .badge {
            padding: 3px 8px;
            font-weight: bold;
            border-radius: 50px;
            font-size: 10px;
            text-transform: uppercase;
            display: inline-block;
        }

        .estado-pendiente {
            background-color: #ffc107;
            color: #343a40;
        }

        .estado-aprobada {
            background-color: #28a745;
            color: white;
        }

        .estado-rechazada {
            background-color: #dc3545;
            color: white;
        }

        .estado-facturada {
            background-color: #17a2b8;
            color: white;
        }
    </style>
</head>

<body>
    <div class="cotizacion-container">
        <!-- Encabezado de la empresa -->
        <div class="header-section position-relative">
            <div class="company-brand">
                <div class="company-text">
                    <div class="company-name">ELECTROTOP PERÚ S.A.C.</div>
                    <div class="company-slogan">Soluciones Integrales en Seguridad y Telecomunicaciones</div>
                </div>
            </div>

            <div class="company-details">
                <div class="detail-item">
                    <div>
                        <label>DIRECCIÓN</label>
                        <div><?php echo htmlspecialchars($cotizacion['direccion']); ?></div>
                    </div>
                </div>
                <div class="detail-item">
                    <div>
                        <label>CORREO</label>
                        <div><?php echo htmlspecialchars($cotizacion['email']); ?></div>
                    </div>
                </div>
                <div class="detail-item">
                    <div>
                        <label>RUC</label>
                        <div><?php echo htmlspecialchars($cotizacion['ruc']); ?></div>
                    </div>
                </div>
                <div class="detail-item">
                    <div>
                        <label>TELÉFONO</label>
                        <div><?php echo htmlspecialchars($cotizacion['celular']); ?></div>
                    </div>
                </div>
            </div>

            <div class="document-type-badge">
                <?php echo $cotizacion['tipo_operacion']; ?>
            </div>
        </div>

        <!-- Información del cliente -->
        <div class="client-info-section">
            <div class="section-title">INFORMACIÓN DEL CLIENTE</div>

            <div class="row" style="display: flex; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px; margin-bottom: 10px;">
                    <div class="info-field">
                        <label>CLIENTE</label>
                        <div><?php echo htmlspecialchars($cotizacion['cliente']); ?></div>
                    </div>
                </div>
                <div style="flex: 1; min-width: 150px; margin-bottom: 10px;">
                    <div class="info-field">
                        <label>FECHA</label>
                        <div><?php echo date('d/m/Y', strtotime($cotizacion['fecha'])); ?></div>
                    </div>
                </div>
                <div style="flex: 1; min-width: 150px; margin-bottom: 10px;">
                    <div class="info-field">
                        <label>HORA</label>
                        <div><?php echo substr($cotizacion['hora'], 0, 5); ?></div>
                    </div>
                </div>
            </div>

            <div class="row" style="display: flex; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px; margin-bottom: 10px;">
                    <div class="info-field">
                        <label>DOCUMENTO</label>
                        <div><?php echo htmlspecialchars($cotizacion['tipo_documento']); ?>: <?php echo htmlspecialchars($cotizacion['numero_documento']); ?></div>
                    </div>
                </div>
                <div style="flex: 1; min-width: 150px; margin-bottom: 10px;">
                    <div class="info-field">
                        <label>ESTADO</label>
                        <div>
                            <span class="badge estado-<?php echo strtolower($cotizacion['estado']); ?>">
                                <?php echo $cotizacion['estado']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos -->
        <div class="products-section">
            <div class="section-title">PRODUCTOS/SERVICIOS</div>

            <table class="products-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">ITEM</th>
                        <th style="width: 15%;">CÓDIGO</th>
                        <th style="width: 35%;">DESCRIPCIÓN</th>
                        <th style="width: 12%;">P. UNIT.</th>
                        <th style="width: 8%;">CANT.</th>
                        <th style="width: 15%;">P. TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($productos) > 0): ?>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $producto['item'] ?? ''; ?></td>
                                <td><?php echo htmlspecialchars($producto['codigo']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($producto['descripcion']); ?>
                                    <?php if (!empty($producto['notas'])): ?>
                                        <div class="product-notes">
                                            <i>Nota: <?php echo htmlspecialchars($producto['notas']); ?></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">S/ <?php echo number_format($producto['precio'], 2); ?></td>
                                <td style="text-align: center;"><?php echo $producto['cantidad']; ?></td>
                                <td style="text-align: right;">S/ <?php echo number_format($producto['total'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px;">
                                No hay productos en esta cotización
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Totales -->
        <div class="totals-section">
            <div class="total-display">
                <span class="total-label">TOTAL:</span>
                <span class="total-currency">S/</span>
                <span class="total-amount"><?php echo number_format($cotizacion['total'], 2); ?></span>
            </div>
        </div>

        <!-- Condiciones -->
        <div class="conditions-section">
            <div class="section-title">CONDICIONES COMERCIALES</div>

            <div style="display: flex; flex-wrap: wrap; margin-bottom: 15px;">
                <div style="flex: 2; min-width: 300px; margin-bottom: 10px;">
                    <div class="info-field">
                        <label>CONDICIONES COMERCIALES</label>
                        <div><?php echo nl2br(htmlspecialchars($cotizacion['condiciones'])); ?></div>
                    </div>
                </div>
                <div style="flex: 1; min-width: 150px; margin-bottom: 10px;">
                    <div class="info-field">
                        <label>FORMA DE PAGO</label>
                        <div><?php echo htmlspecialchars($cotizacion['forma_pago']); ?></div>
                    </div>
                </div>
            </div>

            <div style="display: flex; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 150px; margin-bottom: 10px;">
                    <div class="info-field">
                        <label>VALIDEZ (días)</label>
                        <div><?php echo htmlspecialchars($cotizacion['validez']); ?></div>
                    </div>
                </div>
                <div style="flex: 3; min-width: 300px; margin-bottom: 10px;">
                    <div class="info-field">
                        <label>CUENTA BANCARIA</label>
                        <div><?php echo nl2br(htmlspecialchars($cotizacion['cuenta'])); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Firmas -->
        <div class="signatures-section">
            <div class="signature-box client-signature">
                <div class="signature-line"></div>
                <div class="signature-label">FIRMA DEL CLIENTE</div>
            </div>
            <div class="signature-box company-signature">
                <div class="signature-line"></div>
                <div class="signature-label">FIRMA Y SELLO ELECTROTOP PERÚ</div>
                <div class="company-stamp">
                    <img src="assets/sello.png" alt="Sello Electrotop Perú">
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="additional-info">
            <div style="display: flex; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <div class="info-field">
                        <label>CREADO POR</label>
                        <div><?php echo htmlspecialchars($cotizacion['usuario_nombre']); ?></div>
                    </div>
                </div>
                <div style="flex: 1; min-width: 250px; text-align: right;">
                    <div class="info-field">
                        <label>FECHA DE CREACIÓN</label>
                        <div><?php echo date('d/m/Y H:i', strtotime($cotizacion['fecha'] . ' ' . $cotizacion['hora'])); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php
$html = ob_get_clean();

// Configurar encabezados para PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="Cotizacion_Electrotop_' . $id . '.pdf"');

// Cargar html2pdf
require_once 'vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
/*
try {
    $html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', array(10, 10, 10, 10));
    $html2pdf->writeHTML($html);
    $html2pdf->output();
} catch (Exception $e) {
    echo 'Error al generar PDF: ' . $e->getMessage();
}
    */
?>