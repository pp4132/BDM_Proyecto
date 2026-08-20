<?php
session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header("Location: Index.html");
        exit();
    }

    include("conexion.php");

    $idUsuario = $_SESSION['usuario_id'];

   $pendientes = 0;
    $rechazados = 0;
    $proceso = 0;
    $cerrados = 0;

    if ($usuario['Rol'] === 'Supervisor') {

    $stmt = $conexion->prepare("
        SELECT Estado, COUNT(*) AS total
        FROM vw_siniestros_detalle
        GROUP BY Estado
    ");

    } elseif ($usuario['Rol'] === 'Ajustador') {

        $stmt = $conexion->prepare("
            SELECT Estado, COUNT(*) AS total
            FROM vw_siniestros_detalle
            WHERE IdAjustador = ?
            GROUP BY Estado
        ");
        $stmt->bind_param("i", $_SESSION['usuario_id']);

    } else {

        $stmt = $conexion->prepare("
            SELECT Estado, COUNT(*) AS total
            FROM vw_siniestros_detalle
            WHERE IdAsegurado = ?
            GROUP BY Estado
        ");
        $stmt->bind_param("i", $_SESSION['usuario_id']);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {

        $estado = (int)$row['Estado'];

        switch ($estado) {

            case 0:
                $pendientes = $row['total'];
                break;

            case 1:
                $rechazados = $row['total'];
                break;

            case 6:
                $cerrados = $row['total'];
                break;

            default:
                // 2,3,4,5 = proceso
                $proceso += $row['total'];
                break;
        }
    }

    $stmt->close();
?>