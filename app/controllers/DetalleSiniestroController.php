
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/conexion.php';

require_once __DIR__ . '/../../model/Usuario.php';
require_once __DIR__ . '/../../model/Siniestro.php';
require_once __DIR__ . '/../../model/Multimedia.php';
require_once __DIR__ . '/../../model/Reparacion.php';
require_once __DIR__ . '/../../model/Comentario.php';


try {

    // =========================================
    // 1. VALIDAR SESIÓN
    // =========================================

    if (!isset($_SESSION['usuario_id'])) {

        header("Location: ../view/InicioSesion.html");
        exit();

    }

    $idUsuario = (int) $_SESSION['usuario_id'];

    // =========================================
    // 2. OBTENER USUARIO
    // =========================================

    $usuarioModel = new Usuario($conexion);

    $usuario = $usuarioModel->obtenerPorId($idUsuario);

    if (!$usuario) {

        $_SESSION = [];

        session_destroy();

        header("Location: ../view/InicioSesion.html");
        exit();

    }

    // =========================================
    // 3. VALIDAR ROL
    // =========================================

    $rol = $usuario['Rol'];

    if (!in_array(
        $rol,
        ['Supervisor', 'Ajustador', 'Asegurado'],
        true
    )) {

        header("Location: ../view/panel.php");
        exit();

    }

    $_SESSION['Rol'] = $rol;

    // =========================================
    // 4. VALIDAR ID DEL SINIESTRO
    // =========================================

    $idSiniestro = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$idSiniestro || $idSiniestro <= 0) {

        header("Location: ../view/panel.php");
        exit();

    }

    // =========================================
    // 5. INICIALIZAR MODELOS
    // =========================================

    $siniestroModel = new Siniestro($conexion);

    $multimediaModel = new Multimedia($conexion);

    $reparacionModel = new Reparacion($conexion);

    $comentarioModel = new Comentario($conexion);

    // =========================================
    // 6. OBTENER DETALLE DEL SINIESTRO
    // =========================================

    $siniestro = $siniestroModel->obtenerDetalle(
        $idSiniestro,
        $rol,
        $idUsuario
    );

    if (!$siniestro) {

        header("Location: ../view/panel.php");
        exit();

    }

    // =========================================
    // 7. GENERAR TOKEN CSRF
    // =========================================

    if (empty($_SESSION['comentario_csrf'])) {

        $_SESSION['comentario_csrf'] = bin2hex(
            random_bytes(32)
        );

    }

    // =========================================
    // 8. REGISTRAR COMENTARIO
    // =========================================

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (
            !isset($_POST['comentario_csrf']) ||
            !hash_equals(
                $_SESSION['comentario_csrf'],
                $_POST['comentario_csrf']
            )
        ) {

            throw new Exception("Solicitud no válida.");

        }

        $texto = trim($_POST['comentario'] ?? '');

        $idComentarioPadre = null;

        if (
            isset($_POST['idComentarioPadre']) &&
            $_POST['idComentarioPadre'] !== ''
        ) {

            $idComentarioPadre = filter_var(
                $_POST['idComentarioPadre'],
                FILTER_VALIDATE_INT
            );

            if (!$idComentarioPadre || $idComentarioPadre <= 0) {

                throw new Exception("Respuesta inválida.");

            }

        }

        if ($idComentarioPadre !== null) {

            if (!$comentarioModel->validarPadre(
                $idComentarioPadre,
                $idSiniestro
            )) {
                throw new Exception("Comentario padre inválido.");
            }

        }

        $comentarioModel->guardar(
            $idSiniestro,
            $idUsuario,
            $texto,
            $idComentarioPadre
        );

        header(
            "Location: DetalleSiniestroController.php?id=" .
            $idSiniestro . "#comentarios"
        );

        exit();

    }

    // =========================================
    // 9. LISTAR MULTIMEDIA
    // =========================================

    $archivos = $multimediaModel->listarPorSiniestro(
        $idSiniestro
    );

    // =========================================
    // 10. LISTAR REPARACIONES
    // =========================================

    $reparacionesArray = $reparacionModel->listarPorSiniestro(
        $idSiniestro
    );

    // =========================================
    // 11. CONTAR COMENTARIOS
    // =========================================

    $totalComentarios = $siniestroModel->contarComentarios(
        $idSiniestro
    );

    // =========================================
    // 12. LISTAR COMENTARIOS
    // =========================================

    $comentariosArray =
        $comentarioModel->listarPorSiniestro(
            $idSiniestro
        );

    // =========================================
    // 13. CARGAR VISTA
    // =========================================

    require __DIR__ . '/../view/Siniestros.php';

} catch (Throwable $e) {

    error_log(
        "Error DetalleSiniestroController: " .
        $e->getMessage() .
        " en línea " .
        $e->getLine()
    );

    http_response_code(500);

    echo "<h3>Error al cargar el siniestro</h3>";

    echo "<pre>";
    echo htmlspecialchars(
        $e->getMessage(),
        ENT_QUOTES,
        'UTF-8'
    );
    echo "</pre>";

    echo "Archivo: " . htmlspecialchars($e->getFile());

    echo "<br>Línea: " . $e->getLine();

    exit;
}

?>
