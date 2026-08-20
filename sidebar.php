  <br><br>
  <div class="user-info">
    <?php if (!empty($usuario['Foto'])): ?>
    <img src="data:image/jpeg;base64,<?= base64_encode($usuario['Foto']); ?>" alt="Foto">
    <?php else: ?>
    <img src="Img/Perfil_Icono.png" alt="Usuario">
    <?php endif; ?>
    <h6><?php echo htmlspecialchars($usuario['Nombre']); ?> <?php echo htmlspecialchars($usuario['Apellidos']); ?></h6>
    <small><?= htmlspecialchars($usuario['Rol']) ?></small>
  </div>

  <a href="PanelUsuario.php">Panel principal</a>
  <a href="Siniestros.php">Mis Siniestros</a>
  <a href="Busqueda.php">Buscar</a>
  <?php if ($usuario['Rol'] !== 'Asegurado'): ?>
  <a href="CrearSiniestros.php" class="active">
    <i class="bi bi-plus-circle"></i> Crear Siniestro
  </a>
  <?php endif; ?>