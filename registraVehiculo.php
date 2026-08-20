<?php

session_start();
include("conexion.php");

/* =========================================
   VALIDAR SESION
========================================= */

if (!isset($_SESSION['usuario_id'])) {

    header("Location: InicioSesion.html");
    exit();
}

/* =========================================
   ID ASEGURADO
========================================= */

$idAsegurado = $_SESSION['usuario_id'];

/* =========================================
   DATOS FORMULARIO
========================================= */

$tipo = trim($_POST['tipo'] ?? '');
$marca = trim($_POST['marca'] ?? '');
$modelo = trim($_POST['modelo'] ?? '');
$anio = trim($_POST['anio'] ?? '');
$placas = trim($_POST['placas'] ?? '');
$numeroPoliza = trim($_POST['numeroPoliza'] ?? '');
$numeroSerie = trim($_POST['numeroSerie'] ?? '');

/* =========================================
   VALIDACIONES
========================================= */

$errores = [];

/* Tipo */
if (empty($tipo)) {
    $errores[] = "El tipo es obligatorio";
}

/* Marca */
if (empty($marca)) {
    $errores[] = "La marca es obligatoria";
}

/* Modelo */
if (empty($modelo)) {
    $errores[] = "El modelo es obligatorio";
}

/* Año */
if ($anio < 1950 || $anio > date('Y') + 1) {
    $errores[] = "Año inválido";
}

/* Placas */
if (empty($placas)) {
    $errores[] = "Las placas son obligatorias";
}

/* =========================================
   SI HAY ERRORES
========================================= */

if (!empty($errores)) {

    $_SESSION['errores_vehiculo'] = $errores;
    header("Location: PanelUsuario.php");
    exit();
}

/* =========================================
   PROCEDURE
========================================= */

$opcion = 1;
$idVehiculo = 0;

/* =========================================
   PREPARE
========================================= */

$stmt = $conexion->prepare(
    "CALL sp_vehiculos(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {

    die("Error prepare: " . $conexion->error);
}

/* =========================================
   BIND
========================================= */

$stmt->bind_param(
    "iiisssisss",

    $opcion,
    $idVehiculo,
    $idAsegurado,
    $tipo,
    $marca,
    $modelo,
    $anio,
    $placas,
    $numeroPoliza,
    $numeroSerie
);

/* =========================================
   EXECUTE
========================================= */

if ($stmt->execute()) {

    while ($conexion->more_results() && $conexion->next_result()) {
        if ($resultado = $conexion->store_result()) {
            $resultado->free();
        }
    }

    header("Location: PanelUsuario.php?vehiculo=ok");
    exit();

} else {

    die("Error execute: " . $stmt->error);
}

$stmt->close();
$conexion->close();

?>