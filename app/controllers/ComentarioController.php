
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../model/Usuario.php';
require_once __DIR__ . '/../../model/Siniestro.php';
require_once __DIR__ . '/../../model/Comentario.php';

header('Content-Type: application/json; charset=utf-8');

function respuestaJSON($success, $mensaje, $datos = [])
{
    echo json_encode([
        'success' => $success,
        'mensaje' => $mensaje,
        'datos' => $datos
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {

    // =====================================
    // VALIDAR SESIÓN
    // =====================================

    if (!isset($_SESSION['usuario_id'])) {
        http_response_code(401);
        respuestaJSON(false, 'Debes iniciar sesión.');
    }

    $idUsuario = (int)$_SESSION['usuario_id'];

    // =====================================
    // VALIDAR USUARIO
    // =====================================

    $usuarioModel = new Usuario($conexion);

    $usuario = $usuarioModel->obtenerPorId(
        $idUsuario
    );

    if (!$usuario) {
        http_response_code(401);
        respuestaJSON(false, 'Usuario no válido.');
    }

    $rol = $usuario['Rol'];

    if (!in_array(
        $rol,
        ['Supervisor', 'Ajustador', 'Asegurado'],
        true
    )) {
        http_response_code(403);
        respuestaJSON(false, 'No tienes permisos.');
    }

    // =====================================
    // VALIDAR SINIESTRO
    // =====================================

    $idSiniestro = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$idSiniestro || $idSiniestro <= 0) {
        http_response_code(400);
        respuestaJSON(false, 'Siniestro inválido.');
    }

    $siniestroModel = new Siniestro($conexion);

    $siniestro = $siniestroModel->obtenerDetalle(
        $idSiniestro,
        $rol,
        $idUsuario
    );

    if (!$siniestro) {
        http_response_code(403);
        respuestaJSON(
            false,
            'No tienes acceso a este siniestro.'
        );
    }

    $comentarioModel = new Comentario($conexion);

    // =====================================
    // TOKEN CSRF
    // =====================================

    if (empty($_SESSION['comentario_csrf'])) {

        $_SESSION['comentario_csrf'] = bin2hex(
            random_bytes(32)
        );

    }

    // =====================================
    // REGISTRAR COMENTARIO
    // =====================================

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $token = $_POST['comentario_csrf'] ?? '';

        if (
            !hash_equals(
                $_SESSION['comentario_csrf'],
                $token
            )
        ) {
            http_response_code(403);

            respuestaJSON(
                false,
                'Token de seguridad inválido.'
            );
        }

        $texto = trim($_POST['comentario'] ?? '');

        if ($texto === '') {
            http_response_code(400);
            respuestaJSON(false, 'Escribe un comentario.');
        }

        if (mb_strlen($texto) > 10000) {
            http_response_code(400);
            respuestaJSON(
                false,
                'El comentario supera el límite permitido.'
            );
        }

        // Validar comentario padre
        $idComentarioPadre = null;

        if (
            isset($_POST['idComentarioPadre']) &&
            $_POST['idComentarioPadre'] !== ''
        ) {

            $idComentarioPadre = filter_var(
                $_POST['idComentarioPadre'],
                FILTER_VALIDATE_INT
            );

            if (
                !$idComentarioPadre ||
                $idComentarioPadre <= 0
            ) {
                http_response_code(400);

                respuestaJSON(
                    false,
                    'La respuesta seleccionada no es válida.'
                );
            }

            if (!$comentarioModel->validarPadre(
                $idComentarioPadre,
                $idSiniestro
            )) {

                http_response_code(403);

                respuestaJSON(
                    false,
                    'El comentario original no pertenece a este siniestro.'
                );
            }
        }

        // Registrar
        $nuevoId = $comentarioModel->guardar(
            $idSiniestro,
            $idUsuario,
            $texto,
            $idComentarioPadre
        );

        // Actualizar total
        $total = $siniestroModel->contarComentarios(
            $idSiniestro
        );

        respuestaJSON(
            true,
            'Comentario registrado correctamente.',
            [
                'idComentario' => $nuevoId,
                'totalComentarios' => $total
            ]
        );
    }

    // =====================================
    // CONSULTAR COMENTARIOS
    // =====================================

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $comentarios = $comentarioModel->listarPorSiniestro(
            $idSiniestro
        );

        respuestaJSON(
            true,
            'Comentarios consultados.',
            [
                'comentarios' => $comentarios,
                'totalComentarios' => count($comentarios)
            ]
        );
    }

    http_response_code(405);

    respuestaJSON(
        false,
        'Método de solicitud no permitido.'
    );

} catch (Throwable $e) {

    error_log(
        'ComentarioController: ' . $e->getMessage()
    );

    http_response_code(500);

    respuestaJSON(
        false,
        'Ocurrió un error al procesar los comentarios.'
    );
}
?>
