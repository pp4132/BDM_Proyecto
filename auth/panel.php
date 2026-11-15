<?php

session_start();

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '../controller/PanelController.php';

try {

    $controller = new PanelController($conexion);

    $controller->obtenerPanel();

} catch (Throwable $e) {

    error_log("Error en PanelUsuario: " . $e->getMessage());

    http_response_code(500);

    echo "Ocurrió un error al cargar el panel de usuario.";

} finally {

    $conexion->close();

}

?>