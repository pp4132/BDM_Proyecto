<?php
session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header("Location: Index.html");
        exit();
    }

    include("conexion.php");

    $idUsuario = $_SESSION['usuario_id'];

    $stmt = $conexion->prepare("
        SELECT
            IdUsuario,
            Nombre,
            Apellidos,
            Email,
            Telefono,
            Rol
        FROM vw_usuarios_sesion
        WHERE IdUsuario = ?
    ");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();

    $usuario = $stmt->get_result()->fetch_assoc();
    $idUsuario = $_SESSION['usuario_id'];

    $conexion->query("SET @idUsuario = " . (int)$usuario['IdUsuario']);

    if (!$usuario) {
        session_destroy();
        header("Location: Index.html");
        exit();
    }

    function validarRol($rolesPermitidos, $usuario) {

        if (!in_array($usuario['Rol'], $rolesPermitidos)) {

            header("Location: PanelUsuario.php");
            exit();
        }
    }
?>