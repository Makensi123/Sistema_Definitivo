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
    $_SESSION['error'] = "Cotización no encontrada";
    header("Location: GestionCotizaciones.php");
    exit;
}

// Obtener productos de la cotización
$stmt_productos = $mysqli->prepare("SELECT * FROM cotizacion_productos WHERE cotizacion_id = ?");
$stmt_productos->bind_param("i", $id);
$stmt_productos->execute();
$productos = $stmt_productos->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización #<?php echo $id; ?> - Electrotop Perú</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Styles/CrearCotizacion.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>

<body>
    <div class="container-fluid px-0">
        <!-- Barra superior -->
        <div class="top-bar bg-primary py-2 text-white">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="assets/logo-icono.png" alt="Icono Electrotop" class="me-2" style="height: 30px;">
                    <span class="fw-bold">COTIZACIÓN #<?php echo str_pad($id, 5, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="user-info">
                    <i class="bi bi-person-circle me-1"></i>
                    <?php echo $_SESSION['user']['nombre'] ?? 'Usuario'; ?>
                </div>
            </div>
        </div>

        <div class="container py-4">
            <!-- Botones de acción -->
            <div class="d-flex justify-content-between mb-4">
                <a href="GestionCotizaciones.php" class="btn btn-back">
                    <i class="bi bi-arrow-left-circle-fill me-1"></i> Volver
                </a>
                <div>
                    <button type="button" class="btn btn-download me-2" id="btnDownloadPDF">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Descargar PDF
                    </button>
                    <a href="EditarCotizacion.php?id=<?php echo $id; ?>" class="btn btn-edit me-2">
                        <i class="bi bi-pencil-fill me-1"></i> Editar
                    </a>
                    <a href="GenerarPDF.php?id=<?php echo $id; ?>" target="_blank" class="btn btn-print">
                        <i class="bi bi-printer-fill me-1"></i> Imprimir
                    </a>
                </div>
            </div>

            <!-- Encabezado de la cotización -->
            <div class="cotizacion-container" id="cotizacionContent">
                <!-- Encabezado de la empresa -->
                <div class="header-section position-relative">
                    <div class="company-brand">
                        <img src="assets/logo1-original.png" alt="Electrotop Perú" class="company-logo">
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
                                <div><?php echo htmlspecialchars($cotizacion['direccion']); ?></div>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-envelope-fill"></i>
                            <div>
                                <label>CORREO</label>
                                <div><?php echo htmlspecialchars($cotizacion['email']); ?></div>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-file-text-fill"></i>
                            <div>
                                <label>RUC</label>
                                <div><?php echo htmlspecialchars($cotizacion['ruc']); ?></div>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-phone-fill"></i>
                            <div>
                                <label>TELÉFONO</label>
                                <div><?php echo htmlspecialchars($cotizacion['celular']); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="document-type-badge">
                        <span><?php echo $cotizacion['tipo_operacion']; ?></span>
                    </div>
                </div>

                <!-- Información del cliente -->
                <div class="client-info-section">
                    <h5 class="section-title"><i class="bi bi-person-lines-fill me-2"></i>INFORMACIÓN DEL CLIENTE</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-field">
                                <label>CLIENTE</label>
                                <div><?php echo htmlspecialchars($cotizacion['cliente']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-field">
                                <label>FECHA</label>
                                <div><?php echo date('d/m/Y', strtotime($cotizacion['fecha'])); ?></div>
                            </div>
                            <div class="info-field mt-2">
                                <label>HORA</label>
                                <div><?php echo substr($cotizacion['hora'], 0, 5); ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-field">
                                <label>TIPO DOCUMENTO</label>
                                <div><?php echo htmlspecialchars($cotizacion['tipo_documento']); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="info-field">
                                <label>NÚMERO DOCUMENTO</label>
                                <div><?php echo htmlspecialchars($cotizacion['numero_documento']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
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
                    <h5 class="section-title"><i class="bi bi-cart-plus-fill me-2"></i>PRODUCTOS/SERVICIOS</h5>
                    
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($productos) > 0): ?>
                                    <?php foreach ($productos as $producto): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $producto['item'] ?? ''; ?></td>
                                            <td><?php echo htmlspecialchars($producto['codigo']); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($producto['descripcion']); ?>
                                                <?php if (!empty($producto['notas'])): ?>
                                                    <div class="product-notes">
                                                        <small><i class="bi bi-info-circle"></i> <?php echo htmlspecialchars($producto['notas']); ?></small>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">S/ <?php echo number_format($producto['precio'], 2); ?></td>
                                            <td class="text-center"><?php echo $producto['cantidad']; ?></td>
                                            <td class="text-end">S/ <?php echo number_format($producto['total'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="bi bi-box-seam"></i>
                                                <p>No hay productos en esta cotización</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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
                    <h5 class="section-title"><i class="bi bi-file-text-fill me-2"></i>CONDICIONES COMERCIALES</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="info-field">
                                <label>CONDICIONES COMERCIALES</label>
                                <div><?php echo nl2br(htmlspecialchars($cotizacion['condiciones'])); ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-field">
                                <label>FORMA DE PAGO</label>
                                <div><?php echo htmlspecialchars($cotizacion['forma_pago']); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <div class="info-field">
                                <label>VALIDEZ (días)</label>
                                <div><?php echo htmlspecialchars($cotizacion['validez']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-9">
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
                <div class="additional-info mt-4 pt-3 border-top">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-field">
                                <label>CREADO POR</label>
                                <div><?php echo htmlspecialchars($cotizacion['usuario_nombre']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="info-field">
                                <label>FECHA DE CREACIÓN</label>
                                <div><?php echo date('d/m/Y H:i', strtotime($cotizacion['fecha'] . ' ' . $cotizacion['hora'])); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Generar PDF
        document.getElementById('btnDownloadPDF').addEventListener('click', function() {
            // Mostrar carga
            const loading = Swal.fire({
                title: 'Generando PDF',
                html: 'Por favor espere mientras se genera el documento...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Configuración para html2pdf
            const element = document.getElementById('cotizacionContent');
            const opt = {
                margin: 10,
                filename: `Cotizacion_${document.querySelector('.company-name').textContent}_${document.querySelector('.document-type-badge span').textContent}_<?php echo str_pad($id, 5, '0', STR_PAD_LEFT); ?>.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // Generar y descargar PDF
            html2pdf().from(element).set(opt).save().then(() => {
                loading.close();
            });
        });
    </script>
</body>

</html>