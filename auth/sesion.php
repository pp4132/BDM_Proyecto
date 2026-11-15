
<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

// Comprobar sesión
if (!isset($_SESSION['usuario_id'])) {

    header(
        "Location: " . BASE_URL .
        "app/view/Index.html"
    );

    exit();

}

$controladorAuth = new AuthController($conexion);

try {

    $usuario = $controladorAuth->obtenerUsuarioSesion();

    if (!$usuario) {

        $_SESSION = [];

        session_destroy();

        header(
            "Location: " . BASE_URL .
            "app/view/Index.html"
        );

        exit();

    }

} catch (Throwable $e) {

    error_log("Error al validar sesión: " . $e->getMessage());

    $_SESSION = [];

    session_destroy();

    header(
        "Location: " . BASE_URL .
        "app/view/Index.html"
    );

    exit();

}

?>
