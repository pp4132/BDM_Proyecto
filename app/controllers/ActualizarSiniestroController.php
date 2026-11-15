
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/InicioSesion.html");
    exit();
}

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../model/Siniestro.php';

$idUsuario = (int)$_SESSION['usuario_id'];
$rol = $_SESSION['Rol'] ?? '';

$idSiniestro = (int)($_POST['idSiniestro'] ?? 0);

try {

    if (!in_array($rol, ['Supervisor', 'Ajustador'], true)) {
        throw new Exception("No tienes permisos para actualizar siniestros.");
    }

    if ($idSiniestro <= 0) {
        throw new Exception("Identificador de siniestro inválido.");
    }

    $modelo = new Siniestro($conexion);

    // Verificar que el usuario tenga acceso al siniestro
    $siniestro = $modelo->obtenerDetalle(
        $idSiniestro,
        $rol,
        $idUsuario
    );

    if (!$siniestro) {
        throw new Exception("No tienes acceso a este siniestro.");
    }

    $datos = [
        'idSiniestro' => $idSiniestro,
        'tipo' => trim($_POST['tipo'] ?? ''),
        'estado' => trim($_POST['estado'] ?? ''),
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'ubicacion' => trim($_POST['ubicacion'] ?? ''),
        'montoEstimado' => $_POST['montoEstimado'] ?? 0,
        'montoAprobado' => $_POST['montoAprobado'] ?? 0
    ];

    if ($datos['montoEstimado'] < 0 ||
        $datos['montoAprobado'] < 0) {
        throw new Exception("Los montos no pueden ser negativos.");
    }

    if ($rol === 'Ajustador') {

        if ($datos['tipo'] === '' ||
            $datos['descripcion'] === '') {
            throw new Exception("Completa los datos del siniestro.");
        }

    }

    if ($rol === 'Supervisor') {

        $estadosPermitidos = [
            'Pendiente',
            'Rechazado',
            'Aceptado',
            'Aceptado con pago de deducible',
            'Aceptado sin pago de deducible',
            'Aplica pago para reparación de la unidad',
            'Pérdida total'
        ];

        if (!in_array($datos['estado'], $estadosPermitidos, true)) {
            throw new Exception("Estado de siniestro no válido.");
        }

    }

    $modelo->actualizar($datos, $rol);

    $_SESSION['siniestro_mensaje'] =
        "Siniestro actualizado correctamente.";

} catch (Throwable $e) {

    $_SESSION['siniestro_error'] = $e->getMessage();

}

$conexion->close();

header("Location: ../controllers/DetalleSiniestroController.php?id=" . $idSiniestro);
exit();

?>
