<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'agregar':
                $codigo = $mysqli->real_escape_string($_POST['codigo']);
                $descripcion = $mysqli->real_escape_string($_POST['descripcion']);
                $precio = floatval($_POST['precio']);
                
                $stmt = $mysqli->prepare("INSERT INTO productos (codigo, descripcion, precio) VALUES (?, ?, ?)");
                $stmt->bind_param("ssd", $codigo, $descripcion, $precio);
                $stmt->execute();
                break;
                
            case 'editar':
                $id = intval($_POST['id']);
                $codigo = $mysqli->real_escape_string($_POST['codigo']);
                $descripcion = $mysqli->real_escape_string($_POST['descripcion']);
                $precio = floatval($_POST['precio']);
                
                $stmt = $mysqli->prepare("UPDATE productos SET codigo = ?, descripcion = ?, precio = ? WHERE id = ?");
                $stmt->bind_param("ssdi", $codigo, $descripcion, $precio, $id);
                $stmt->execute();
                break;
                
            case 'eliminar':
                $id = intval($_POST['id']);
                $stmt = $mysqli->prepare("DELETE FROM productos WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                break;
        }
    }
    header("Location: GestionProductos.php");
    exit;
}

// Obtener productos
$result = $mysqli->query("SELECT * FROM productos ORDER BY id DESC");
$productos = $result->fetch_all(MYSQLI_ASSOC);
?>