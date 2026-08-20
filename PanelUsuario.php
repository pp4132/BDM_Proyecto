<?php
include("sesion.php");
/*Siniestros del usuario*/

if ($usuario['Rol'] === 'Supervisor') {

    $stmtSiniestros = $conexion->prepare("
        SELECT
            IdSiniestro,
            Folio,
            Estado,
            FechaReporte
        FROM vw_siniestros_panel
    ");

} elseif ($usuario['Rol'] === 'Ajustador') {

    $stmtSiniestros = $conexion->prepare("
        SELECT
            IdSiniestro,
            Folio,
            Estado,
            FechaReporte
        FROM vw_siniestros_panel
        WHERE IdAjustador = ?
    ");

    $stmtSiniestros->bind_param("i", $idUsuario);

} elseif ($usuario['Rol'] === 'Asegurado') {

    $stmtSiniestros = $conexion->prepare("
        SELECT
            IdSiniestro,
            Folio,
            Estado,
            FechaReporte
        FROM vw_siniestros_panel
        WHERE IdAsegurado = ?
    ");

    $stmtSiniestros->bind_param("i", $idUsuario);
}

$stmtSiniestros->execute();

$siniestros = $stmtSiniestros->get_result();

$siniestrosArray = $siniestros->fetch_all(MYSQLI_ASSOC);

$stmtSiniestros->close();




/*Vehiculos del asegurado*/
$stmtVehiculos = $conexion->prepare("
    SELECT
        IdVehiculo,
        Tipo,
        Marca,
        Modelo,
        Anio,
        Placas,
        NumeroPoliza
    FROM vw_vehiculos_panel
    WHERE IdAsegurado = ?
");

$stmtVehiculos->bind_param(
    "i",
    $idUsuario
);

$stmtVehiculos->execute();

$vehiculos = $stmtVehiculos->get_result();

if (!$vehiculos) {
    die($stmtVehiculos->error);
}

$vehiculosArray = $vehiculos->fetch_all(MYSQLI_ASSOC);

$stmtVehiculos->close();

include("ESTADOS.php");


/*Función de cumpleaños*/
$stmtMensaje = $conexion->prepare("
    SELECT fn_mensaje_bienvenida(?) AS Mensaje
");

$stmtMensaje->bind_param("i", $idUsuario);
$stmtMensaje->execute();

$mensaje = $stmtMensaje
    ->get_result()
    ->fetch_assoc()['Mensaje'];

?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Usuario - Sistema de Gestión de Siniestros</title>

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
  <a href="#misVehiculos">
    Mis Vehículos
  </a>
  <!--Botonoes para los vehiculos-->
  <a><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar">
  Editar perfil
  </button></a>
  <a><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalVehiculo">
    Registrar vehículo
  </button></a>
  
</div>

<!-- CONTENIDO PRINCIPAL -->
 
<div class="content">
  <h2 class="mb-4">
  Bienvenido, <?= htmlspecialchars($usuario['Nombre']) ?>
</h2>

<?php if (!empty($mensaje)): ?>
<div class="alert alert-warning">
    <?= htmlspecialchars($mensaje) ?>
</div>
<?php endif; ?>

  <!-- Resumen general -->
  <div class="row mb-4">
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <h5 class="card-title fw-bold">Siniestros Pendientes</h5>
          <p class="display-6"><?= $pendientes ?></p>
        </div>
      </div>
    </div>
   <!-- <div class="row mb-4">
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <h5 class="card-title fw-bold">Siniestros Rechazados</h5>
          <p class="display-6"><?= $rechazados  ?></p>
        </div>
      </div>
    </div>-->
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <h5 class="card-title fw-bold">Siniestros En Proceso</h5>
          <p class="display-6"><?= $proceso ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <h5 class="card-title fw-bold">Siniestros Cerrados</h5>
         <p class="display-6"><?= $cerrados ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Lista de siniestros -->
  <h3 class="mb-3">Mis Siniestros</h3>
 <!-- FILTROS -->
<div class="row mb-3">
  <div class="col-md-3">
    <select class="form-select" id="filtroOrden">
      <option value="">Ordenar por</option>
      <option value="asc">Fecha ascendente</option>
      <option value="desc">Fecha descendente</option>
    </select>
  </div>

  <div class="col-md-3">
    <select class="form-select" id="filtroEstado">
      <option value="">Estado</option>
      <option value="pendiente">Pendiente</option>
      <option value="proceso">En proceso</option>
      <option value="cerrado">Cerrado</option>
    </select>
  </div>
</div>

<!-- LISTA SINIESTROS -->
<div class="row" id="listaSiniestros">
<?php if (!empty($siniestrosArray)): ?>

    <?php foreach($siniestrosArray as $siniestro): ?>

        <div class="col-md-4 mb-3">
            <div class="card card-siniestro shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold">
                        <?= htmlspecialchars($siniestro['Folio']) ?>
                    </h5>

                    <p>
                        Estado:

                        <?php
                        $estado = strtolower($siniestro['Estado']);

                        $badge = 'bg-secondary';

                        if ($estado == 'pendiente') {
                            $badge = 'bg-warning text-dark';
                        }

                        if ($estado == 'en proceso') {
                            $badge = 'bg-primary';
                        }

                        if ($estado == 'cerrado') {
                            $badge = 'bg-success';
                        }
                        ?>

                        <span class="badge <?= $badge ?>">
                            <?= htmlspecialchars($siniestro['Estado']) ?>
                        </span>
                    </p>

                    <p class="card-text">
                        Fecha:
                        <?= date('d/m/Y', strtotime($siniestro['FechaReporte'])) ?>
                    </p>

                    <a
                        href="Siniestros.php?id=<?= $siniestro['IdSiniestro'] ?>"
                        class="btn btn-corporate btn-sm">

                        Ver Detalle

                    </a>
                </div>
            </div>
        </div>

    <?php endforeach; ?>

<?php else: ?>

    <div class="alert alert-info">
        No hay siniestros registrados.
    </div>

<?php endif; ?>

</div>

<hr>

<!--LISTAR VEHICULOS-->
<h3 id="misVehiculos" class="mb-3">
  Mis Vehículos
</h3>


<div class="row">
  <?php if (!empty($vehiculosArray)): ?>

    <?php foreach($vehiculosArray as $vehiculo): ?>

        <div class="col-md-4 mb-3">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h5 class="card-title">
                        <?= htmlspecialchars($vehiculo['Marca']) ?>
                        <?= htmlspecialchars($vehiculo['Modelo']) ?>
                    </h5>

                    <p>
                        <strong>Tipo:</strong>
                        <?= htmlspecialchars($vehiculo['Tipo']) ?>
                    </p>

                    <p>
                        <strong>Placas:</strong>
                        <?= htmlspecialchars($vehiculo['Placas']) ?>
                    </p>

                    <p>
                        <strong>Póliza:</strong>
                        <?= htmlspecialchars($vehiculo['NumeroPoliza']) ?>
                    </p>

                </div>

            </div>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <div class="alert alert-info">
        No hay vehículos registrados.
    </div>

<?php endif; ?>

<hr>



<!-- Modificar al usuario -->
<?php include("modal/modificarusuario.php"); ?>

<!-- Mostrar errores o mensajes relacionados con vehículos -->
<?php
if (!empty($_SESSION['errores_vehiculo'])) {
    foreach ($_SESSION['errores_vehiculo'] as $error) {
        echo "<div class='alert alert-danger'>$error</div>";
    }
    unset($_SESSION['errores_vehiculo']);
}

if (!empty($_SESSION['vehiculo_actualizado'])) {
    echo "<div class='alert alert-success'>Vehículo actualizado correctamente</div>";
    unset($_SESSION['vehiculo_actualizado']);
}
?>

<!-- MODAL REGISTRAR VEHÍCULO -->
<?php include("modal/registrarvehiculo.php"); ?>

<!-- MODALES EDITAR VEHÍCULO -->
<?php foreach($vehiculosArray as $vehiculo): ?>
<?php include("modal/actualizarvehiculo.php"); ?>
<?php endforeach; ?>


<!-- FOOTER -->
<footer class="text-center">
  © 2026 Departamento de Siniestros
</footer>

<script src="public/js/bootstrap.bundle.min.js"></script>
</body>
</html>