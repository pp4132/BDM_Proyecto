<?php
include("sesion.php");

// =========================
// VALIDAR ROL
// =========================
if (!in_array($usuario['Rol'], ['Supervisor','Ajustador'])) {

    header("Location: PanelUsuario.php");
    exit();
}

// =========================
// DATOS FORMULARIO
// =========================
$idSiniestro = $_POST['idSiniestro'] ?? 0;
$taller = trim($_POST['taller'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$costo = $_POST['costo'] ?? 0;

// =========================
// VALIDACIONES
// =========================
if (!is_numeric($idSiniestro) || $idSiniestro <= 0) {
    die("Siniestro inválido");
}

if (empty($taller)) {
    die("Taller obligatorio");
}

if (empty($descripcion)) {
    die("Descripción obligatoria");
}

if (!is_numeric($costo) || $costo < 0) {
    die("Costo inválido");
}

// =========================
// VARIABLES
// =========================
$opcion = 1;
$idReparacion = 0;
$IdUsuarioCreador =$idUsuario;
$fechaInicio = NULL;
$fechaFin = NULL;

$estado = "pendiente";

// =========================
// TRIGGER HISTORIAL
// =========================
$conexion->query(
    "SET @idUsuario = " . (int)$usuario['IdUsuario']
);

// =========================
// INSERTAR REPARACION
// =========================
$stmt = $conexion->prepare("
    CALL sp_reparaciones(
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )
");

$stmt->bind_param(
    "iiiissssds",
    $opcion,
    $idReparacion,
    $idSiniestro,
    $IdUsuarioCreador,
    $descripcion,
    $taller,
    $fechaInicio,
    $fechaFin,
    $costo,
    $estado
);


if (!$stmt->execute()) {

    die("Error al crear reparación: " . $stmt->error);
}

$stmt->close();

// limpiar resultados pendientes
while ($conexion->more_results() && $conexion->next_result()) {

    $extra = $conexion->store_result();

    if ($extra instanceof mysqli_result) {
        $extra->free();
    }
}

// =========================
// REDIRECCIONAR
// =========================
header("Location: Siniestros.php?id=" . $idSiniestro);
exit();
?>