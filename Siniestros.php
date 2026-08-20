<?php
include("sesion.php");

// =========================
// VALIDAR ROL
// =========================
if (!in_array($usuario['Rol'], ['Supervisor','Ajustador', 'Asegurado'])) {

    header("Location: PanelUsuario.php");
    exit();
}

// =========================
// VALIDAR ID SINIESTRO
// =========================
$idSiniestro = $_GET['id'] ?? 0;

if (!is_numeric($idSiniestro) || $idSiniestro <= 0) {

    header("Location: PanelUsuario.php");
    exit();
}


// =========================
// TRAER SINIESTRO (SP)
// =========================
if ($usuario['Rol'] === 'Supervisor') {

    $stmt = $conexion->prepare("
        SELECT
            IdSiniestro,
            Folio,
            Estado,
            Descripcion,
            FechaReporte,
            Ubicacion,
            MontoEstimado,
            MontoAprobado,
            Tipo,
            Marca,
            Modelo,
            Placas,
            NombreAsegurado
        FROM vw_siniestros_detalle
        WHERE IdSiniestro = ?
    ");

    $stmt->bind_param("i", $idSiniestro);
}
elseif ($usuario['Rol'] === 'Ajustador') {

    $stmt = $conexion->prepare("
        SELECT
            IdSiniestro,
            Folio,
            Estado,
            Descripcion,
            FechaReporte,
            Ubicacion,
            MontoEstimado,
            MontoAprobado,
            Tipo,
            Marca,
            Modelo,
            Placas,
            NombreAsegurado
        FROM vw_siniestros_detalle
        WHERE IdSiniestro = ?
        AND IdAjustador = ?
    ");

    $stmt->bind_param("ii", $idSiniestro, $idUsuario);
}
elseif ($usuario['Rol'] === 'Asegurado') {

    $stmt = $conexion->prepare("
        SELECT
            IdSiniestro,
            Folio,
            Estado,
            Descripcion,
            FechaReporte,
            Ubicacion,
            MontoEstimado,
            MontoAprobado,
            Tipo,
            Marca,
            Modelo,
            Placas
        FROM vw_siniestros_detalle
        WHERE IdSiniestro = ?
        AND IdAsegurado = ?
    ");

    $stmt->bind_param("ii", $idSiniestro, $idUsuario);
}

$stmt->execute();

$siniestro = $stmt->get_result()->fetch_assoc();


if (!$siniestro) {

    header("Location: PanelUsuario.php");
    exit();
}

$stmt->close();

// =========================
// TRAER ARCHIVOS MULTIMEDIA
// =========================

$stmtFiles = $conexion->prepare("
    SELECT
        IdArchivo,
        NombreArchivo,
        TipoMime,
        FechaSubida
    FROM vw_multimedia_siniestro
    WHERE IdSiniestro = ?
");

$stmtFiles->bind_param("i", $idSiniestro);

$stmtFiles->execute();

$archivos = $stmtFiles->get_result();

$stmtFiles->close();


/*REPARACIONES */
$stmtRep = $conexion->prepare("
    SELECT
        IdReparacion,
        DescripcionTrabajo,
        Taller,
        FechaInicio,
        FechaFin,
        CostoFinal,
        Estado,
        FechaCreacion
    FROM vw_reparaciones_siniestro
    WHERE IdSiniestro = ?
");

$stmtRep->bind_param("i", $idSiniestro);

$stmtRep->execute();

$reparaciones = $stmtRep->get_result();

$reparacionesArray = $reparaciones->fetch_all(MYSQLI_ASSOC);

$stmtRep->close();


// =========================
// TOTAL COMENTARIOS
// =========================

$stmtComentarios = $conexion->prepare("
    SELECT fn_total_comentarios_siniestro(?) AS TotalComentarios
");

$stmtComentarios->bind_param("i", $idSiniestro);

$stmtComentarios->execute();

$resultadoComentarios = $stmtComentarios->get_result();

$totalComentarios =
    $resultadoComentarios->fetch_assoc()['TotalComentarios'];

$stmtComentarios->close();

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detalles del siniestro</title>

  <link rel="stylesheet" href="public/css/bootstrap.min.css">
  <link rel="stylesheet" href="styleInicioSesion.css">
  <link rel="stylesheet" href="stylePanel.css">

    <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<!-- NAVBAR -->
<header>
  <nav class="navbar navbar-expand-lg navbar-dark bg-corporate-dark fixed-top px-4">
    <a class="navbar-brand d-flex align-items-center" href="Index.html">
      <img src="Img/Logo.png" alt="Logo Empresa" height="100" class="me-2">
      <span>Sistema de Gestión de Siniestros</span>
    </a>
    <div class="ms-auto">
      <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
    </div>
  </nav>
</header>

<!-- SIDEBAR -->
<div class="sidebar">
  <!-- Información del usuario -->
   <?php include("sidebar.php"); ?>
</div>

<!-- CONTENIDO PRINCIPAL -->
 <br><br>
<div class="content">

<h2>Siniestro #<?= $siniestro['IdSiniestro'] ?></h2>

    <!-- INFO GENERAL -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <p><strong>Tipo:</strong> <?= htmlspecialchars($siniestro['Tipo']) ?></p>
            <p><strong>Estado:</strong> <?= htmlspecialchars($siniestro['Estado']) ?></p>
            <p><strong>Fecha Reporte:</strong> <?= $siniestro['FechaReporte'] ?></p>
            <p><strong>Ubicación:</strong> <?= htmlspecialchars($siniestro['Ubicacion']) ?></p>
            <p><strong>Descripción:</strong> <?= htmlspecialchars($siniestro['Descripcion']) ?></p>
            <p><strong>Monto Estimado:</strong> $<?= number_format($siniestro['MontoEstimado'],2) ?></p>
            <p><strong>Monto Aprobado:</strong> $<?= number_format($siniestro['MontoAprobado'],2) ?></p>
        </div>
    </div>

    <!-- COMENTARIOS -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <strong>Comentarios</strong>
        </div>

        <div class="card-body">

            <!-- LISTA EN VIVO -->
            <div id="comentariosContainer"></div>

            <div class="text-end text-muted mb-2">
                <small>
                    💬 Total de comentarios:
                    <strong><?= $totalComentarios ?></strong>
                </small>
            </div>

            <!-- FORM -->
            <form id="formComentario" method="POST" action="agregarComentario.php">
                <input type="hidden" name="idSiniestro" value="<?= $siniestro['IdSiniestro'] ?>">

                <textarea name="comentario"
                        class="form-control mb-2"
                        placeholder="Escribe un comentario..."
                        required></textarea>

                <button class="btn btn-primary btn-sm">
                    Enviar
                </button>
            </form>

        </div>
    </div>

    
    <!--Actualizar siniestros-->
    <?php if (in_array($usuario['Rol'], ['Supervisor', 'Ajustador'])): ?>
    <?php include("modal/Actualizar_siniestros.php"); ?>
     <?php endif; ?>

    <!--REPARACIONES DEL SINIESTRO-->
    <?php include("modal/Actualizar_reparaciones.php"); ?>

    <!--CREAR REPARACIONES-->
    <?php if (in_array($usuario['Rol'], ['Supervisor', 'Ajustador'])): ?>
    <!--Agregar la reparacion-->
    <?php if (in_array($usuario['Rol'], ['Supervisor', 'Ajustador'])): ?>
    <div class="card mb-4 shadow-sm">
        <div class="card-header"><strong>Agregar reparación</strong></div>

        <div class="card-body">

           <form action="crearReparacion.php" method="POST" class="mt-2" onsubmit="return confirm('¿Crear reparación?')">

                <input type="hidden" name="idSiniestro" value="<?= $siniestro['IdSiniestro'] ?>">

                <input type="text" name="taller" class="form-control mb-2" placeholder="Taller" required>

                <textarea name="descripcion" class="form-control mb-2" placeholder="Trabajo realizado" required></textarea>

                <input type="number" name="costo" class="form-control mb-2" placeholder="Costo" min="0" step="0.01" required>

                <button class="btn btn-success">
                    Crear reparación
                </button>

            </form>

        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>


    <!-- SUBIR MULTIMEDIA -->
     <?php if (in_array($usuario['Rol'], ['Supervisor', 'Ajustador'])): ?>
    <div class="card mb-4 shadow-sm">
       <div class="card-header">
            <strong>Subir multimedia</strong>
        </div>
        <div class="card-body">
            <form action="procesar_multimedia.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="opcion" value="1">
                <input type="hidden" name="idSiniestro" value="<?= $siniestro['IdSiniestro'] ?>">
                <input type="file" name="archivos[]" multiple required class="form-control mb-2">
                <button type="submit" class="btn btn-corporate">Subir Archivos</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- LISTAR MULTIMEDIA -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <strong>Eliminar multimedia</strong>
        </div>
        <div class="card-body">
            <ul class="list-group">
            <?php while($file = $archivos->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($file['NombreArchivo']) ?> (<?= htmlspecialchars($file['TipoMime']) ?>)
                    <form action="procesar_multimedia.php" method="POST" style="display:inline">
                        <input type="hidden" name="opcion" value="3">
                        <input type="hidden" name="idArchivo" value="<?= $file['IdArchivo'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar archivo?')">Eliminar</button>
                    </form>
                </li>
            <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <a href="PanelUsuario.php" class="btn btn-outline-dark mb-4">Volver al Panel</a>
</div>

<script src="public/js/bootstrap.bundle.min.js"></script>
<script>
    function cargarComentarios() {

        fetch("comentario_listado.php?id=<?= $siniestro['IdSiniestro'] ?>")
            .then(res => res.text())
            .then(html => {
                document.getElementById("comentariosContainer").innerHTML = html;
            });

    }

    // carga inicial
    cargarComentarios();

    // refresco cada 4 segundos
    setInterval(cargarComentarios, 4000);
</script>

</body>
</html>