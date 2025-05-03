<?php
session_start();
include "Conexion.php";

header('Content-Type: application/json');

try {
    $id = $_POST['id'];

    // Verificar si la venta existe
    $sqlCheck = "SELECT id FROM ventas WHERE id = ?";
    $stmtCheck = $mysqli->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $id);
    $stmtCheck->execute();
    
    if ($stmtCheck->get_result()->num_rows === 0) {
        throw new Exception("Venta no encontrada");
    }

    // Iniciar transacción
    $mysqli->begin_transaction();

    // 1. Eliminar detalles de venta
    $sqlDetalles = "DELETE FROM detalle_venta WHERE id_venta = ?";
    $stmtDetalles = $mysqli->prepare($sqlDetalles);
    $stmtDetalles->bind_param("i", $id);
    
    if (!$stmtDetalles->execute()) {
        throw new Exception("Error al eliminar detalles: " . $stmtDetalles->error);
    }

    // 2. Eliminar venta
    $sqlVenta = "DELETE FROM ventas WHERE id = ?";
    $stmtVenta = $mysqli->prepare($sqlVenta);
    $stmtVenta->bind_param("i", $id);
    
    if (!$stmtVenta->execute()) {
        throw new Exception("Error al eliminar venta: " . $stmtVenta->error);
    }

    // Confirmar transacción
    $mysqli->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>