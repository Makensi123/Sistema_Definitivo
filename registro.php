<?php
require "Controllers/Conexion.php";
require "authMiddleware.php"   
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ElectroTop</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
            color: #0d6efd;
            font-weight: bold;
        }
        .btn-login {
            width: 100%;
        }
    </style>        
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h1 class="logo">ElectroTop</h1>
            <form action="Login.php" method="post">
                <div class="mb-3">
                    <label for="user" class="form-label">Usuario</label>
                    <input type="text" class="form-control" name="user" id="user" placeholder="Ingrese su Usuario" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Ingrese su Contraseña" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary btn-login">Ingresar</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>