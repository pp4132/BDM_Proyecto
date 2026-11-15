
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/InicioSesion.html");
    exit();
}

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../model/Multimedia.php';
require_once __DIR__ . '/../../model/Siniestro.php';

$idUsuario = (int)$_SESSION['usuario_id'];
$rol = $_SESSION['Rol'] ?? '';

$idSiniestro = (int)($_POST['idSiniestro'] ?? 0);
$opcion = (int)($_POST['opcion'] ?? 0);

try {

    if (!in_array($rol, ['Supervisor', 'Ajustador'], true)) {
        throw new Exception("No tienes permisos para gestionar multimedia.");
    }

    if ($idSiniestro <= 0) {
        throw new Exception("Siniestro inválido.");
    }

    $siniestroModel = new Siniestro($conexion);

    $siniestro = $siniestroModel->obtenerDetalle(
        $idSiniestro,
        $rol,
        $idUsuario
    );

    if (!$siniestro) {
        throw new Exception("No tienes acceso a este siniestro.");
    }

    $multimediaModel = new Multimedia($conexion);

    // SUBIR ARCHIVOS
    if ($opcion === 1) {

        if (
            empty($_FILES['archivos']['name'][0]) ||
            !isset($_FILES['archivos'])
        ) {
            throw new Exception("Selecciona al menos un archivo.");
        }

        $permitidos = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'application/pdf',
            'video/mp4'
        ];

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        foreach ($_FILES['archivos']['tmp_name'] as $i => $temporal) {

            if ($_FILES['archivos']['error'][$i] !== UPLOAD_ERR_OK) {
                throw new Exception("Error al recibir uno de los archivos.");
            }

            if (!is_uploaded_file($temporal)) {
                throw new Exception("Archivo no válido.");
            }

            if ($_FILES['archivos']['size'][$i] > 10 * 1024 * 1024) {
                throw new Exception("Cada archivo debe pesar máximo 10 MB.");
            }

            $mime = $finfo->file($temporal);

            if (!in_array($mime, $permitidos, true)) {
                throw new Exception("Tipo de archivo no permitido: " . $mime);
            }

            $nombre = basename($_FILES['archivos']['name'][$i]);

            $contenido = file_get_contents($temporal);

            if ($contenido === false) {
                throw new Exception("No se pudo leer el archivo.");
            }

            if (str_starts_with($mime, 'image/')) {
                $tipo = 'Imagen';
            } elseif (str_starts_with($mime, 'video/')) {
                $tipo = 'Video';
            } else {
                $tipo = 'Documento';
            }

            $resultado = $multimediaModel->guardar(
                $idSiniestro,
                $idUsuario,
                $nombre,
                $mime,
                $tipo,
                $contenido
            );

            if (!$resultado) {
                throw new Exception("No se pudo guardar el archivo.");
            }
        }

        $_SESSION['multimedia_mensaje'] =
            "Archivos subidos correctamente.";

    }

    // ELIMINAR ARCHIVO
    elseif ($opcion === 3) {

        $idArchivo = (int)($_POST['idArchivo'] ?? 0);

        if ($idArchivo <= 0) {
            throw new Exception("Identificador de archivo inválido.");
        }

        // Verificar que el archivo pertenezca al siniestro
        $archivos = $multimediaModel->listarPorSiniestro($idSiniestro);

        $pertenece = false;

        foreach ($archivos as $archivo) {

            if ((int)$archivo['IdArchivo'] === $idArchivo) {
                $pertenece = true;
                break;
            }
        }

        if (!$pertenece) {
            throw new Exception("El archivo no pertenece a este siniestro.");
        }

        $multimediaModel->eliminar($idArchivo);

        $_SESSION['multimedia_mensaje'] =
            "Archivo eliminado correctamente.";

    }

    else {

        throw new Exception("Operación multimedia no válida.");

    }

} catch (Throwable $e) {

    $_SESSION['multimedia_error'] = $e->getMessage();

}

$conexion->close();

header("Location: /DetalleSiniestroController.php?id=" . $idSiniestro);
exit();

?>
