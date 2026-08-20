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
   USUARIO LOGUEADO
========================================= */

$idAsegurado = $_SESSION['usuario_id'];

/* =========================================
   DATOS FORMULARIO
========================================= */

$idVehiculo = $_POST['idVehiculo'] ?? 0;

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

/* ID Vehículo */
if (!is_numeric($idVehiculo) || $idVehiculo <= 0) {
    $errores[] = "Vehículo inválido";
}

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
if (!is_numeric($anio) || $anio < 1950 || $anio > date('Y') + 1) {
    $errores[] = "Año inválido";
}

/* Placas */
if (empty($placas)) {
    $errores[] = "Las placas son obligatorias";
}

/* =========================================
   VALIDAR QUE EL VEHICULO PERTENEZCA
   AL USUARIO LOGUEADO
========================================= */

$stmtValidar = $conexion->prepare("
    SELECT IdVehiculo
    FROM vw_vehiculos_usuario
    WHERE IdVehiculo = ?
    AND IdAsegurado = ?
");

$stmtValidar->bind_param(
    "ii",
    $idVehiculo,
    $idAsegurado
);

$stmtValidar->execute();

$resultado = $stmtValidar->get_result();

if ($resultado->num_rows == 0) {

    $errores[] = "No tienes permisos sobre este vehículo";
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
   OPCION UPDATE
========================================= */

$opcion = 2;

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

    header("Location: PanelUsuario.php?actualizado=1");
    exit();

} else {

    die("Error execute: " . $stmt->error);
}

$stmt->close();
$conexion->close();

?>