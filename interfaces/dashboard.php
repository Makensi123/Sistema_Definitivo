<?php
session_start(); 
if(!isset($_SESSION['user'])){
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
    <title>Dashboard Premium - Hikvision</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <link rel="stylesheet" href="../Styles/Dashboard.css">
</head>
<body>
    <!-- Premium Header -->
    <header class="dashboard-header text-center text-white">
        <div class="container">
            <div class="logo-container animate__animated animate__fadeInDown">
                <img src="../assets/logo1-original.png" alt="Hikvision Premium" class="logo-3d">
            </div>
            <h1 class="user-greeting animate__animated animate__fadeIn animate__delay-1s">Bienvenido, <span class="fw-bold"><?php echo htmlspecialchars($usuario); ?></span></h1>
            <p class="lead animate__animated animate__fadeIn animate__delay-1s">Panel de Control Premium Hikvision</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mb-5 animate__animated animate__fadeIn animate__delay-2s">
        <div class="feature-grid">
            <!-- Crear Cotización -->
            <div class="card card-feature text-center p-4">
                <i class="bi bi-file-earmark-plus-fill"></i>
                <h3 class="fw-bold">Nueva Cotización</h3>
                <p class="text-muted mb-4">Crea cotizaciones profesionales en minutos</p>
                <a href="../interfaces/CrearCotizacion.php" class="btn btn-premium">
                    <i class="bi bi-arrow-right-circle me-2"></i>Acceder
                </a>
            </div>
            
            <!-- Gestión de Productos -->
            <div class="card card-feature text-center p-4">
                <i class="bi bi-box-seam"></i>
                <h3 class="fw-bold">Gestión de Productos</h3>
                <p class="text-muted mb-4">Administra tu catálogo completo</p>
                <a href="../interfaces/GestionProductos.php" class="btn btn-premium">
                    <i class="bi bi-arrow-right-circle me-2"></i>Acceder
                </a>
            </div>
            
            <!-- Gestión de Cotizaciones -->
            <div class="card card-feature text-center p-4">
                <i class="bi bi-file-earmark-text"></i>
                <h3 class="fw-bold">Cotizaciones</h3>
                <p class="text-muted mb-4">Consulta y gestiona tus presupuestos</p>
                <a href="gestionCotizacion.php" class="btn btn-premium">
                    <i class="bi bi-arrow-right-circle me-2"></i>Acceder
                </a>
            </div>
            
            <!-- Gestión de Ventas -->
            <div class="card card-feature text-center p-4">
                <i class="bi bi-cash-coin"></i>
                <h3 class="fw-bold">Gestión de Ventas</h3>
                <p class="text-muted mb-4">Seguimiento de transacciones</p>
                <a href="gestionVentas.php" class="btn btn-premium">
                    <i class="bi bi-arrow-right-circle me-2"></i>Acceder
                </a>
            </div>
        </div>
        
        <!-- Logout Button -->
        <div class="text-center mt-5 pt-4">
            <a href="../middleware/logout.php" class="btn btn-danger btn-lg logout-btn px-4 fw-bold">
                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
            </a>
        </div>
    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../Js/dashboard.js"></script>
</body>
</html>