<?php
session_start();    
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}       
$usuario = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hikvision</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --success-color: #198754;
            --danger-color: #dc3545;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
        }
        
        .card-feature {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
        }
        
        .card-feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-feature .bi {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .logout-btn {
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background-color: #bb2d3b !important;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="dashboard-header">
        <div class="container text-center">
            <div class="user-avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <h1 class="mb-2">Bienvenido, <?php echo htmlspecialchars($usuario); ?></h1>
            <p class="lead opacity-75">Panel de gestión integral Hikvision</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mb-5">
        <div class="feature-grid">
            <!-- Crear Cotización -->
            <div class="card card-feature text-center p-4">
                <i class="bi bi-file-earmark-plus-fill"></i>
                <h3>Nueva Cotización</h3>
                <p class="text-muted">Crea una nueva cotización para clientes</p>
                <a href="crear_cotizacion.php" class="btn btn-primary mt-3">Acceder</a>
            </div>
            
            <!-- Gestión de Productos -->
            <div class="card card-feature text-center p-4">
                <i class="bi bi-box-seam"></i>
                <h3>Productos</h3>
                <p class="text-muted">Administra el catálogo de productos</p>
                <a href="productos.php" class="btn btn-outline-dark mt-3">Acceder</a>
            </div>
            
            <!-- Gestión de Cotizaciones -->
            <div class="card card-feature text-center p-4">
                <i class="bi bi-file-earmark-text"></i>
                <h3>Cotizaciones</h3>
                <p class="text-muted">Administra tus cotizaciones</p>
                <a href="cotizaciones.php" class="btn btn-outline-primary mt-3">Acceder</a>
            </div>
            
            <!-- Gestión de Ventas -->
            <div class="card card-feature text-center p-4">
                <i class="bi bi-cash-coin"></i>
                <h3>Ventas</h3>
                <p class="text-muted">Registro y seguimiento de ventas</p>
                <a href="ventas.php" class="btn btn-outline-success mt-3">Acceder</a>
            </div>
        </div>
        
        <!-- Logout Button -->
        <div class="text-center mt-5">
            <a href="logout.php" class="btn btn-danger btn-lg logout-btn px-4">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </div>
    </main>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>