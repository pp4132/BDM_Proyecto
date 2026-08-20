<?php

session_start();
include("conexion.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: InicioSesion.html");
    exit();
}

$idUsuario = $_SESSION['usuario_id'];

/* =========================================================
   DATOS DEL FORMULARIO
========================================================= */

$nombre = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

/* =========================================================
   OBTENER DATOS ACTUALES
========================================================= */

$sql = "SELECT 
            IdRol,
            Alias,
            FechaNacimiento,
            Genero,
            Email,
            Contrasena,
            Foto
        FROM usuarios
        WHERE IdUsuario = ?";

$stmtDatos = $conexion->prepare($sql);
$stmtDatos->bind_param("i", $idUsuario);
$stmtDatos->execute();

$resultado = $stmtDatos->get_result();
$datosActuales = $resultado->fetch_assoc();

if (!$datosActuales) {
    die("Usuario no encontrado");
}

/* =========================================================
   CONSERVAR DATOS EXISTENTES
========================================================= */

$idRol = $datosActuales['IdRol'];
$alias = $datosActuales['Alias'];
$fechaNacimiento = $datosActuales['FechaNacimiento'];
$genero = $datosActuales['Genero'];
$email = $datosActuales['Email'];
$contrasena = $datosActuales['Contrasena'];

/* =========================================================
   FOTO
========================================================= */

$foto = $datosActuales['Foto'];

if (isset($_FILES['foto']) && $_FILES['foto']['tmp_name'] != '') {

    $foto = file_get_contents($_FILES['foto']['tmp_name']);
}

/* =========================================================
   OPCION UPDATE
========================================================= */

$opcion = 2;

/* =========================================================
   LLAMADA AL PROCEDURE
========================================================= */

$stmt = $conexion->prepare(
    "CALL sp_usuarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    die("Error prepare: " . $conexion->error);
}

$stmt->bind_param(
    "iiissssbssss",
    $opcion,
    $idUsuario,
    $idRol,
    $nombre,
    $apellidos,
    $alias,
    $fechaNacimiento,
    $foto,
    $genero,
    $email,
    $contrasena,
    $telefono
);

/* =========================================================
   ENVIAR BLOB
========================================================= */

if ($foto !== null) {
    $stmt->send_long_data(7, $foto);
}

/* =========================================================
   EJECUTAR
========================================================= */

if ($stmt->execute()) {

    header("Location: PanelUsuario.php?updated=1");
    exit();

} else {

    die("Error execute: " . $stmt->error);
}

$stmt->close();
$conexion->close();

?>