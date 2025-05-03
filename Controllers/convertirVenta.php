<?php
session_start();
include "Conexion.php";

header('Content-Type: application/json');

try {
    $id = $_POST['id'];

    // 1. Obtener la cotización
    $sql = "SELECT * FROM gestionCotizacion WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $cotizacion = $stmt->get_result()->fetch_assoc();

    if (!$cotizacion) {
        throw new Exception("Cotización no encontrada");
    }

    // 2. Insertar en ventas
    $sqlVenta = "INSERT INTO ventas (
        id_cotizacion, cliente, fecha, hora, tipo_documento, 
        numero_documento, total, usuario, estado
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Registrada')";

    $stmtVenta = $mysqli->prepare($sqlVenta);
    $stmtVenta->bind_param(
        "isssssds",
        $id,
        $cotizacion['cliente'],
        $cotizacion['fecha'],
        $cotizacion['hora'],
        $cotizacion['tipo_documento'],
        $cotizacion['numero_documento'],
        $cotizacion['total'],
        $cotizacion['usuario']
    );

    if ($stmtVenta->execute()) {
        // 3. Actualizar estado de cotización
        $sqlUpdate = "UPDATE gestionCotizacion SET estado = 'Convertido a Venta' WHERE id = ?";
        $stmtUpdate = $mysqli->prepare($sqlUpdate);
        $stmtUpdate->bind_param("i", $id);
        $stmtUpdate->execute();
        
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Error al crear venta: " . $stmtVenta->error);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>