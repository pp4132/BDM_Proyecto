<?php
include("sesion.php");

// =========================
// VALIDAR SESION
// =========================
if (!isset($_SESSION['usuario_id'])) {
    header("Location: Index.html");
    exit();
}

$idUsuario = $_SESSION['usuario_id'];
$rol = $usuario['Rol'];

// =========================
// RECIBIR DATOS DEL FORMULARIO
// =========================
$idSiniestro = $_POST['idSiniestro'] ?? 0;
$tipo = trim($_POST['tipo'] ?? '');
$estado = trim($_POST['estado'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$ubicacion = trim($_POST['ubicacion'] ?? '');
$montoEstimado = $_POST['montoEstimado'] ?? 0;
$montoAprobado = $_POST['montoAprobado'] ?? 0;

// =========================
// VALIDACIONES BASICAS
// =========================
$errores = [];

if (!is_numeric($idSiniestro) || $idSiniestro <= 0) $errores[] = "Siniestro inválido";
if (empty($tipo)) $errores[] = "Tipo obligatorio";
if ($rol === 'Supervisor' && empty($estado)) $errores[] = "Estado obligatorio para Supervisor";

if (!empty($errores)) {
    $mensaje = urlencode(implode("|", $errores));
    header("Location: Siniestros.php?id=$idSiniestro&error=$mensaje");
    exit();
}

// =========================
// OBTENER VEHICULO DEL SINIESTRO
// =========================
$stmtVeh = $conexion->prepare("
    SELECT IdVehiculo
    FROM vw_siniestros_acceso
    WHERE IdSiniestro = ?
");
$stmtVeh->bind_param("i", $idSiniestro);
$stmtVeh->execute();
$resultVeh = $stmtVeh->get_result()->fetch_assoc();
$stmtVeh->close();

if (!$resultVeh) {
    die("Siniestro no encontrado");
}

$idVehiculo = $resultVeh['IdVehiculo'];

// =========================
// AJUSTAR MONTOS SEGUN ROL
// =========================
if ($rol === 'Ajustador') {
    $montoAprobado = null; // Ajustador no puede tocar este campo
}

// =========================
// SUPERVISOR
// =========================
if ($usuario['Rol'] === 'Supervisor') {

    $estado = $_POST['estado'] ?? '';
    $montoAprobado = $_POST['montoAprobado'] ?? 0;
    $opcion = 3;

    $stmt = $conexion->prepare("
        CALL sp_siniestros(
            ?, 
            ?, 
            0, 
            0, 
            0,
            '', 
            ?, 
            '', 
            NOW(), 
            '', 
            0, 
            ?)
    ");

    $stmt->bind_param(
        "iisi",
        $opcion,
        $idSiniestro,
        $estado,
        $montoAprobado
    );
}
// =========================
// AJUSTADOR
// =========================
elseif ($usuario['Rol'] === 'Ajustador') {
    $opcion = 2;

    $stmt = $conexion->prepare("
        CALL sp_siniestros(
            ?,
            ?, 
            ?, 
            0, 
            0,
            ?, 
            ?, 
            ?,
            NOW(), 
            ?, 
            ?, 
            ?
        )
    ");

    $stmt->bind_param(
        "iiissssdd",
        $opcion,
        $idSiniestro,
        $idVehiculo,
        $tipo,
        $estado,
        $descripcion,
        $ubicacion,
        $montoEstimado,
        $montoAprobado
    );
}

if (!$stmt->execute()) {
    die("Error al actualizar el siniestro: " . $stmt->error);
}

// limpiar resultados pendientes
while ($conexion->more_results() && $conexion->next_result()) {
    $extra = $conexion->store_result();
    if ($extra instanceof mysqli_result) $extra->free();
}

$stmt->close();
$conexion->close();

// =========================
// REDIRECCIONAR
// =========================
header("Location: Siniestros.php?id=" . $idSiniestro . "&actualizado=1");
exit();
?>