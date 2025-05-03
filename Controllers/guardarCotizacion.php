<?php
session_start();

// Incluir el archivo de conexión
include "Conexion.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION['user'])) {
    header("Location: ../interfaces/index.php");
    exit;
}

// Verificar que la conexión existe
if (!$mysqli) {
    $_SESSION['error'] = "Error de conexión a la base de datos";
    header("Location: ../interfaces/CrearCotizacion.php");
    exit;
}

// Obtener datos del formulario
$cliente = $_POST['cliente'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];
$tipo_documento = $_POST['tipo_documento'];
$numero_documento = $_POST['numero_documento'];
$tipo_operacion = $_POST['tipo_operacion'];
$total = $_POST['total'];
$condiciones = $_POST['condiciones'];
$forma_pago = $_POST['forma_pago'];
$validez = $_POST['validez'];
$cuenta = $_POST['cuenta'];
$productos_json = $_POST['productos_json'];
$usuario = $_SESSION['user'];

// Insertar en gestionCotizacion
$sql = "INSERT INTO gestionCotizacion (
    cliente, 
    fecha, 
    hora,
    tipo_documento,
    numero_documento,
    tipo_operacion,
    total,
    condiciones,
    forma_pago,
    validez,
    cuenta,
    productos,
    usuario,
    estado
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendiente')";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    $_SESSION['error'] = "Error al preparar la consulta: ".$mysqli->error;
    header("Location: ../interfaces/CrearCotizacion.php");
    exit;
}

$stmt->bind_param(
    "ssssssdssisss", 
    $cliente,
    $fecha,
    $hora,
    $tipo_documento,
    $numero_documento,
    $tipo_operacion,
    $total,
    $condiciones,
    $forma_pago,
    $validez,
    $cuenta,
    $productos_json,
    $usuario
);

if ($stmt->execute()) {
    $_SESSION['success'] = "Cotización guardada correctamente (ID: ".$mysqli->insert_id.")";
    header("Location: ../interfaces/gestionCotizacion.php?highlight=".$mysqli->insert_id);
    exit;
} else {
    $_SESSION['error'] = "Error al guardar la cotización: ".$stmt->error;
    header("Location: ../interfaces/CrearCotizacion.php");
    exit;
}

$stmt->close();
$mysqli->close();
?>