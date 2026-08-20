<?php
$host = "localhost:3307";
$user = "root";
$password = "bilashdfg6882hf.S";
$database = "bd_siniestros";

$conexion = new mysqli($host, $user, $password, $database);

$conexion->set_charset("utf8mb4");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>