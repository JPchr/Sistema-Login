<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "control_escolar";

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>
