<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "electrotop";

$mysqli = new mysqli($host, $user, $password, $dbname);
if($mysqli->connect_errno) {
    die("Fallo al conectar a la base de datos ElectroTop (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}
?>