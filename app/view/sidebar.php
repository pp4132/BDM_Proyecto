<?php
require_once __DIR__ . '/../../config/config.php';
?>

  <br><br>
  <div class="user-info">
    <?php if (!empty($_SESSION['Foto'])): ?>
    <img src="data:image/jpeg;base64,<?= base64_encode($_SESSION['Foto']); ?>" alt="Foto">
    <?php else: ?>
    <img src="../assets/Img/Perfil_Icono.png" alt="Usuario">
    <?php endif; ?>
    <h6><?php echo htmlspecialchars($_SESSION['nombre']); ?> <?php echo htmlspecialchars($usuario['Apellidos']); ?></h6>
    <small><?= htmlspecialchars($_SESSION['Rol']) ?></small>
  </div>

  <a href="/Proyecto BDM/app/view/panel.php">Panel principal</a>
  
  <a href="/Proyecto BDM/app/controllers/BusquedaController.php">Buscar</a>
  <?php if ($_SESSION['Rol'] !== 'Asegurado'): ?>
    <!--<a href="../controllers/DetalleSiniestoController.php">Mis Siniestros</a>-->
    <a href="/Proyecto BDM/app/controllers/SiniestroController.php" class="active">
      <i class="bi bi-plus-circle"></i> Crear Siniestro
    </a>
  <?php endif; ?>