<?php
session_start();
include "Conexion.php";

header('Content-Type: application/json');

try {
    $id = $_POST['id'];
    $datos = json_decode($_POST['datos'], true);

    // Validar datos
    if (empty($datos['cliente']) || empty($datos['tipo_documento']) || empty($datos['numero_documento'])) {
        throw new Exception("Todos los campos obligatorios deben estar completos");
    }

    // Verificar si la venta puede ser editada (no completada o anulada)
    $sqlCheck = "SELECT estado FROM ventas WHERE id = ?";
    $stmtCheck = $mysqli->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $id);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();
    $venta = $result->fetch_assoc();

    if ($venta['estado'] !== 'PENDIENTE') {
        throw new Exception("Solo se pueden editar ventas en estado PENDIENTE");
    }

    // Iniciar transacción
    $mysqli->begin_transaction();

    // 1. Actualizar datos principales de la venta
    $sqlVenta = "UPDATE ventas SET 
        cliente = ?,
        tipo_documento = ?,
        numero_documento = ?,
        metodo_pago = ?,
        observaciones = ?
        WHERE id = ?";

    $stmtVenta = $mysqli->prepare($sqlVenta);
    $stmtVenta->bind_param(
        "sssssi",
        $datos['cliente'],
        $datos['tipo_documento'],
        $datos['numero_documento'],
        $datos['metodo_pago'],
        $datos['observaciones'],
        $id
    );

    if (!$stmtVenta->execute()) {
        throw new Exception("Error al actualizar venta: " . $stmtVenta->error);
    }

    // 2. Actualizar productos
    $totalVenta = 0;
    
    foreach ($datos['productos'] as $producto) {
        $totalProducto = $producto['precio'] * $producto['cantidad'];
        $totalVenta += $totalProducto;

        $sqlProducto = "UPDATE detalle_venta SET 
            codigo = ?,
            descripcion = ?,
            precio_unitario = ?,
            cantidad = ?,
            total = ?,
            notas = ?
            WHERE id = ? AND id_venta = ?";

        $stmtProducto = $mysqli->prepare($sqlProducto);
        $stmtProducto->bind_param(
            "ssdddsii",
            $producto['codigo'],
            $producto['descripcion'],
            $producto['precio'],
            $producto['cantidad'],
            $totalProducto,
            $producto['notas'],
            $producto['id'],
            $id
        );

        if (!$stmtProducto->execute()) {
            throw new Exception("Error al actualizar producto: " . $stmtProducto->error);
        }
    }

    // 3. Actualizar total de la venta
    $sqlTotal = "UPDATE ventas SET total = ? WHERE id = ?";
    $stmtTotal = $mysqli->prepare($sqlTotal);
    $stmtTotal->bind_param("di", $totalVenta, $id);
    
    if (!$stmtTotal->execute()) {
        throw new Exception("Error al actualizar total: " . $stmtTotal->error);
    }

    // Confirmar transacción
    $mysqli->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>