<?php
session_start();
include "Conexion.php";

header('Content-Type: application/json');

try {
    $id = $_POST['id'];
    $estado = $_POST['estado'];

    $sql = "UPDATE gestionCotizacion SET estado = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("si", $estado, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>