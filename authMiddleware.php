<?php
session_start();    
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php"); // Si no hay sesión, redirige al login
    exit;
}   
?>