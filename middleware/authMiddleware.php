<?php
session_start();    
if (isset($_SESSION['user'])) {
    header("Location: ../interfaces/dashboard.php");
    exit;
}   
?>