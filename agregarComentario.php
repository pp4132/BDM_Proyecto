<?php
include("sesion.php");

$idUsuario = $usuario['IdUsuario'];
$idSiniestro = $_POST['idSiniestro'];
$comentario = trim($_POST['comentario']);

if (empty($comentario)) {
    die("Comentario vacío");
}

$opcion = 1;
$idComentario = 0;
$idPadre = null;

$stmt = $conexion->prepare("CALL sp_comentarios(?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "iiissi",
    $opcion,
    $idComentario,
    $idSiniestro,
    $idUsuario,
    $comentario,
    $idPadre
);

$stmt->execute();
$stmt->close();

header("Location: Siniestros.php?id=" . $idSiniestro);
exit();
?>