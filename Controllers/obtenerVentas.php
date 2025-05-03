<?php
session_start();
include "Conexion.php";

header('Content-Type: application/json');

try {
    $estado = $_GET['estado'] ?? '';
    $fechaDesde = $_GET['fechaDesde'] ?? '';
    $fechaHasta = $_GET['fechaHasta'] ?? '';
    $highlightId = $_GET['highlight'] ?? null;

    // Construir consulta SQL con filtros
    $sql = "SELECT v.*, COUNT(dv.id) as items 
            FROM ventas v
            LEFT JOIN detalle_venta dv ON v.id = dv.id_venta
            WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($estado)) {
        $sql .= " AND v.estado = ?";
        $params[] = $estado;
        $types .= 's';
    }

    if (!empty($fechaDesde)) {
        $sql .= " AND v.fecha >= ?";
        $params[] = $fechaDesde;
        $types .= 's';
    }

    if (!empty($fechaHasta)) {
        $sql .= " AND v.fecha <= ?";
        $params[] = $fechaHasta;
        $types .= 's';
    }

    $sql .= " GROUP BY v.id ORDER BY v.fecha DESC, v.hora DESC";

    $stmt = $mysqli->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $html = '';
    $total = 0;

    while ($venta = $result->fetch_assoc()) {
        $total++;
        $claseEstado = '';
        switch ($venta['estado']) {
            case 'Pagada':
                $claseEstado = 'estado-completado';
                break;
            case 'Registrada':
                $claseEstado = 'estado-pendiente';
                break;
            case 'Anulada':
                $claseEstado = 'estado-anulado';
                break;
        }

        $highlightClass = ($highlightId && $highlightId == $venta['id']) ? 'highlight-row' : '';
        
        $html .= '<tr class="venta-row '.$claseEstado.' '.$highlightClass.'" data-id="'.$venta['id'].'">';
        $html .= '<td>VEN-'.str_pad($venta['id'], 5, '0', STR_PAD_LEFT).'</td>';
        $html .= '<td>'.date('d/m/Y', strtotime($venta['fecha'])).'</td>';
        $html .= '<td>'.htmlspecialchars($venta['cliente']).'</td>';
        $html .= '<td>'.$venta['tipo_documento'].': '.$venta['numero_documento'].'</td>';
        $html .= '<td class="text-end">S/ '.number_format($venta['total'], 2).'</td>';
        $html .= '<td><span class="badge bg-'.($venta['estado'] == 'Pagada' ? 'success' : ($venta['estado'] == 'Registrada' ? 'warning' : 'danger')).'">'.$venta['estado'].'</span></td>';
        $html .= '<td class="action-buttons">';
        $html .= '<button class="btn btn-sm btn-primary ver-detalle me-1" data-id="'.$venta['id'].'">';
        $html .= '<i class="bi bi-eye"></i>';
        $html .= '</button>';

        if ($venta['estado'] == 'Registrada') {
            $html .= '<button class="btn btn-sm btn-warning editar-venta me-1" data-id="'.$venta['id'].'">';
            $html .= '<i class="bi bi-pencil"></i>';
            $html .= '</button>';
            $html .= '<button class="btn btn-sm btn-success completar-venta me-1" data-id="'.$venta['id'].'">';
            $html .= '<i class="bi bi-check-circle"></i>';
            $html .= '</button>';
            $html .= '<button class="btn btn-sm btn-danger anular-venta" data-id="'.$venta['id'].'">';
            $html .= '<i class="bi bi-x-circle"></i>';
            $html .= '</button>';
        }
        $html .= '</td>';
        $html .= '</tr>';
    }

    if ($total === 0) {
        $html = '<tr><td colspan="7" class="text-center">No se encontraron ventas</td></tr>';
    }

    echo json_encode([
        'success' => true,
        'html' => $html,
        'total' => $total,
        'highlightId' => $highlightId
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'html' => '<tr><td colspan="7" class="text-center text-danger">Error al cargar ventas</td></tr>',
        'total' => 0
    ]);
}
?>