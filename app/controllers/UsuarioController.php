
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../model/Usuario.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/InicioSesion.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../view/panel.php");
    exit();
}

try {

    $idUsuario = (int) $_SESSION['usuario_id'];

    $modelo = new Usuario($conexion);

    // Obtener datos actuales
    $actuales = $modelo->obtenerDatosActuales($idUsuario);

    if (!$actuales) {
        throw new Exception("Usuario no encontrado.");
    }

    // Conservar información existente
    $datos = $actuales;

    $datos['Nombre'] = trim($_POST['nombre'] ?? '');
    $datos['Apellidos'] = trim($_POST['apellidos'] ?? '');
    $datos['Telefono'] = trim($_POST['telefono'] ?? '');

    if ($datos['Nombre'] === '' || $datos['Apellidos'] === '') {
        throw new Exception("Nombre y apellidos son obligatorios.");
    }

    // Conservar foto anterior si no se selecciona otra
    if (
        isset($_FILES['foto']) &&
        $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir la fotografía.");
        }

        // Limitar tamaño a 5 MB
        if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
            throw new Exception("La fotografía supera los 5 MB.");
        }

        $info = getimagesize($_FILES['foto']['tmp_name']);

        if ($info === false) {
            throw new Exception("El archivo seleccionado no es una imagen válida.");
        }

        $datos['Foto'] = file_get_contents(
            $_FILES['foto']['tmp_name']
        );
    }

    // Actualizar mediante el modelo
    $modelo->actualizar($idUsuario, $datos);

    $_SESSION['perfil_actualizado'] = true;

    header("Location: ../view/panel.php");
    exit();

} catch (Throwable $e) {

    error_log("Error al actualizar usuario: " . $e->getMessage());

    $_SESSION['error_perfil'] = "No fue posible actualizar el perfil.";

    header("Location: ../view/panel.php");
    exit();
}
