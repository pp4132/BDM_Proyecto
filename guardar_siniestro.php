<?php
session_start();
include("conexion.php");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* =========================
   VALIDAR SESIÓN
========================= */

if (!isset($_SESSION['usuario_id'])) {
    header("Location: PanelUsuario.php");
    exit();
}

$idUsuario = $_SESSION['usuario_id'];

/* =========================
   DATOS FORM
========================= */

$fecha = $_POST['fecha'] ?? null;
$idVehiculo = $_POST['IdVehiculo'] ?? null;
$descripcion = $_POST['descripcion'] ?? '';
$ubicacion = $_POST['ubicacion'] ?? '';
$tipo = $_POST['tipo'] ?? '';

$errores = [];

/* =========================
   VALIDACIONES
========================= */

if (empty($idVehiculo)) $errores[] = "Vehículo requerido";
if (empty($descripcion)) $errores[] = "Descripción requerida";
if (empty($tipo)) $errores[] = "Tipo requerido";
if (empty($fecha)) $errores[] = "Fecha requerida";

/* =========================
   VALIDAR VEHÍCULO
========================= */

$stmtCheck = $conexion->prepare("
    SELECT IdVehiculo
    FROM vw_vehiculos_usuario
    WHERE IdVehiculo = ?
");

$stmtCheck->bind_param("i", $idVehiculo);
$stmtCheck->execute();

if ($stmtCheck->get_result()->num_rows === 0) {
    $errores[] = "Vehículo inválido";
}

/* =========================
   SI HAY ERRORES
========================= */

if (!empty($errores)) {
    die(implode(" | ", $errores));
}

/* =========================
   SP SINIESTROS
========================= */

$opcion = 1;
$idSiniestro = 0;
$idAjustador = $idUsuario; // quien lo crea
$idnoseusa=0;
$estado = "pendiente";
$montoEstimado = null;
$montoAprobado = null;


try {

        $conexion->begin_transaction();
    // SP expects 11 params
    $stmt = $conexion->prepare("
        CALL sp_siniestros(?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $fecha = $fecha . " 00:00:00";
    // bind_param con tipos correctos:
    // i = integer, s = string, d = double/decimal
    $stmt->bind_param(
        "iiiiisssssdd",
        $opcion,
        $idSiniestro,
        $idVehiculo,
        $idAjustador,
        $idnoseusa,
        $tipo,
        $estado,
        $descripcion,
        $fecha,
        $ubicacion,
        $montoEstimado,
        $montoAprobado
    );

    /* =========================
    EJECUTAR
    ========================= */


    $stmt->execute();

    $result = $stmt->get_result();

    if (!$result) {
        throw new Exception("No se pudo obtener resultado del SP");
    }

    $row = $result->fetch_assoc();

    if (!$row) {
        throw new Exception("SP no devolvió ID del siniestro");
    }

    $result->free();
    $stmt->close();

    while ($conexion->more_results() && $conexion->next_result()) {
        $dummyResult = $conexion->store_result();
        if ($dummyResult instanceof mysqli_result) {
            $dummyResult->free();
        }
        if ($res = $conexion->store_result()) {
            $res->free();
        }
    }

    $idSiniestro = $row['id'];

    /* =========================
    ARCHIVOS (BLOB o ruta)
    ========================= */
    
    if (count($_FILES['archivos']['name']) > 5) {
    throw new Exception("Máximo 5 archivos");
    }
    
    if (!empty($_FILES['archivos']['name'][0])) {

        foreach ($_FILES['archivos']['tmp_name'] as $key => $tmp_name) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmp_name);
            finfo_close($finfo);
            $contenido = file_get_contents($tmp_name);
            $tamano = $_FILES['archivos']['size'][$key];

            if ($_FILES['archivos']['error'][$key] !== UPLOAD_ERR_OK) {
                    throw new Exception("Error al subir archivo");
            }

            if ($tamano > 25 * 1024 * 1024) {
                throw new Exception("Archivo demasiado grande");
            }

            if (str_starts_with($mime, 'image/')) {
                $tipoDB = 'imagen';
            }
            elseif (str_starts_with($mime, 'video/')) {
                $tipoDB = 'video';
            }
            else {
                throw new Exception("Tipo no permitido");
            }

            $opcionMultimedia = 1;
            $idArchivo = null;
            $idComentario = null;
            $null = NULL;

            $nombreArchivo = $_FILES['archivos']['name'][$key];

            $stmtFile = $conexion->prepare("
                CALL sp_multimedia(?,?,?,?,?,?,?,?,?)
            ");

            $stmtFile->bind_param(
                "iiiissssi",
                $opcionMultimedia,
                $idArchivo,
                $idSiniestro,
                $idUsuario,
                $tipoDB,
                $nombreArchivo,
                $mime,
                $null,
                $idComentario
            );

            $stmtFile->send_long_data(7, $contenido);
            $stmtFile->execute();

            $resultFile = $stmtFile->get_result();

            if ($resultFile instanceof mysqli_result) {
                $resultFile->free();
            }
            while ($conexion->more_results() && $conexion->next_result()) {
                $extraResult = $conexion->store_result();

                if ($extraResult instanceof mysqli_result) {
                    $extraResult->free();
                }
            }

$stmtFile->close();
        }
    }

    $conexion->commit();


} catch (Exception $e) {

    $conexion->rollback();
   die("Error: " . $e->getMessage());
}
/* =========================
   REDIRECCIÓN
========================= */

header("Location: Siniestros.php");
exit();

?>