<?php
require "Controllers/Conexion.php";
require "authMiddleware.php";   
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ElectroTop</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">    
    <link rel="stylesheet" href="Styles/index.css">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="logo-container">
                <img src="assets/logo1-original.png" alt="ElectroTop Logo" class="logo-img">
            </div>
            <form action="Login.php" method="post">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="user" id="user" placeholder="Ingrese su Usuario" required>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Ingrese su Contraseña" required>
                    </div>
                </div>
                <button type="submit" name="submit" class="btn btn-primary btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> Ingresar
                </button>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Add some 3D hover effect to the logo -->
    <script src="Js/index.js"></script>
</body>
</html>