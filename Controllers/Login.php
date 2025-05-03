<?php
session_start(); 
include "../Controllers/Conexion.php";

if (isset($_POST['submit'])) {
    $usuario = trim($_POST['user']);
    $contraseña = trim($_POST['password']);

    if (empty($usuario) || empty($contraseña)) {
        echo "Los campos usuario y contraseña no pueden estar vacíos";
        exit;
    }

    $stmt = $mysqli->prepare("SELECT ID, contraseña FROM USUARIOS WHERE usuario = ?");
    $stmt->bind_param('s', $usuario);  // 's' es para string
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($ID, $hashed_password);
        $stmt->fetch();

        if (password_verify($contraseña, $hashed_password)) {
            $_SESSION['user'] = $usuario; // Guardamos el nombre de usuario en la sesión
            header("Location: ../interfaces/dashboard.php");
            exit;
        } else {
            echo "La contraseña es inválida";
        }
    } else {
        echo "El usuario no existe";
    }

    $stmt->close();
}

$mysqli->close();

?>