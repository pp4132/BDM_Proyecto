<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../model/Usuario.php';
require_once __DIR__ . '/../../model/Vehiculo.php';
require_once __DIR__ . '/../../model/Siniestro.php';
require_once __DIR__ . '/../../model/Multimedia.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../view/InicioSesion.html");
        exit();
    }

    $idUsuario = (int) $_SESSION['usuario_id'];


    $usuarioModel = new Usuario($conexion);

    $usuario = $usuarioModel->obtenerPorId($idUsuario);

    if (!$usuario) {
        $_SESSION = [];
        session_destroy();

        header("Location: ../view/InicioSesion.html");
        exit();
    }

    $rol = $usuario['Rol'];

    $_SESSION['Rol'] = $rol;

    if ($rol !== 'Supervisor' && $rol !== 'Ajustador') {
        header("Location: ../view/panel.php");
        exit();
    }

    $vehiculoModel = new Vehiculo($conexion);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $vehiculosArray =
            $vehiculoModel->listarDisponiblesParaSiniestro();

        require __DIR__ . '/../view/CrearSiniestros.php';
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit("Método no permitido.");
    }

    $siniestroModel = new Siniestro($conexion);
    $multimediaModel = new Multimedia($conexion);

    $fecha = trim($_POST['fecha'] ?? '');
    $idVehiculo = (int) ($_POST['IdVehiculo'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');

    $errores = [];

    if ($idVehiculo <= 0) {
        $errores[] = "Vehículo requerido.";
    }

    if ($descripcion === '') {
        $errores[] = "Descripción requerida.";
    }

    if ($tipo === '') {
        $errores[] = "Tipo requerido.";
    }

    $fechaObjeto = DateTime::createFromFormat('Y-m-d', $fecha);

    if (!$fechaObjeto || $fechaObjeto->format('Y-m-d') !== $fecha) {
        $errores[] = "Fecha inválida.";
    }

    /*VALIDAR VEHÍCULO*/

    if ($idVehiculo > 0) {

        if (!$siniestroModel->validarVehiculo($idVehiculo)) {
            $errores[] = "Vehículo inválido.";
        }
    }

    /*VALIDAR ARCHIVOS*/

    $archivos = $_FILES['archivos'] ?? null;

    if ($archivos && isset($archivos['name'])) {

        $cantidad = count(array_filter(
            $archivos['name'],
            fn($nombre) => $nombre !== ''
        ));

        if ($cantidad > 5) {
            $errores[] = "Máximo 5 archivos.";
        }

        foreach ($archivos['error'] as $error) {

            if ($error !== UPLOAD_ERR_OK &&
                $error !== UPLOAD_ERR_NO_FILE) {

                $errores[] = "Error al subir uno de los archivos.";
                break;
            }
        }
    }

    if (!empty($errores)) {

        $_SESSION['errores_siniestro'] = $errores;

        header("Location: ../controllers/SiniestroController.php");
        exit();
    }

    $conexion->begin_transaction();

    $datos = [
        'opcion' => 1,
        'idSiniestro' => 0,
        'idVehiculo' => $idVehiculo,
        'idAjustador' => $idUsuario,
        'idnoseusa' => 0,
        'tipo' => $tipo,
        'estado' => 'pendiente',
        'descripcion' => $descripcion,
        'fecha' => $fecha . ' 00:00:00',
        'ubicacion' => $ubicacion,
        'montoEstimado' => null,
        'montoAprobado' => null
    ];

    $idSiniestro = $siniestroModel->crear($datos);

    if ($archivos && !empty($archivos['name'][0])) {

        foreach ($archivos['tmp_name'] as $key => $tmpName) {

            if ($archivos['error'][$key] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($archivos['error'][$key] !== UPLOAD_ERR_OK) {
                throw new Exception("Error al subir archivo.");
            }

            $tamano = $archivos['size'][$key];

            if ($tamano > 128 * 1024 * 1024) {
                throw new Exception("Archivo demasiado grande.");
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmpName);
            finfo_close($finfo);

            if (str_starts_with($mime, 'image/')) {
                $tipoDB = 'imagen';
            } elseif (str_starts_with($mime, 'video/')) {
                $tipoDB = 'video';
            } else {
                throw new Exception("Tipo de archivo no permitido.");
            }

            $contenido = file_get_contents($tmpName);

            if ($contenido === false) {
                throw new Exception("No se pudo leer el archivo.");
            }

            $multimediaModel->guardar(
                $idSiniestro,
                $idUsuario,
                $archivos['name'][$key],
                $mime,
                $tipoDB,
                $contenido
            );
        }
    }

    $conexion->commit();

    $_SESSION['siniestro_creado'] =
        "Siniestro registrado correctamente.";

    header("Location: DetalleSiniestroController.php");
    exit();

} catch (Throwable $e) {

    if (isset($conexion) && $conexion instanceof mysqli) {
        try {
            if ($conexion->errno === 0) {
                $conexion->rollback();
            }
        } catch (Throwable $errorRollback) {
            error_log($errorRollback->getMessage());
        }
    }

    error_log("Error SiniestroController: " . $e->getMessage());

    $_SESSION['errores_siniestro'] = [
        "No fue posible procesar la solicitud. Verifica los datos e inténtalo nuevamente."
    ];

    header("Location: ../controllers/SiniestroController.php");
    exit();
}
