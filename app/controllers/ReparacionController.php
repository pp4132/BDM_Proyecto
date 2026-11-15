
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/InicioSesion.html");
    exit();
}

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../model/Reparacion.php';
require_once __DIR__ . '/../../model/Siniestro.php';

$idUsuario = (int)$_SESSION['usuario_id'];
$rol = $_SESSION['Rol'] ?? '';

$idSiniestro = (int)($_POST['idSiniestro'] ?? 0);
$accion = $_POST['accion'] ?? '';

try {

    if (!in_array($rol, ['Supervisor', 'Ajustador'], true)) {
        throw new Exception("No tienes permisos para gestionar reparaciones.");
    }

    if ($idSiniestro <= 0) {
        throw new Exception("Identificador de siniestro inválido.");
    }

    // Comprobar acceso al siniestro
    $siniestroModel = new Siniestro($conexion);

    $siniestro = $siniestroModel->obtenerDetalle(
        $idSiniestro,
        $rol,
        $idUsuario
    );

    if (!$siniestro) {
        throw new Exception("No tienes acceso a este siniestro.");
    }

    $reparacionModel = new Reparacion($conexion);

    // CREAR REPARACIÓN
    if ($accion === 'crear') {

        $taller = trim($_POST['taller'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $costo = $_POST['costo'] ?? '';

        if ($taller === '' || $descripcion === '' || !is_numeric($costo)) {
            throw new Exception("Completa correctamente los datos.");
        }

        if ((float)$costo < 0) {
            throw new Exception("El costo no puede ser negativo.");
        }

        $datos = [
            'IdSiniestro' => $idSiniestro,
            'IdUsuarioCreador' => $idUsuario,
            'DescripcionTrabajo' => $descripcion,
            'Taller' => $taller,
            'FechaInicio' => null,
            'FechaFin' => null,
            'CostoFinal' => (float)$costo
        ];

        $resultado = $reparacionModel->registrar($datos);

        if (!$resultado) {
            throw new Exception("No se pudo registrar la reparación.");
        }

        $_SESSION['reparacion_mensaje'] =
            "Reparación registrada correctamente.";

    }

    // ACTUALIZAR ESTADO
    elseif ($accion === 'actualizar') {

        if ($rol !== 'Supervisor') {
            throw new Exception("No tienes permisos para actualizar el estado.");
        }

        $idReparacion = (int)($_POST['idReparacion'] ?? 0);
        $estado = $_POST['estado'] ?? '';

        $estadosPermitidos = [
            'Pendiente',
            'En proceso',
            'Finalizada'
        ];

        if ($idReparacion <= 0 ||
            !in_array($estado, $estadosPermitidos, true)) {
            throw new Exception("Datos de actualización inválidos.");
        }

        // Comprobar que la reparación pertenece al siniestro
        $reparaciones = $reparacionModel->listarPorSiniestro($idSiniestro);

        $pertenece = false;

        foreach ($reparaciones as $reparacion) {
            if ((int)$reparacion['IdReparacion'] === $idReparacion) {
                $pertenece = true;
                break;
            }
        }

        if (!$pertenece) {
            throw new Exception("La reparación no pertenece a este siniestro.");
        }

        $reparacionModel->actualizarEstado(
            $idReparacion,
            $estado
        );

        $_SESSION['reparacion_mensaje'] =
            "Estado de reparación actualizado correctamente.";

    } else {

        throw new Exception("Acción no válida.");

    }

} catch (Throwable $e) {

    $_SESSION['reparacion_error'] = $e->getMessage();

}

$conexion->close();

header("Location: ../controllers/DetalleSiniestroController.php?id=" . $idSiniestro);
exit();

?>
