<?php

$host = "localhost:3307";
$password = "bilashdfg6882hf.S";
$user = "root";
$database = "bd_siniestros";

$conexion = new mysqli(
    $host,
    $user,
    $password,
    $database
);

if ($conexion->connect_error) {
    error_log("Error de conexión: " . $conexion->connect_error);
    die("No se pudo conectar con la base de datos.");
}

$conexion->set_charset("utf8mb4");


?>