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
// VALIDAR IDS
// =========================
$idReparacion = $_POST['idReparacion'] ?? 0;
$idSiniestro = $_POST['idSiniestro'] ?? 0;

if (!is_numeric($idReparacion) || $idReparacion <= 0) {
    die("Reparación inválida");
}

// =========================
// IMPORTANTE PARA TRIGGER
// =========================
$conexion->query(
    "SET @idUsuario = " . (int)$usuario['IdUsuario']
);

// =========================
// SUPERVISOR
// =========================
if ($usuario['Rol'] === 'Supervisor') {

    $estado = trim($_POST['estado'] ?? '');

    if (empty($estado)) {
        die("Estado inválido");
    }

    $opcion = 2;

    $stmt = $conexion->prepare("
        CALL sp_reparaciones(
            ?, ?, 0, 0,
            '', '', NULL, NULL,
            0, ?
        )
    ");

    if (!$stmt) {
        die($conexion->error);
    }

    $stmt->bind_param(
        "iis",
        $opcion,
        $idReparacion,
        $estado
    );

}

// =========================
// AJUSTADOR
// =========================
elseif ($usuario['Rol'] === 'Ajustador') {

    $taller = trim($_POST['taller'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $costo = $_POST['costo'] ?? 0;

    if (empty($taller)) {
        die("Taller obligatorio");
    }

    if (empty($descripcion)) {
        die("Descripción obligatoria");
    }

    if (!is_numeric($costo) || $costo < 0) {
        die("Costo inválido");
    }

    $opcion = 5;

    $stmt = $conexion->prepare("
        CALL sp_reparaciones(
            ?, ?, 0, 0,
            ?, ?, NULL, NULL,
            ?, '')
    ");

    if (!$stmt) {
        die($conexion->error);
    }

    $stmt->bind_param(
        "iissd",
        $opcion,
        $idReparacion,
        $descripcion,
        $taller,
        $costo
    );

}

// =========================
// EJECUTAR
// =========================
if (!$stmt->execute()) {

    die("Error al actualizar: " . $stmt->error);
}

// limpiar resultados
while ($conexion->more_results() && $conexion->next_result()) {

    $extra = $conexion->store_result();

    if ($extra instanceof mysqli_result) {
        $extra->free();
    }
}

$stmt->close();

// =========================
// REDIRECCIONAR
// =========================
header("Location: Siniestros.php?id=" . $idSiniestro);
exit();
?>