<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/conexion.php';

require_once __DIR__ . '/../controllers/PanelController.php';

$controller = new PanelController($conexion);

$datos = $controller->obtenerPanel();

// Pasar datos a la vista
extract($datos);

require __DIR__ . '/PanelUsuario.php';

?>