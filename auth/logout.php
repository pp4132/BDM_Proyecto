
<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

try {

    $controlador = new AuthController($conexion);

    $controlador->logout();

} catch (Throwable $e) {

    error_log("Error al cerrar sesión: " . $e->getMessage());

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );

    }

    session_destroy();

}

header(
    "Location: ../app/view/Index.html"
);

exit();

?>
