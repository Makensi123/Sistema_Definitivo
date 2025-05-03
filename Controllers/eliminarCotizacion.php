<?php
session_start();
include "Conexion.php";

header('Content-Type: application/json');

try {
    $id = $_POST['id'];

    // Verificar si ya fue convertida a venta
    $sqlCheck = "SELECT estado FROM gestionCotizacion WHERE id = ?";
    $stmtCheck = $mysqli->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $id);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();
    $cotizacion = $result->fetch_assoc();

    if ($cotizacion['estado'] === 'Convertido a Venta') {
        throw new Exception("No se puede eliminar una cotización convertida a venta");
    }

    // Eliminar cotización
    $sql = "DELETE FROM gestionCotizacion WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Error al eliminar: " . $stmt->error);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>