
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================================
// 1. VALIDAR SESIÓN
// =========================================

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/InicioSesion.html");
    exit();
}

// =========================================
// 2. INCLUIR MODELOS
// =========================================

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../model/Multimedia.php';
require_once __DIR__ . '/../../model/Siniestro.php';
require_once __DIR__ . '/../../model/Usuario.php';

$idUsuario = (int)$_SESSION['usuario_id'];

$multimediaModel = new Multimedia($conexion);
$siniestroModel = new Siniestro($conexion);
$usuarioModel = new Usuario($conexion);

// Obtener usuario real
$usuario = $usuarioModel->obtenerPorId($idUsuario);

if (!$usuario) {
    http_response_code(403);
    exit("Usuario no válido.");
}

$rol = $usuario['Rol'];

$metodo = $_SERVER['REQUEST_METHOD'];

// =========================================
// 3. DESCARGAR ARCHIVO
// =========================================

if (
    $metodo === 'GET' &&
    (int)($_GET['opcion'] ?? 0) === 4
) {

    try {

        $idArchivo = filter_input(
            INPUT_GET,
            'idArchivo',
            FILTER_VALIDATE_INT
        );

        $idSiniestro = filter_input(
            INPUT_GET,
            'idSiniestro',
            FILTER_VALIDATE_INT
        );

        if (
            !$idArchivo ||
            !$idSiniestro ||
            $idArchivo <= 0 ||
            $idSiniestro <= 0
        ) {
            throw new Exception(
                "Identificadores inválidos."
            );
        }

        // Validar roles permitidos
        if (!in_array(
            $rol,
            ['Supervisor', 'Ajustador', 'Asegurado'],
            true
        )) {
            throw new Exception(
                "No tienes permisos para descargar archivos."
            );
        }

        // Verificar acceso al siniestro
        $siniestro = $siniestroModel->obtenerDetalle(
            $idSiniestro,
            $rol,
            $idUsuario
        );

        if (!$siniestro) {
            throw new Exception(
                "No tienes acceso a este siniestro."
            );
        }

        // Obtener archivo desde la base de datos
        $archivo = $multimediaModel->obtenerArchivoPorSiniestro(
            $idArchivo,
            $idSiniestro
        );

        if (!$archivo) {
            throw new Exception(
                "El archivo no existe o no pertenece a este siniestro."
            );
        }

        if (!is_string($archivo['Archivo'])) {
            throw new Exception(
                "El contenido del archivo no está disponible."
            );
        }

        // Nombre seguro
        $nombre = basename(
            str_replace('\\', '/', $archivo['NombreArchivo'])
        );

        if ($nombre === '' || $nombre === '.' || $nombre === '..') {
            $nombre = 'archivo_' . $idArchivo;
        }

        // Validar MIME
        $mime = $archivo['TipoMime'];

        $permitidos = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'application/pdf',
            'video/mp4'
        ];

        if (!in_array($mime, $permitidos, true)) {
            $mime = 'application/octet-stream';
        }

        $contenido = $archivo['Archivo'];

        // Evitar que contenido previo contamine la descarga
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Preparar descarga
        header('Content-Type: ' . $mime);

        header(
            'Content-Disposition: attachment; filename="' .
            preg_replace('/[^A-Za-z0-9._-]/', '_', $nombre) .
            '"; filename*=UTF-8\'\'' .
            rawurlencode($nombre)
        );

        header(
            'Content-Length: ' . strlen($contenido)
        );

        header('X-Content-Type-Options: nosniff');

        header('Cache-Control: private, no-store');

        // Enviar contenido
        echo $contenido;

        $conexion->close();

        exit();

    } catch (Throwable $e) {

        error_log(
            "Error descarga multimedia: " .
            $e->getMessage()
        );

        http_response_code(404);

        exit(
            htmlspecialchars(
                $e->getMessage(),
                ENT_QUOTES,
                'UTF-8'
            )
        );

    }

}

// =========================================
// 4. VALIDAR OPERACIONES POST
// =========================================

$idSiniestro = (int)($_POST['idSiniestro'] ?? 0);

$opcion = (int)($_POST['opcion'] ?? 0);

try {

    if ($metodo !== 'POST') {
        throw new Exception(
            "Método de solicitud no permitido."
        );
    }

    // Solo Supervisor y Ajustador pueden gestionar
    if (!in_array(
        $rol,
        ['Supervisor', 'Ajustador'],
        true
    )) {
        throw new Exception(
            "No tienes permisos para gestionar multimedia."
        );
    }

    if ($idSiniestro <= 0) {
        throw new Exception(
            "Siniestro inválido."
        );
    }

    // Verificar acceso al siniestro
    $siniestro = $siniestroModel->obtenerDetalle(
        $idSiniestro,
        $rol,
        $idUsuario
    );

    if (!$siniestro) {
        throw new Exception(
            "No tienes acceso a este siniestro."
        );
    }

    // =====================================
    // 5. SUBIR ARCHIVOS
    // =====================================

    if ($opcion === 1) {

        if (
            !isset($_FILES['archivos']) ||
            empty($_FILES['archivos']['name'][0])
        ) {
            throw new Exception(
                "Selecciona al menos un archivo."
            );
        }

        $permitidos = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'application/pdf',
            'video/mp4'
        ];

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        foreach (
            $_FILES['archivos']['tmp_name'] as $i => $temporal
        ) {

            if (
                $_FILES['archivos']['error'][$i] !== UPLOAD_ERR_OK
            ) {
                throw new Exception(
                    "Error al recibir uno de los archivos."
                );
            }

            if (!is_uploaded_file($temporal)) {
                throw new Exception(
                    "Archivo no válido."
                );
            }

            if (
                $_FILES['archivos']['size'][$i] >
                100 * 1024 * 1024
            ) {
                throw new Exception(
                    "Cada archivo debe pesar máximo 100 MB."
                );
            }

            $mime = $finfo->file($temporal);

            if (!in_array($mime, $permitidos, true)) {
                throw new Exception(
                    "Tipo de archivo no permitido: " . $mime
                );
            }

            $nombre = basename(
                str_replace(
                    '\\',
                    '/',
                    $_FILES['archivos']['name'][$i]
                )
            );

            $contenido = file_get_contents($temporal);

            if ($contenido === false) {
                throw new Exception(
                    "No se pudo leer el archivo."
                );
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
                throw new Exception(
                    "No se pudo guardar el archivo."
                );
            }

        }

        $_SESSION['multimedia_mensaje'] =
            "Archivos subidos correctamente.";

    }

    // =====================================
    // 6. ELIMINAR ARCHIVOS
    // =====================================

    elseif ($opcion === 3) {

        $idArchivo = (int)($_POST['idArchivo'] ?? 0);

        if ($idArchivo <= 0) {
            throw new Exception(
                "Identificador de archivo inválido."
            );
        }

        // Verificar pertenencia al siniestro
        $archivo = $multimediaModel->obtenerArchivoPorSiniestro(
            $idArchivo,
            $idSiniestro
        );

        if (!$archivo) {
            throw new Exception(
                "El archivo no pertenece a este siniestro."
            );
        }

        $multimediaModel->eliminar($idArchivo);

        $_SESSION['multimedia_mensaje'] =
            "Archivo eliminado correctamente.";

    }

    else {

        throw new Exception(
            "Operación multimedia no válida."
        );

    }

} catch (Throwable $e) {

    error_log(
        "Error MultimediaController: " .
        $e->getMessage()
    );

    $_SESSION['multimedia_error'] = $e->getMessage();

}

// =========================================
// 7. REGRESAR AL SINIESTRO
// =========================================

$conexion->close();

header(
    "Location: ../controllers/DetalleSiniestroController.php?id=" .
    $idSiniestro
);

exit();

?>
