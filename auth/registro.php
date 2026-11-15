
<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../app/controllers/RegistroController.php';

try {

    $controller = new RegistroController($conexion);

    $controller->registrar();

} catch (Throwable $e) {

    error_log("Error en registro: " . $e->getMessage());

    header(
        "Location: " . BASE_URL .
        "app/view/Registro.php?error=1"
    );

    exit();

}

?>
