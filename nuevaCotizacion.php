<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

include "Controllers/Conexion.php";

// Procesar el formulario de cotización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar datos recibidos
    $required_fields = ['cliente', 'fecha', 'tipo_documento', 'numero_documento', 'tipo_operacion', 'productos_json'];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "El campo " . str_replace('_', ' ', $field) . " es requerido";
            header("Location: CrearCotizacion.php");
            exit;
        }
    }
    
    // Procesar productos
    $productos = json_decode($_POST['productos_json'], true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($productos)) {
        $_SESSION['error'] = "Error al procesar los productos de la cotización";
        header("Location: CrearCotizacion.php");
        exit;
    }
    
    // Calcular total
    $total = 0;
    foreach ($productos as $producto) {
        $total += floatval($producto['total']);
    }
    
    // Insertar cotización
    $stmt = $mysqli->prepare("INSERT INTO cotizaciones (
        usuario_id, 
        cliente, 
        fecha, 
        hora,
        tipo_documento, 
        numero_documento, 
        tipo_operacion,
        direccion,
        email,
        ruc,
        celular,
        condiciones,
        forma_pago,
        validez,
        cuenta,
        total,
        estado
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDIENTE')");
    
    $stmt->bind_param("issssssssssssssd", 
        $_SESSION['user']['id'],
        $_POST['cliente'],
        $_POST['fecha'],
        $_POST['hora'],
        $_POST['tipo_documento'],
        $_POST['numero_documento'],
        $_POST['tipo_operacion'],
        $_POST['direccion'],
        $_POST['email'],
        $_POST['ruc'],
        $_POST['celular'],
        $_POST['condiciones'],
        $_POST['forma_pago'],
        $_POST['validez'],
        $_POST['cuenta'],
        $total
    );
    
    if ($stmt->execute()) {
        $cotizacion_id = $stmt->insert_id;
        
        // Insertar productos de la cotización
        $stmt_productos = $mysqli->prepare("INSERT INTO cotizacion_productos (
            cotizacion_id,
            producto_id,
            codigo,
            descripcion,
            precio,
            cantidad,
            total,
            notas
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($productos as $producto) {
            $stmt_productos->bind_param("isssddds",
                $cotizacion_id,
                $producto['id'],
                $producto['codigo'],
                $producto['descripcion'],
                $producto['precio'],
                $producto['cantidad'],
                $producto['total'],
                $producto['notas'] ?? ''
            );
            $stmt_productos->execute();
        }
        
        $_SESSION['success'] = "Cotización #$cotizacion_id guardada correctamente";
        header("Location: GestionCotizaciones.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al guardar la cotización: " . $stmt->error;
        header("Location: CrearCotizacion.php");
        exit;
    }
} else {
    header("Location: CrearCotizacion.php");
    exit;
}
?>