<?php   
$host = "localhost";
$user = "root";
$password = "proverbios";
$dbname = "electrotop";

$mysqly = new mysqli($host, $user, $password, $dbname);
if($mysqly->connect_errno) {
    echo "Fallo al conectar a la base de datos ElectroTop (" .$mysqly->connect_errno . ") " .$mysqly->connect_error;
}

?>