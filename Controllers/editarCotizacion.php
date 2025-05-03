<?php
session_start();
include "Conexion.php";

header('Content-Type: application/json');

try {
    $id = $_POST['id'];
    $datos = json_decode($_POST['datos'], true);

    // Verificar si ya fue convertida a venta
    $sqlCheck = "SELECT estado FROM gestionCotizacion WHERE id = ?";
    $stmtCheck = $mysqli->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $id);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();
    $cotizacion = $result->fetch_assoc();

    if ($cotizacion['estado'] === 'Convertido a Venta') {
        throw new Exception("No se puede editar una cotización convertida a venta");
    }

    // Actualizar cotización
    $sql = "UPDATE gestionCotizacion SET 
        cliente = ?,
        tipo_documento = ?,
        numero_documento = ?,
        condiciones = ?,
        forma_pago = ?,
        validez = ?,
        cuenta = ?,
        productos = ?
        WHERE id = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param(
        "ssssssssi",
        $datos['cliente'],
        $datos['tipo_documento'],
        $datos['numero_documento'],
        $datos['condiciones'],
        $datos['forma_pago'],
        $datos['validez'],
        $datos['cuenta'],
        json_encode($datos['productos']),
        $id
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Error al actualizar: " . $stmt->error);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>