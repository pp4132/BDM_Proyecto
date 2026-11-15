<?php
include("conexion.php");

$idSiniestro = $_GET['id'];

$opcion = 2;
$idComentario = 0;
$idUsuario = 0;
$comentario = "";
$idPadre = 0;

$stmt = $conexion->prepare("CALL sp_comentarios(?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiissi", $opcion, $idComentario, $idSiniestro, $idUsuario, $comentario, $idPadre);

$stmt->execute();

$res = $stmt->get_result();

while ($c = $res->fetch_assoc()):
?>

<div class="border rounded p-2 mb-2">
    <strong><?= htmlspecialchars($c['Nombre'] . ' ' . $c['Apellidos']) ?></strong><br>

    <?= htmlspecialchars($c['Comentario']) ?><br>

    <small class="text-muted">
        <?= $c['Fecha'] ?>
    </small>
</div>

<?php endwhile; ?>