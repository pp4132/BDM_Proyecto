<?php

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../auth/sesion.php';
require_once __DIR__ . '/../../model/Siniestro.php';

if (!isset($usuario['Rol']) ||
    !in_array($usuario['Rol'], ['Supervisor', 'Ajustador'], true)) {

    header("Location: ../view/panel.php");
    exit();
}

$siniestroModel = new Siniestro($conexion);

$query = trim($_GET['query'] ?? '');

$resultados = [];

if ($query !== '') {
    $resultados = $siniestroModel->buscarGlobal($query);
}

require dirname(__DIR__) . '../view/Busqueda.php';