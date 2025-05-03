<?php
include "Conexion.php";

$estado = $_GET['estado'] ?? '';
$fechaDesde = $_GET['fechaDesde'] ?? '';
$fechaHasta = $_GET['fechaHasta'] ?? '';
$highlightId = $_GET['highlight'] ?? null;

// Construir consulta SQL con filtros
$sql = "SELECT * FROM gestionCotizacion WHERE 1=1";
$params = [];
$types = '';

if (!empty($estado)) {
    $sql .= " AND estado = ?";
    $params[] = $estado;
    $types .= 's';
}

if (!empty($fechaDesde)) {
    $sql .= " AND fecha >= ?";
    $params[] = $fechaDesde;
    $types .= 's';
}

if (!empty($fechaHasta)) {
    $sql .= " AND fecha <= ?";
    $params[] = $fechaHasta;
    $types .= 's';
}

$sql .= " ORDER BY fecha DESC, hora DESC";

$stmt = $mysqli->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$html = '';
$total = 0;

while ($cotizacion = $result->fetch_assoc()) {
    $total++;
    $claseEstado = '';
    switch ($cotizacion['estado']) {
        case 'Pendiente':
            $claseEstado = 'estado-pendiente';
            break;
        case 'Aprobado':
            $claseEstado = 'estado-aprobado';
            break;
        case 'Rechazado':
            $claseEstado = 'estado-rechazado';
            break;
        case 'Convertido a Venta':
            $claseEstado = 'estado-venta';
            break;
    }

    // Añadir clase para resaltar si es la cotización recién creada
    $highlightClass = ($highlightId && $highlightId == $cotizacion['id']) ? 'highlight-row' : '';

    $html .= '<tr class="cotizacion-row ' . $claseEstado . ' ' . $highlightClass . '" data-id="' . $cotizacion['id'] . '">';
    $html .= '<td>' . $cotizacion['id'] . '</td>';
    $html .= '<td>' . date('d/m/Y', strtotime($cotizacion['fecha'])) . '</td>';
    $html .= '<td>' . htmlspecialchars($cotizacion['cliente']) . '</td>';
    $html .= '<td>' . $cotizacion['tipo_documento'] . ': ' . $cotizacion['numero_documento'] . '</td>';
    $html .= '<td class="text-end">S/ ' . number_format($cotizacion['total'], 2) . '</td>';
    $html .= '<td><span class="badge bg-' . getColorEstado($cotizacion['estado']) . '">' . $cotizacion['estado'] . '</span></td>';
    $html .= '<td class="action-buttons">';
    $html .= '<button class="btn btn-sm btn-primary ver-detalle me-1" data-id="' . $cotizacion['id'] . '">';
    $html .= '<i class="bi bi-eye"></i>';
    $html .= '</button>';

    if ($cotizacion['estado'] == 'Pendiente') {
        $html .= '<button class="btn btn-sm btn-warning editar-cotizacion me-1" data-id="' . $cotizacion['id'] . '">';
        $html .= '<i class="bi bi-pencil"></i>';
        $html .= '</button>';
        $html .= '<button class="btn btn-sm btn-success aprobar-cotizacion me-1" data-id="' . $cotizacion['id'] . '">';
        $html .= '<i class="bi bi-check-circle"></i>';
        $html .= '</button>';
        $html .= '<button class="btn btn-sm btn-danger rechazar-cotizacion me-1" data-id="' . $cotizacion['id'] . '">';
        $html .= '<i class="bi bi-x-circle"></i>';
        $html .= '</button>';
        $html .= '<button class="btn btn-sm btn-dark eliminar-cotizacion" data-id="' . $cotizacion['id'] . '">';
        $html .= '<i class="bi bi-trash"></i>';
        $html .= '</button>';
    } elseif ($cotizacion['estado'] == 'Aprobado') {
        $html .= '<button class="btn btn-sm btn-info convertir-venta" data-id="' . $cotizacion['id'] . '">';
        $html .= '<i class="bi bi-cart-check"></i> Venta';
        $html .= '</button>';
    }
    $html .= '</td>';
    $html .= '</tr>';
}

function getColorEstado($estado)
{
    switch ($estado) {
        case 'Pendiente':
            return 'warning';
        case 'Aprobado':
            return 'success';
        case 'Rechazado':
            return 'danger';
        case 'Convertido a Venta':
            return 'info';
        default:
            return 'secondary';
    }
}

// Al final del archivo obtenerCotizaciones.php, cambia el echo json_encode a:
echo json_encode([
    'html' => $html,
    'total' => $total,
    'highlightId' => $highlightId,
    'success' => true
]);

$stmt->close();
$mysqli->close();
