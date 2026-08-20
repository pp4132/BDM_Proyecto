<?php
include("sesion.php");

if ($usuario['Rol'] !== 'Supervisor' && $usuario['Rol'] !== 'Ajustador') {
    header("Location: PanelUsuario.php");
    exit();
}

$stmtVehiculos = $conexion->prepare("
    SELECT
        IdVehiculo,
        Marca,
        Modelo,
        Placas
    FROM vw_vehiculos_panel
");

$stmtVehiculos->execute();

$vehiculos = $stmtVehiculos->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Crear Siniestro</title>

  <link rel="stylesheet" href="public/css/bootstrap.min.css">
  <link rel="stylesheet" href="stylePanel.css">
  <link rel="stylesheet" href="styleInicioSesion.css">

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
  <?php include("sidebar.php"); ?>
</div>

<!-- CONTENIDO -->
<div class="content">
    
    <!--<h2 class="mb-4"><br><br>Crear Nuevo Siniestro</h2>-->
  <h2 class="mt-5">Crear Nuevo Siniestro</h2>

  <div class="card shadow-sm p-4">

    <form id="formSiniestro" method="POST" action="guardar_siniestro.php" enctype="multipart/form-data">

      <!-- Tipo -->
      <div class="mb-3">
        <label class="form-label">Tipo de siniestro</label>
        <select class="form-select" name="tipo" required>
          <option value="">Seleccionar...</option>
          <option>Daño material</option>
          <option>Robo</option>
          <option>Choque</option>
          <option>Fallo de frenos</option>
        </select>
        <!--<input type="text" class="form-control" name="tipo" placeholder="Ej. Choque">-->
      </div>

        <!-- Vehículo -->
        <div class="mb-3">
        <label class="form-label">Vehículo</label>
        <select class="form-select" name="IdVehiculo" required>
            <option value="">Seleccionar...</option>
            <?php $vehiculosArray = $vehiculos->fetch_all(MYSQLI_ASSOC); 
            foreach($vehiculosArray as $v):?>
              <option value="<?= $v['IdVehiculo'] ?>">
                 <?= $v['Marca'] . " " . $v['Modelo'] . " - " . $v['Placas'] ?>
              </option>
            <?php endforeach; ?>
        </select>
        </div>

        <!-- Descripción -->
        <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea class="form-control" name="descripcion" rows="4" required></textarea>
        </div>

      <!-- Fecha -->
      <div class="mb-3">
        <label class="form-label">Fecha del siniestro</label>
        <input type="date" class="form-control" name="fecha" required>
      </div>

      <!-- Ubicación -->
      <div class="mb-3">
        <label class="form-label">Ubicación</label>
        <input type="text" class="form-control" name="ubicacion" placeholder="Ej. Monterrey, NL">
      </div>

      <!-- Archivos -->
      <div class="mb-3">
        <label class="form-label">Evidencias (imágenes o videos)</label>
        <input type="file" class="form-control" id="archivos" name="archivos[]" multiple accept="image/*,video/*">
      </div>

      <!-- Preview -->
      <div id="preview" class="mb-3 d-flex flex-wrap gap-2"></div>

      <!-- Botones -->
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-corporate">
          <i class="bi bi-check-circle"></i> Crear Siniestro
        </button>

        <a href="Siniestros.html" class="btn btn-secondary">
          Cancelar
        </a>
      </div>

    </form>

  </div>
</div>

<!-- FOOTER -->
<footer class="text-center">
  © 2026 Departamento de Siniestros
</footer>

<script src="public/js/bootstrap.bundle.min.js"></script>

<!-- SCRIPT -->
<script>
  const inputArchivos = document.getElementById("archivos");
  const preview = document.getElementById("preview");

  inputArchivos.addEventListener("change", () => {
    preview.innerHTML = "";

    [...inputArchivos.files].forEach(file => {
      const tipo = file.type;

      if (tipo.startsWith("image/")) {
        const img = document.createElement("img");
        img.src = URL.createObjectURL(file);
        img.style.width = "100px";
        img.style.height = "100px";
        img.style.objectFit = "cover";
        img.classList.add("rounded");
        preview.appendChild(img);
      }

      if (tipo.startsWith("video/")) {
        const video = document.createElement("video");
        video.src = URL.createObjectURL(file);
        video.width = 120;
        video.controls = true;
        preview.appendChild(video);
      }

      if (inputArchivos.files.length > 5) {
          alert("Máximo 5 archivos");
          inputArchivos.value = "";
          return;
      }
    
    });
  });


</script>

</body>
</html>