<?php
require "Controllers/Conexion.php";

header('Content-Type: application/json');

// Configurar conexión para usar UTF-8
$mysqli->set_charset("utf8");

$termino = isset($_GET['termino']) ? $mysqli->real_escape_string(trim($_GET['termino'])) : '';

if(strlen($termino) < 1) {
    echo json_encode([]);
    exit;
}

// Búsqueda mejorada que busca en código, descripción y categoría
$sql = "SELECT id, codigo, descripcion, precio 
        FROM productos
        WHERE codigo LIKE '%$termino%' 
        OR descripcion LIKE '%$termino%'
        LIMIT 10";

$result = $mysqli->query($sql);

if(!$result) {
    echo json_encode(['error' => $mysqli->error]);
    exit;
}

$productos = [];
while($row = $result->fetch_assoc()) {
    $productos[] = [
        'id' => $row['id'],
        'codigo' => $row['codigo'],
        'descripcion' => $row['descripcion'],
        'precio' => number_format($row['precio'], 2),
    ];
}

echo json_encode($productos);
?>